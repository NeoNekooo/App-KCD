<?php

namespace App\Http\Controllers\Admin\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Rombel;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SiswaExport;
use App\Models\PelanggaranSanksi;
use App\Models\PelanggaranNilai;

class SiswaController extends Controller
{
    /**
     * Menampilkan Daftar Siswa (Tabel)
     */
    public function index(Request $request)
    {
        $query = Siswa::with('rombel');

        // 1. FILTER KELAS (Aman dari Error JSON)
        if ($request->filled('rombel_id')) {
            $rombel = Rombel::find($request->rombel_id);
            if ($rombel) {
                // Cek apakah sudah array atau masih string JSON
                $anggotaData = is_array($rombel->anggota_rombel) 
                    ? $rombel->anggota_rombel 
                    : json_decode($rombel->anggota_rombel, true);
                
                if (!empty($anggotaData)) {
                    $anggotaPdIds = array_column($anggotaData, 'peserta_didik_id');
                    $query->whereIn('peserta_didik_id', $anggotaPdIds);
                } else {
                    $query->whereRaw('1 = 0');
                }
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        // 2. PENCARIAN
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nama', 'like', "%{$searchTerm}%")
                  ->orWhere('nisn', 'like', "%{$searchTerm}%")
                  ->orWhere('nik', 'like', "%{$searchTerm}%");
            });
        }

        // 3. PAGINASI
        $perPage = $request->input('per_page', 15);
        $siswas = ($perPage == 'all') 
            ? $query->orderBy('nama', 'asc')->paginate(9999) 
            : $query->orderBy('nama', 'asc')->paginate($perPage);

        // 4. DATA ROMBEL (Untuk Dropdown)
        $rombels = Rombel::select('id', 'nama', 'anggota_rombel')
                         ->orderBy('nama', 'asc')
                         ->get()
                         ->unique('nama');

        // 5. MAPPING KELAS MANUAL (Agar tabel tidak kosong kolom kelasnya)
        $siswaRombelMap = [];
        foreach($rombels as $r) {
            $members = is_array($r->anggota_rombel) ? $r->anggota_rombel : json_decode($r->anggota_rombel, true);
            if(is_array($members)) {
                foreach($members as $m) {
                    if(isset($m['peserta_didik_id'])) {
                        $siswaRombelMap[$m['peserta_didik_id']] = $r->nama;
                    }
                }
            }
        }

