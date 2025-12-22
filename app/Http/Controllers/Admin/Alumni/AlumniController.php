<?php

namespace App\Http\Controllers\Admin\Alumni;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Sekolah;

class AlumniController extends Controller
{
    /**
     * Display a listing of the resource.
     */
        public function index(Request $request)
{
    $query = Siswa::where('status', 'Lulus'); // KUNCI alumni

    // PENCARIAN
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('nama', 'like', "%{$search}%")
              ->orWhere('nisn', 'like', "%{$search}%")
              ->orWhere('nik', 'like', "%{$search}%");
        });
    }

    // PAGINASI
    $perPage = $request->input('per_page', 15);
    $siswas = ($perPage === 'all')
        ? $query->orderBy('nama')->paginate(9999)
        : $query->orderBy('nama')->paginate($perPage);

    return view('admin.alumni.index', compact('siswas'));
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

    public function show($id)
    {
        // Kita ambil pakai get() supaya jadi Collection (isi 1 item)
        // Bukan findOrFail() yang langsung jadi object
        $siswas = Siswa::where('id', $id)->get();

        return view('admin.alumni.show', compact('siswas'));
    }

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

        return redirect()->route('admin.alumni.dataAlumni.show', $siswa->id)
                         ->with('success', 'Data siswa berhasil diperbarui.');

        $siswa->update($dataToUpdate);

        // LOGIC REDIRECT: Cek apakah sedang mode Multiple?
        if ($request->has('_ids_multiple')) {
            // Balik ke mode show-multiple di ID yang sama
            return redirect()->route('admin.alumni.show-multiple', [
                'ids' => $request->_ids_multiple,
                'current_id' => $siswa->id
            ])->with('success', 'Data berhasil disimpan.');
        }

        // Redirect Biasa
        return redirect()->route('admin.alumni.dataAlumni.show', $siswa->id)
                         ->with('success', 'Data tambahan siswa berhasil diperbarui.');
    
    }

public function showMultiple(Request $request)
    {
        $idsStr = $request->query('ids', '');
        if (empty($idsStr)) {
            return redirect()->route('admin.kesiswaan.siswa.index');
        }

        $idsArray = explode(',', $idsStr);
        
        // Ambil semua siswa yang dipilih, urutkan sesuai nama
        $siswas = Siswa::whereIn('id', $idsArray)
                        ->orderBy('nama', 'asc')
                        ->get();

        return view('admin.alumni.show', compact('siswas'));
    }

    public function lulus(Request $request)
    {
        // Ambil kelas XII, urutkan
        $kelas = Siswa::where('nama_rombel', 'LIKE', 'XII%')
                    ->select('nama_rombel')
                    ->distinct()
                    ->orderBy('nama_rombel')
                    ->get();

        // Ambil siswa berdasarkan kelas + urutkan nama A-Z
        $siswa = collect();

        if ($request->kelas) {
            $siswa = Siswa::where('nama_rombel', $request->kelas)
                        ->orderBy('nama')   // ← ini yang wajib
                        ->get();
        }

        return view('admin.alumni.lulus', compact('kelas', 'siswa'));
    }



    public function process(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|array',
        ]);

        Siswa::whereIn('id', $request->siswa_id)->update([
            'status' => 'Lulus',
        ]);

        return back()->with('success', 'Siswa berhasil diluluskan.');
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

}
