<?php

namespace App\Http\Controllers\Admin\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Rombel;
use App\Models\Sekolah;
use App\Models\PelanggaranNilai;
use App\Models\PelanggaranSanksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SiswaExport;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class SiswaController extends Controller
{
    /**
     * Menampilkan Daftar Siswa (Tabel)
     */
    public function index(Request $request)
    {
        $query = Siswa::with('rombel')->where('status', 'Aktif');

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
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'no_wa' => 'nullable|string|max:32',
        ]);
        $data = $request->except(['foto']);

        if ($request->hasFile('foto')) {
            $request->validate(['foto' => 'image|mimes:jpeg,png,jpg|max:5120']); // 5MB

            $file = $request->file('foto');
            $fileName = time() . '_' . Str::slug($request->nama_lengkap) . '.jpg';
            $path = 'siswa/foto/' . $fileName;

            // Compress using Intervention Image and save as jpg with quality 70
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file);
            $image->scale(width: 400);
            $encoded = $image->toJpeg(70);
            Storage::disk('public')->put($path, (string) $encoded);

            $data['foto'] = $path;
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

    /**
     * Halaman Buku Induk - Pilih Kelas lalu tampilkan siswa per kelas
     */
    public function bukuIndukIndex(Request $request)
    {
        $rombels = Rombel::select('id','nama','anggota_rombel')
                         ->orderBy('nama', 'asc')
                         ->get()
                         ->unique('nama');

        $siswas = collect();
        $selectedRombel = null;

        if ($request->filled('rombel_id')) {
            $rombel = Rombel::find($request->rombel_id);
            if ($rombel) {
                $anggotaData = is_array($rombel->anggota_rombel)
                    ? $rombel->anggota_rombel
                    : json_decode($rombel->anggota_rombel, true);

                $pesertaIds = !empty($anggotaData) ? array_column($anggotaData, 'peserta_didik_id') : [];

                $siswas = Siswa::whereIn('peserta_didik_id', $pesertaIds)
                                ->orderBy('nama', 'asc')
                                ->get();
                $selectedRombel = $rombel;
            }
        }

        return view('admin.kesiswaan.siswa.buku_induk_index', compact('rombels','siswas','selectedRombel'));
    }

    /**
     * Tampilkan siswa untuk rombel tertentu (link dari daftar rombel)
     */
    public function bukuIndukRombel(Rombel $rombel)
    {
        $anggotaData = is_array($rombel->anggota_rombel)
            ? $rombel->anggota_rombel
            : json_decode($rombel->anggota_rombel, true);

        $pesertaIds = !empty($anggotaData) ? array_column($anggotaData, 'peserta_didik_id') : [];

        $siswas = Siswa::whereIn('peserta_didik_id', $pesertaIds)
                        ->orderBy('nama', 'asc')
                        ->get();

        $rombels = Rombel::select('id','nama','anggota_rombel')
                         ->orderBy('nama', 'asc')
                         ->get()
                         ->unique('nama');

        return view('admin.kesiswaan.siswa.buku_induk_index', compact('rombels','siswas','rombel'));
    }

    /**
     * Cetak Buku Induk per siswa (re-use cetakPdf)
     */
    public function cetakBukuInduk($id)

    {
        // Reuse existing biodata PDF as Buku Induk (same format)
        return $this->cetakPdf($id);
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

        // 2. Handle Foto (kompres sama seperti GTK)
        if ($request->hasFile('foto')) {
            $request->validate(['foto' => 'image|mimes:jpeg,png,jpg|max:5120']); // 5MB

            // Hapus foto lama
            if ($siswa->foto && Storage::disk('public')->exists($siswa->foto)) {
                Storage::disk('public')->delete($siswa->foto);
            }

            $file = $request->file('foto');
            $fileName = time() . '_' . Str::slug($siswa->nama) . '.jpg';
            $path = 'siswa/foto/' . $fileName;

            $manager = new ImageManager(new Driver());
            $image = $manager->read($file);
            $image->scale(width: 400);
            $encoded = $image->toJpeg(70);
            Storage::disk('public')->put($path, (string) $encoded);

            $dataToUpdate['foto'] = $path;
        }

        // 3. Simpan
        $siswa->update($dataToUpdate);

        if (session()->has('peserta_didik_id')) {
            return redirect()
                ->route('admin.personal.siswa.profil')
                ->with('success', 'Profil berhasil diperbarui.');
        }
        
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
        $sekolah = Sekolah::first();
        return view('admin.kesiswaan.siswa.cetak_massal_index', compact('rombels', 'sekolah'));
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

    // --- TAMBAHKAN BARIS INI ---
    $sekolah = Sekolah::first();

    foreach ($siswas as $siswa) {
        if (empty($siswa->qr_token)) {
            $siswa->qr_token = Str::uuid()->toString();
            $siswa->save();
        }
    }

    // Pastikan 'sekolah' dimasukkan ke compact
    return view('admin.kesiswaan.siswa.kartu_massal', compact('siswas', 'rombel', 'sekolah'));
}

    /**
     * Upload/Compress Foto Siswa (dipanggil dari modal/upload khusus)
     */
    public function uploadMedia(Request $request, $id)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:5120'
        ]);

        $siswa = Siswa::findOrFail($id);

        // Hapus foto lama jika ada
        if ($siswa->foto && Storage::disk('public')->exists($siswa->foto)) {
            Storage::disk('public')->delete($siswa->foto);
        }

        $file = $request->file('foto');
        $fileName = time() . '_' . Str::slug($siswa->nama) . '.jpg';
        $path = 'siswa/foto/' . $fileName;

        $manager = new ImageManager(new Driver());
        $image = $manager->read($file);
        $image->scale(width: 400);
        $encoded = $image->toJpeg(70);
        Storage::disk('public')->put($path, (string) $encoded);

        $siswa->foto = $path;
        $siswa->save();

        return back()->with('success', 'Foto ' . $siswa->nama . ' berhasil dikompres dan disimpan!');
    }

    /**
     * Upload Background untuk Kartu Siswa (sama seperti GTK)
     */
    public function uploadBackgroundKartu(Request $request)
    {
        $request->validate([
            'background_kartu' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $sekolah = Sekolah::first();
        if (!$sekolah) {
            $sekolah = new Sekolah();
        }

        if ($request->hasFile('background_kartu')) {
            // Hapus file lama jika ada
            if ($sekolah->background_kartu && Storage::disk('public')->exists($sekolah->background_kartu)) {
                Storage::disk('public')->delete($sekolah->background_kartu);
            }

            $path = $request->file('background_kartu')->store('sekolah_media/background', 'public');
            $sekolah->background_kartu = $path;
            $sekolah->save();
        }

        return back()->with('success', 'Background kartu siswa berhasil diupdate!');
    }

    // --- FITUR CETAK BIODATA PDF (BARU) ---

    public function cetakPdf($id)
    {
     $siswa = Siswa::with(['rombel', 'mutasiKeluar'])->findOrFail($id);
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

    public function inactive(Request $request)
{
    // 1. FILTER DASAR: Ambil siswa yang statusnya BUKAN 'Aktif'
    // Asumsi di database statusnya ditulis 'Aktif'. Sesuaikan jika pakai angka (misal 1).
    $query = Siswa::with('rombel')->where('status', '!=', 'Aktif');

    // 2. FILTER KELAS (Opsional untuk alumni, tapi tetap kita simpan fiturnya)
    if ($request->filled('rombel_id')) {
        $rombel = Rombel::find($request->rombel_id);
        if ($rombel) {
            $anggotaData = is_array($rombel->anggota_rombel)
                ? $rombel->anggota_rombel
                : json_decode($rombel->anggota_rombel, true);

            if (!empty($anggotaData)) {
                $anggotaPdIds = array_column($anggotaData, 'peserta_didik_id');
                $query->whereIn('peserta_didik_id', $anggotaPdIds);
            } else {
                $query->whereRaw('1 = 0');
            }
        }
    }

    // 3. PENCARIAN
    if ($request->filled('search')) {
        $searchTerm = $request->search;
        $query->where(function ($q) use ($searchTerm) {
            $q->where('nama', 'like', "%{$searchTerm}%")
              ->orWhere('nisn', 'like', "%{$searchTerm}%")
              ->orWhere('nik', 'like', "%{$searchTerm}%");
        });
    }

    // 4. PAGINASI
    $perPage = $request->input('per_page', 15);
    $siswas = ($perPage == 'all')
        ? $query->orderBy('nama', 'asc')->paginate(9999)
        : $query->orderBy('nama', 'asc')->paginate($perPage);

    // 5. DATA ROMBEL (Untuk Dropdown Filter)
    $rombels = Rombel::select('id', 'nama', 'anggota_rombel')
                     ->orderBy('nama', 'asc')
                     ->get()
                     ->unique('nama');

    // 6. MAPPING KELAS (Opsional, biasanya alumni tidak punya rombel aktif)
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

    // Return ke View Baru (misal: inactive.blade.php)
    return view('admin.kesiswaan.siswa.inactive', compact('siswas', 'rombels', 'siswaRombelMap'));
}
public function registerKeluar(Request $request, $id)
{
    $request->validate([
        'status' => 'required', // Value contoh: 'Lulus', 'Mutasi', 'Dikeluarkan'
        'tanggal_keluar' => 'required|date',
        'alasan' => 'nullable|string',
    ]);

    $siswa = Siswa::findOrFail($id);

    // 1. Simpan Log (Ini kode lama Anda, sudah benar)
    $siswa->mutasiKeluar()->create([
        'status' => $request->status,
        'tanggal_keluar' => $request->tanggal_keluar,
        'keterangan' => $request->alasan,
    ]);

    // 2. [TAMBAHAN WAJIB] Update Status di Tabel Induk Siswa
    $siswa->status = $request->status;

    // (Opsional) Hapus rombel_id jika Anda menggunakan relasi foreign key
    // $siswa->rombel_id = null;

    $siswa->save(); // <--- SIMPAN PERUBAHAN KE DATABASE

    return redirect()->route('admin.kesiswaan.siswa.index')
                     ->with('success', 'Status keluar siswa ' . $siswa->nama . ' berhasil diperbarui menjadi ' . $request->status);
}

    /**
     * Undo a register-keluar action by deleting the related mutasi record
     * and setting the student's status back to 'Aktif'. This is intended
     * for accidental registrations and is a safe reversal.
     */
    public function unregisterKeluar(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        $mutasi = $siswa->mutasiKeluar;
        if (!$mutasi) {
            return redirect()->route('admin.kesiswaan.siswa.index')
                             ->with('error', 'Tidak ditemukan catatan keluar yang bisa dibatalkan untuk siswa ini.');
        }

        // Delete the mutasi record and restore status
        try {
            $mutasi->delete();
            $siswa->status = 'Aktif';

            // If there are any tanggal_keluar on siswa record, try to clear it
            if (isset($siswa->tanggal_keluar)) {
                $siswa->tanggal_keluar = null;
            }
            $siswa->save();

            return redirect()->route('admin.kesiswaan.siswa.index')
                             ->with('success', 'Pencatatan keluar siswa untuk ' . $siswa->nama . ' berhasil dibatalkan.');
        } catch (\Exception $e) {
            return redirect()->route('admin.kesiswaan.siswa.index')
                             ->with('error', 'Gagal membatalkan pencatatan keluar: ' . $e->getMessage());
        }
    }
}