        return view('admin.kesiswaan.siswa.index', compact('siswas', 'rombels', 'siswaRombelMap'));
    }

    public function create()
    {
        return view('admin.kesiswaan.siswa.create');
    }

    public function store(Request $request)
    {
        $request->validate(['nama_lengkap' => 'required|string|max:255']);
        $data = $request->except(['foto']);
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('siswa/foto', 'public');
        }
        Siswa::create($data);
        return redirect()->route('admin.kesiswaan.siswa.index')->with('success', 'Data siswa berhasil ditambahkan.');
    }

    /**
     * SHOW SINGLE (Dibungkus jadi Collection biar View-nya sama)
     */
    public function show($id)
    {
        // Kita ambil pakai get() supaya jadi Collection (isi 1 item)
        // Bukan findOrFail() yang langsung jadi object
        $siswas = Siswa::with('rombel')->where('id', $id)->get();

        return view('admin.kesiswaan.siswa.show', compact('siswas'));
    }

    /**
     * SHOW MULTIPLE (Ambil banyak ID, kirim sebagai Collection)
     */
    public function showMultiple(Request $request)
    {
        $idsStr = $request->query('ids', '');
        if (empty($idsStr)) {
            return redirect()->route('admin.kesiswaan.siswa.index');
        }

        $idsArray = explode(',', $idsStr);
        
        // Ambil semua siswa yang dipilih, urutkan sesuai nama
        $siswas = Siswa::with('rombel')
                        ->whereIn('id', $idsArray)
                        ->orderBy('nama', 'asc')
                        ->get();

        return view('admin.kesiswaan.siswa.show', compact('siswas'));
    }
    public function edit($id)
    {
        // Redirect ke show karena kita pakai One Page Edit
        return $this->show($id);
    }

    /**
     * UPDATE DATA
     * Hanya mengizinkan edit Kolom Tambahan & Foto.
     * Data Dapodik (Nama, NISN, dll) aman/terkunci.
     */
    /**
     * UPDATE: HANYA IZINKAN EDIT KOLOM TAMBAHAN (GRUP B) + FOTO
     */
    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        // 1. Ambil inputan HANYA untuk kolom tambahan (Grup B + ALAMAT)
        $dataToUpdate = $request->only([
            // --- DATA BARU 1: ALAMAT (SEKARANG BOLEH DIEDIT) ---
            'alamat_jalan', 'rt', 'rw', 'dusun', 'desa_kelurahan', 
            'kecamatan', 'kabupaten_kota', 'provinsi', 'kode_pos', 
            'lintang', 'bujur', 'jenis_tinggal_id_str', // Jenis tinggal juga biasanya nempel sama alamat

            // --- DATA BARU 2: KESEJAHTERAAN ---
            'no_kks', 'penerima_kps', 'no_kps', 'layak_pip', 'alasan_layak_pip',
            'penerima_kip', 'no_kip', 'nama_di_kip', 'alasan_menolak_kip',
            
            // --- DATA BARU 3: DETAIL ORTU & WALI ---
            'tahun_lahir_ayah', 'pendidikan_ayah_id_str', 'penghasilan_ayah_id_str', 'kebutuhan_khusus_ayah',
            'tahun_lahir_ibu', 'pendidikan_ibu_id_str', 'penghasilan_ibu_id_str', 'kebutuhan_khusus_ibu',
            'tahun_lahir_wali', 'pendidikan_wali_id_str', 'penghasilan_wali_id_str', 
            'nama_wali', 'pekerjaan_wali_id_str',

            // --- DATA BARU 4: TRANSPORT & PERIODIK ---
            'alat_transportasi_id_str', 'jarak_rumah_ke_sekolah_km', 'waktu_tempuh_menit', 
            'jumlah_saudara_kandung', 'tinggi_badan', 'berat_badan',
            
            // --- DATA BARU 5: DOKUMEN ---
            'npsn_sekolah_asal', 'no_seri_ijazah', 'no_seri_skhun', 'no_ujian_nasional', 'no_registrasi_akta_lahir',
            'sekolah_asal', 'anak_keberapa',

            // Kontak (Boleh Edit)
            'email', 'nomor_telepon_seluler', 'agama_id_str', 'kewarganegaraan'
        ]);

        // 2. Handle Foto
        if ($request->hasFile('foto')) {
            $request->validate(['foto' => 'image|max:2048']);
            if ($siswa->foto && Storage::disk('public')->exists($siswa->foto)) {
                Storage::disk('public')->delete($siswa->foto);
            }
            $dataToUpdate['foto'] = $request->file('foto')->store('siswa/foto', 'public');
        }

        // 3. Simpan
        $siswa->update($dataToUpdate);

        return redirect()->route('admin.kesiswaan.siswa.show', $siswa->id)
                         ->with('success', 'Data siswa berhasil diperbarui.');

        $siswa->update($dataToUpdate);

        // LOGIC REDIRECT: Cek apakah sedang mode Multiple?
        if ($request->has('_ids_multiple')) {
            // Balik ke mode show-multiple di ID yang sama
            return redirect()->route('admin.kesiswaan.siswa.show-multiple', [
                'ids' => $request->_ids_multiple,
                'current_id' => $siswa->id
            ])->with('success', 'Data berhasil disimpan.');
        }

        // Redirect Biasa
        return redirect()->route('admin.kesiswaan.siswa.show', $siswa->id)
                         ->with('success', 'Data tambahan siswa berhasil diperbarui.');
    
    }

    public function destroy(Siswa $siswa)
    {
        if ($siswa->foto) {
            Storage::disk('public')->delete($siswa->foto);
        }
        $siswa->delete();
        return redirect()->route('admin.kesiswaan.siswa.index')->with('success', 'Data siswa berhasil dihapus.');
    }

    // --- FITUR CETAK KARTU ---

    public function cetakKartu($id)
    {
        $siswa = Siswa::findOrFail($id);
        if (empty($siswa->qr_token)) {
            $siswa->qr_token = Str::uuid()->toString();
            $siswa->save();
        }
        return view('admin.kesiswaan.siswa.kartu', compact('siswa'));
    }

    public function showCetakMassalIndex()
    {
        $rombels = Rombel::orderBy('nama', 'asc')->get()->unique('nama');
        return view('admin.kesiswaan.siswa.cetak_massal_index', compact('rombels'));
    }

    public function cetakKartuMassal(Rombel $rombel)
    {
        $anggotaData = is_array($rombel->anggota_rombel) 
            ? $rombel->anggota_rombel 
            : json_decode($rombel->anggota_rombel, true);
            
        $siswaIds = [];
        if (!empty($anggotaData)) {
             $anggotaPdIds = array_column($anggotaData, 'peserta_didik_id');
             $siswaIds = Siswa::whereIn('peserta_didik_id', $anggotaPdIds)->pluck('id');
        }

        $siswas = Siswa::whereIn('id', $siswaIds)->orderBy('nama', 'asc')->get();

        foreach ($siswas as $siswa) {
            if (empty($siswa->qr_token)) {
                $siswa->qr_token = Str::uuid()->toString();
                $siswa->save();
            }
        }

        return view('admin.kesiswaan.siswa.kartu_massal', compact('siswas', 'rombel'));
    }

    // --- FITUR CETAK BIODATA PDF (BARU) ---

    public function cetakPdf($id)
    {
        $siswa = Siswa::with('rombel')->findOrFail($id);
        $sekolah = Sekolah::first();

        // WRAPPING: Bungkus jadi collection agar view bisa meloop
        $siswas = collect([$siswa]); 

        $pdf = Pdf::loadView('admin.kesiswaan.siswa.pdf_biodata', compact('siswas', 'sekolah'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('Biodata_'.$siswa->nama.'.pdf');
    }

    /**
     * CETAK PDF MULTIPLE (Dari Tombol Header)
     */
    public function cetakPdfMultiple(Request $request)
    {
        // Gunakan 'input' agar bisa menangkap dari Query String (?ids=...) maupun Body
        $idsStr = $request->input('ids');
        
        // Debugging: Jika masih mental, uncomment baris di bawah ini untuk cek data masuk gak
        // dd($idsStr); 

        if (empty($idsStr)) {
            return redirect()->back()->with('error', 'Gagal mencetak: Tidak ada data siswa yang dipilih.');
        }

        $idsArray = explode(',', $idsStr);
        $sekolah = Sekolah::first();

        // Ambil data siswa
        $siswas = Siswa::with('rombel')
                        ->whereIn('id', $idsArray)
                        ->orderBy('nama', 'asc')
                        ->get();

        if ($siswas->isEmpty()) {
            return redirect()->back()->with('error', 'Data siswa tidak ditemukan di database.');
        }

        $pdf = Pdf::loadView('admin.kesiswaan.siswa.pdf_biodata', compact('siswas', 'sekolah'));
        $pdf->setPaper('a4', 'portrait');
        
        $filename = 'Kumpulan_Biodata_Siswa_'.date('d-m-Y_His').'.pdf';
        return $pdf->stream($filename);
    }

   public function exportExcel(Request $request)
    {
        // A. LOGIKA FILTER ID (SELECTED EXPORT)
        $ids = null;
        // Cek apakah ada parameter 'ids' di URL (dikirim dari Javascript index.blade.php)
        if ($request->has('ids') && !empty($request->query('ids'))) {
            $ids = explode(',', $request->query('ids'));
        }

        // B. LOGIKA NAMA FILE (DARI DB SEKOLAH)
        $sekolah = Sekolah::first();
        
        if ($sekolah) {
            // Ubah "SMK HEBAT" jadi "SMK_HEBAT" agar aman buat nama file
            $schoolName = Str::slug($sekolah->nama, '_'); 
        } else {
            $schoolName = 'DATA_SISWA';
        }

        // Hasil: SMK_NURUL_ISLAM_AFFANDIYAH_DATA_SISWA_17-12-2025.xlsx
        $fileName = strtoupper($schoolName) . '_DATA_SISWA_' . date('d-m-Y') . '.xlsx';
        
        // C. DOWNLOAD
        // Kita kirim $ids ke class SiswaExport. 
        // Jika $ids null, dia akan export semua. Jika ada isi, dia filter.
        return Excel::download(new SiswaExport($ids), $fileName);
    }

    // Personal
    public function profil()
    {
        if (!auth()->check() || !session()->has('peserta_didik_id')) {
            abort(403);
        }

        $siswa = Siswa::where(
            'peserta_didik_id',
            session('peserta_didik_id')
        )->firstOrFail();

        $siswas = collect([$siswa]);

        return view('admin.personal.siswa.profil', compact('siswa'));
    }

    public function pelanggaran()
    {
        // ===============================
        // VALIDASI LOGIN SISWA
        // ===============================
        if (!session()->has('peserta_didik_id')) {
            abort(403, 'Akses ditolak');
        }

        // ===============================
        // AMBIL DATA SISWA LOGIN
        // ===============================
        $siswa = Siswa::where('peserta_didik_id', session('peserta_didik_id'))
            ->with('rombel')
            ->firstOrFail();

        // ===============================
        // AMBIL DATA PELANGGARAN SISWA
        // ===============================
        $pelanggaranSiswa = PelanggaranNilai::where('nipd', $siswa->nipd)
            ->with('detailPoinSiswa')
            ->orderBy('tanggal', 'desc')
            ->get();

        // ===============================
        // HITUNG TOTAL POIN
        // ===============================
        $totalPoin = $pelanggaranSiswa->sum('poin');

        // ===============================
        // AMBIL SANKSI AKTIF
        // ===============================
        $sanksiAktif = PelanggaranSanksi::where('poin_min', '<=', $totalPoin)
            ->where('poin_max', '>=', $totalPoin)
            ->first();

        // ===============================
        // KIRIM KE VIEW
        // ===============================
        return view('admin.personal.siswa.pelanggaran', [
            'siswa'             => $siswa,
            'pelanggaranSiswa'  => $pelanggaranSiswa,
            'totalPoin'         => $totalPoin,
            'sanksiAktif'       => $sanksiAktif,

            // dummy biar blade lama gak error
            'tingkatList' => collect(),
            'rombelList'  => collect(),
            'siswaList'   => collect(),
        ]);
    }
}