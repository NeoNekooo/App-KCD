<?php

// Ganti namespace ini jika folder Anda berbeda
namespace App\Http\Controllers\Admin\Indisipliner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// --- Model Internal Anda ---
use App\Models\PelanggaranKategori;
use App\Models\PelanggaranPoin;
use App\Models\PelanggaranSanksi;
use App\Models\PelanggaranNilai;

// --- Model Data Dapodik ---
use App\Models\Siswa;
use App\Models\Rombel;

// --- Helper ---
use Illuminate\Support\Facades\Validator;
use Exception; // Untuk try-catch
use Illuminate\Support\Facades\DB;

class IndisiplinerSiswaController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | BAGIAN PENGATURAN (Settings)
    | Sesuai route: admin.indisipliner.siswa.pengaturan.*
    |--------------------------------------------------------------------------
    */
    
    /**
     * Menampilkan halaman Pengaturan (Tab Kategori, Poin, Sanksi).
     */
    public function pengaturanIndex()
    {
        // Ambil data untuk Tab 1 & 2
        // Asumsi relasi di Model PelanggaranKategori: pelanggaranPoinSiswa()
        $kategoriList = PelanggaranKategori::with('pelanggaranPoin')
                        ->orderBy('nama')
                        ->get();
        
        // Ambil data untuk Tab 3
        $sanksiList = PelanggaranSanksi::orderBy('poin_min')
                      ->get();

        return view('admin.indisipliner.siswa.pengaturan.index', compact(
            'kategoriList', 
            'sanksiList'
        ));
    }

    /* --- Logic Kategori --- */
    public function storeKategori(Request $request)
    {
        $request->validate(['nama' => 'required|string|max:100']);
        PelanggaranKategori::create($request->only('nama'));
        return redirect()->back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function updateKategori(Request $request, PelanggaranKategori $pelanggaranKategori)
    {
        $request->validate(['nama' => 'required|string|max:100']);
        $pelanggaranKategori->update($request->only('nama'));
        return redirect()->back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroyKategori(PelanggaranKategori $pelanggaranKategori)
    {
        $pelanggaranKategori->delete();
        return redirect()->back()->with('success', 'Kategori berhasil dihapus.');
    }

    /* --- Logic Poin --- */
    public function storePoin(Request $request)
    {
        $request->validate([
            'IDpelanggaran_kategori' => 'required|exists:pelanggaran_kategori,ID',
            'nama' => 'required|string',
            'poin' => 'required|integer',
        ]);
        PelanggaranPoin::create($request->all());
        return redirect()->back()->with('success', 'Jenis pelanggaran berhasil ditambahkan.');
    }

    public function updatePoin(Request $request, PelanggaranPoin $pelanggaranPoin)
    {
        $request->validate([
            'IDpelanggaran_kategori' => 'required|exists:pelanggaran_kategori,ID',
            'nama' => 'required|string',
            'poin' => 'required|integer',
        ]);
        $pelanggaranPoin->update($request->all());
        return redirect()->back()->with('success', 'Jenis pelanggaran berhasil diperbarui.');
    }

    public function destroyPoin(PelanggaranPoin $pelanggaranPoin)
    {
        $pelanggaranPoin->delete();
        return redirect()->back()->with('success', 'Jenis pelanggaran berhasil dihapus.');
    }

    /* --- Logic Sanksi --- */
    public function storeSanksi(Request $request)
    {
        $request->validate([
            'poin_min' => 'required|integer',
            'poin_max' => 'required|integer|gte:poin_min',
            'nama' => 'required|string',
        ]);
        PelanggaranSanksi::create($request->all());
        return redirect()->back()->with('success', 'Sanksi berhasil ditambahkan.');
    }

    public function updateSanksi(Request $request, PelanggaranSanksi $pelanggaranSanksi)
    {
        $request->validate([
            'poin_min' => 'required|integer',
            'poin_max' => 'required|integer|gte:poin_min',
            'nama' => 'required|string',
        ]);
        $pelanggaranSanksi->update($request->all());
        return redirect()->back()->with('success', 'Sanksi berhasil diperbarui.');
    }

    public function destroySanksi(PelanggaranSanksi $pelanggaranSanksi)
    {
        $pelanggaranSanksi->delete();
        return redirect()->back()->with('success', 'Sanksi berhasil dihapus.');
    }


    /*
    |--------------------------------------------------------------------------
    | BAGIAN DAFTAR PELANGGARAN (Index, Store, Destroy)
    | Sesuai route: admin.indisipliner.siswa.daftar.*
    |--------------------------------------------------------------------------
    */

   /**
     * Menampilkan halaman daftar pelanggaran siswa (index).
     */
    public function daftarIndex(Request $request)
    {
        // --- 1. Query Utama (Pelanggaran) ---
        $query = PelanggaranNilai::with(['siswa', 'rombel', 'detailPoinSiswa'])
                 ->orderBy('tanggal', 'desc')
                 ->orderBy('jam', 'desc');

        // --- 2. Logika Filter (Untuk Tabel) ---
        if ($request->filled('rombel_id')) {
            $query->where('rombongan_belajar_id', $request->rombel_id);
        }

        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }

        if ($request->filled('tingkat_kelas')) { 
            $query->whereHas('rombel', function($q) use ($request) {
                $q->where('tingkat_pendidikan_id_str', $request->tingkat_kelas);
            });
        }

        // --- 3. Ambil Data & Paginate ---
        $pelanggaranList = $query->paginate(10)->withQueryString();

        // --- 4. Data untuk Filter & Modal ---
        
        // (A) Ambil daftar SEMESTER (untuk dropdown filter semester)
        $semesterList = $this->getSemesterListForFilter(); // Helper

        
        // --- 👇 PERBAIKAN UTAMA ADA DI SINI 👇 ---

        // (B) Tentukan semester mana yang akan dipakai untuk mengisi dropdown Rombel
        
        // Ambil ID semester terbaru/aktif dari helper
        // (Asumsi helper 'getSemesterListForFilter' mengurutkan dari terbaru)
        $semesterAktifId = $semesterList->first()['semester_id'] ?? null;
        
        // Jika user memfilter (misal "20251"), gunakan itu.
        // Jika user memilih "Semua Semester" (kosong) atau baru buka halaman,
        // kita paksa gunakan semester Aktif ("20251") agar data rombel tidak duplikat.
        $semesterUntukFilterDropdown = $request->input('semester_id', $semesterAktifId);
        if (empty($semesterUntukFilterDropdown)) {
             $semesterUntukFilterDropdown = $semesterAktifId;
        }

        // (C) Ambil daftar KELAS (hanya dari semester yang dipilih)
        $kelasList = Rombel::select('tingkat_pendidikan_id_str as nama')
                         ->where('semester_id', $semesterUntukFilterDropdown) // <-- PERBAIKAN 1
                         ->whereNotNull('tingkat_pendidikan_id_str')
                         ->distinct()
                         ->orderBy('nama')
                         ->get(); 

        // (D) Ambil daftar ROMBEL (hanya dari semester yang dipilih)
        $rombelQuery = Rombel::query();

        // Query ini SEKARANG SELALU terfilter berdasarkan satu semester
        $rombelQuery->where('semester_id', $semesterUntukFilterDropdown); // <-- PERBAIKAN 2 (INI YANG PALING PENTING)

        // Terapkan filter kelas (jika ada)
        if ($request->filled('tingkat_kelas')) {
            $rombelQuery->where('tingkat_pendidikan_id_str', $request->tingkat_kelas);
        }
        
        // Hasil $rombelList di sini PASTI bersih (tidak duplikat)
        $rombelList = $rombelQuery->orderBy('nama')->get(); 

        // --- 👆 PERBAIKAN SELESAI 👆 ---


        // (E) Ambil data KATEGORI (untuk modal)
        $kategoriPelanggaranSiswaList = PelanggaranKategori::with('pelanggaranPoin')
                                         ->orderBy('nama')->get();

        // --- 5. Return View ---
        return view('admin.indisipliner.siswa.daftar.index', compact(
            'pelanggaranList',
            'semesterList',       // Daftar lengkap semester (untuk filter)
            'kelasList',          // Daftar kelas bersih (untuk filter)
            'rombelList',         // Daftar rombel bersih (untuk filter & modal)
            'kategoriPelanggaranSiswaList'
        ));
    }

    /**
     * Menyimpan data pelanggaran baru dari modal.
     */
    public function store(Request $request)
    {
        // --- 1. Validasi (Sesuai MIGRAsI BARU) ---
        $validator = Validator::make($request->all(), [
            'rombongan_belajar_id' => 'required|integer|exists:rombels,id',
            'nipd'                   => 'required|string|exists:siswas,nipd',
            'semester_id'            => 'required|string|max:10', // cth: "20251"
            'IDpelanggaran_poin'     => 'required|integer|exists:pelanggaran_poin,ID',
            'tanggal'                => 'required|date',
            'jam'                    => 'required|date_format:H:i',
            'poin'                   => 'required|integer',
            'pembelajaran'           => 'nullable|string|max:191',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                     ->withErrors($validator)
                     ->withInput();
        }
        
        // --- 2. Simpan ke Database (Sangat Sederhana) ---
        try {
            PelanggaranNilai::create($request->all());
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['msg' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }

        return redirect()->route('admin.indisipliner.siswa.daftar.index')
                     ->with('success', 'Data pelanggaran siswa berhasil disimpan.');
    }

    /**
     * Menghapus data pelanggaran (menggunakan Route Model Binding).
     */
    public function destroy(PelanggaranNilai $pelanggaran)
    {
        try {
            $pelanggaran->delete();
            return redirect()->route('admin.indisipliner.siswa.daftar.index')
                         ->with('success', 'Data pelanggaran berhasil dihapus.');
        } catch (Exception $e) {
            return redirect()->route('admin.indisipliner.siswa.daftar.index')
                         ->withErrors(['msg' => 'Gagal menghapus data. ' . $e->getMessage()]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | BAGIAN AJAX (Sesuai Route Name Anda)
    | Sesuai route: admin.indisipliner.siswa.getRombelDetails
    |--------------------------------------------------------------------------
    */

    /**
     * API untuk mengambil detail Rombel (Semester, Siswa, Mapel).
     * PENTING: Di web.php Anda harus ada route 'getRombelDetails'
     */
    public function getRombelDetails($rombel_id_internal)
    {
        try {
            $rombel = Rombel::find($rombel_id_internal);
            if (!$rombel) {
                return response()->json(['error' => 'Rombel tidak ditemukan'], 404);
            }

            // 1. Ambil Siswa
            $siswaList = Siswa::where('rombongan_belajar_id', $rombel->rombongan_belajar_id)
                              ->select('nipd', 'nisn', 'nama')
                              ->orderBy('nama')
                              ->get();

            // 2. Ambil Mata Pelajaran (dari JSON di tabel rombels)
            $mapelData = json_decode($rombel->pembelajaran, true) ?: [];
            
            // --- 👇 INI ADALAH PERBAIKANNYA 👇 ---
            // Kita ubah format data agar sesuai dengan JavaScript
            $mapelList = [];
            foreach ($mapelData as $mapel) {
                // ⚠️ ASUMSI: Pastikan nama key untuk nama mapel sudah benar
                // (di screenshot Anda terpotong, saya asumsikan 'nama_mata_pelajaran')
                $mapelList[] = [
                    'id'   => $mapel['pembelajaran_id'],      // Mengambil 'pembelajaran_id' dari DB
                    'nama' => $mapel['nama_mata_pelajaran'] ?? 'Mapel Tanpa Nama' // Mengambil nama mapel
                ];
            }
            // --- 👆 AKHIR PERBAIKAN 👆 ---

            // 3. Ambil dan Format Data Semester (dari "20251")
            $semester_id_dapodik = $rombel->semester_id;
            
            $tapel = substr($semester_id_dapodik, 0, 4);
            $sem = substr($semester_id_dapodik, 4, 1);
            $tapel_text = $tapel . '/' . ($tapel + 1);
            $semester_text = ($sem == '1') ? 'Ganjil' : 'Genap';
            
            $semesterData = [
                'id'   => $semester_id_dapodik, // Nilai yang disimpan: "20251"
                'text' => $tapel_text . ' ' . $semester_text // Teks tampilan: "2025/2026 Ganjil"
            ];

            return response()->json([
                'semester' => $semesterData,
                'siswa'    => $siswaList,
                'mapel'    => $mapelList, // Mengirim data mapel yang sudah diformat ulang
            ]);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | BAGIAN REKAPITULASI
    | Sesuai route: admin.indisipliner.siswa.rekapitulasi.index
    |--------------------------------------------------------------------------
    */

    /**
     * Menampilkan halaman rekapitulasi (pencarian dan hasil).
     * INI ADALAH PERBAIKAN UNTUK ERROR $siswaList
     */

     public function getRombelsByTingkat(Request $request)
    {
        // Ambil 'tingkat' dari query ?tingkat=... di JavaScript
        $tingkat = $request->input('tingkat'); 

        if (!$tingkat) {
            return response()->json([], 400); // Bad Request
        }

        // Query ini SAMA PERSIS dengan yang ada di 'daftarIndex' kamu
        // untuk mengambil Rombel berdasarkan 'tingkat_pendidikan_id_str'
        $rombels = Rombel::where('tingkat_pendidikan_id_str', $tingkat) 
                       ->orderBy('nama', 'asc')
                       ->get(['id', 'nama']); // Hanya ambil id dan nama

        // Kembalikan sebagai JSON
        return response()->json($rombels);
    }
    
     public function rekapitulasiIndex(Request $request)
    {
        // ==========================================================
        // --- 1. LOGIKA FILTER BERTINGKAT (SUDAH BENAR) ---
        // ==========================================================
        
        $filterTingkat = $request->input('filter_tingkat');
        $filterRombel = $request->input('filter_rombel');

        // Ambil semua tingkatan unik
        $tingkatList = Rombel::select('tingkat_pendidikan_id_str')
                             ->whereNotNull('tingkat_pendidikan_id_str')
                             ->distinct()
                             ->orderBy('tingkat_pendidikan_id_str', 'asc')
                             ->pluck('tingkat_pendidikan_id_str');

        // Ambil rombel (jika tingkatan dipilih)
        $rombelList = collect();
        if ($filterTingkat) {
            $rombelList = Rombel::where('tingkat_pendidikan_id_str', $filterTingkat)
                                ->orderBy('nama')
                                ->get();
        }

        // Ambil siswa (jika rombel dipilih)
        $siswaList = collect();
        if ($filterRombel) {
            $rombel = Rombel::find($filterRombel); 
            if ($rombel) {
                $siswaList = Siswa::where('rombongan_belajar_id', $rombel->rombongan_belajar_id)
                                  ->orderBy('nama')
                                  ->get();
            }
        }

        // ==========================================================
        // --- 2. LOGIKA PENCARIAN (INI YANG HILANG/KOSONG) ---
        // ==========================================================

        $siswa = null;
        $totalPoin = 0;
        $sanksiAktif = null;
        $pelanggaranSiswa = collect(); 

        // Cek apakah ada NIPD (dari 'nis') yang dicari
        $nipd = $request->input('nis');

        if ($nipd) {
            // --- 👇👇 INI BAGIAN PENTING YANG HILANG 👇👇 ---

            // Jika ada pencarian, cari datanya
            $siswa = Siswa::where('nipd', $nipd)->with('rombel')->first();

            if ($siswa) {
                // Jika siswa ditemukan, ambil semua datanya
                $pelanggaranSiswa = PelanggaranNilai::where('nipd', $siswa->nipd)
                                        ->with('detailPoinSiswa') // Eager load relasi
                                        ->orderBy('tanggal', 'desc')
                                        ->get();
                
                // Hitung total poin
                $totalPoin = $pelanggaranSiswa->sum('poin');

                // Cari sanksi yang sesuai
                $sanksiAktif = PelanggaranSanksi::where('poin_min', '<=', $totalPoin)
                                ->where('poin_max', '>=', $totalPoin)
                                ->first();
            } else {
                // Jika NIPD dicari tapi tidak ada
                return redirect()->route('admin.indisipliner.siswa.rekapitulasi.index')
                                 ->withErrors('Siswa dengan NIPD tersebut tidak ditemukan.');
            }
            // --- 👆👆 AKHIR BAGIAN PENTING YANG HILANG 👆👆 ---
        }

        // ==========================================================
        // --- 3. KIRIM DATA KE VIEW (SUDAH BENAR) ---
        // ==========================================================
        
        return view('admin.indisipliner.siswa.rekapitulasi.index', compact(
            'tingkatList',     // Untuk filter 1
            'rombelList',      // Untuk filter 2
            'siswaList',       // Untuk filter 3
            'siswa',           // Untuk hasil
            'totalPoin',       // Untuk hasil
            'sanksiAktif',     // Untuk hasil
            'pelanggaranSiswa' // Untuk hasil
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | FUNGSI HELPER
    |--------------------------------------------------------------------------
    */

    /**
     * Helper untuk mengambil daftar semester unik dari Rombel
     * untuk mengisi dropdown FILTER di halaman INDEX.
     */
    private function getSemesterListForFilter()
    {
        $semesterIds = Rombel::select('semester_id')
                            ->distinct()
                            ->whereNotNull('semester_id')
                            ->orderBy('semester_id', 'desc')
                            ->pluck('semester_id');

        return $semesterIds->map(function($id) {
            if (empty($id) || strlen($id) < 5) return null;
            
            $tapel = substr($id, 0, 4);
            $sem = substr($id, 4, 1);
            
            return [
                'semester_id'     => $id, // cth: "20251"
                'tahun_pelajaran' => $tapel . '/' . ($tapel + 1), // cth: "2025/2026"
                'semester'        => ($sem == '1') ? 'Ganjil' : 'Genap',
            ];
        })->filter(); // Hapus data null jika ada format yg salah
    }

    /*
    |--------------------------------------------------------------------------
    | BAGIAN AJAX BARU (UNTUK SCANNER QR)
    |--------------------------------------------------------------------------
    */

    /**
     * API untuk mengambil data Siswa dan Rombel-nya berdasarkan QR Token.
     * Dipanggil oleh scanner QR di modal.
     */
    public function findSiswaByQr($qrToken)
    {
        try {
            // 1. Cari Siswa berdasarkan QR Token
            $siswa = Siswa::where('qr_token', $qrToken)->first();

            if (!$siswa) {
                return response()->json(['error' => 'QR Token tidak valid atau siswa tidak ditemukan'], 404);
            }

            // 2. Cari data Rombel internal berdasarkan relasi
            // Asumsi relasi: siswas.rombongan_belajar_id -> rombels.rombongan_belajar_id
            $rombel = Rombel::where('rombongan_belajar_id', $siswa->rombongan_belajar_id)->first();

            if (!$rombel) {
                // Ini tidak seharusnya terjadi jika data sinkron, tapi untuk keamanan
                return response()->json(['error' => 'Data Rombel untuk siswa ini tidak ditemukan'], 404);
            }

            // 3. Ambil Data Semester (Logika disalin dari method Anda 'getRombelDetails')
            $semester_id_dapodik = $rombel->semester_id;
            $tapel = substr($semester_id_dapodik, 0, 4);
            $sem = substr($semester_id_dapodik, 4, 1);
            $tapel_text = $tapel . '/' . ($tapel + 1);
            $semester_text = ($sem == '1') ? 'Ganjil' : 'Genap';
            $semesterData = [
                'id'   => $semester_id_dapodik,
                'text' => $tapel_text . ' ' . $semester_text
            ];

            // 4. Ambil Daftar Siswa (Logika disalin dari method Anda 'getRombelDetails')
            $siswaList = Siswa::where('rombongan_belajar_id', $rombel->rombongan_belajar_id)
                                ->select('nipd', 'nisn', 'nama')
                                ->orderBy('nama')
                                ->get();
            
            // 5. Ambil Daftar Mapel (Logika disalin dari method Anda 'getRombelDetails')
            $mapelData = json_decode($rombel->pembelajaran, true) ?: [];
            $mapelList = [];
            foreach ($mapelData as $mapel) {
                $mapelList[] = [
                    'id'   => $mapel['pembelajaran_id'],
                    'nama' => $mapel['nama_mata_pelajaran'] ?? 'Mapel Tanpa Nama'
                ];
            }

            // 6. Kirim semua data yang dibutuhkan modal
            return response()->json([
                'selected_rombel_id' => $rombel->id,       // ID internal rombel (cth: 123)
                'selected_siswa_nipd' => $siswa->nipd,     // ID siswa (cth: '11223')
                'semester' => $semesterData,
                'siswa' => $siswaList,                   // Daftar lengkap siswa di rombel itu
                'mapel' => $mapelList,                   // Daftar lengkap mapel di rombel itu
            ]);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | ========================================================================
    | BAGIAN KIOS HIBRIDA (BARU)
    | ========================================================================
    */

    /**
     * 1. Menampilkan halaman Kios Pencatatan Cepat.
     */
    public function kioskIndex()
    {
        // Ambil semua kategori dan poin pelanggaran untuk 'chip'
        $kategoriList = PelanggaranKategori::with('pelanggaranPoin')
                                         ->orderBy('nama')->get();
        
        // Kita akan pakai layout yang berbeda (misal 'layouts.kiosk')
        // Jika Anda ingin tetap pakai layout admin, ganti jadi 'admin.indisipliner.siswa.kiosk'
        return view('admin.indisipliner.siswa.kiosk.index', compact(
            'kategoriList'
        ));
    }

    /**
     * 2. API [GET] untuk mencari nama siswa berdasarkan QR Token.
     */
    public function kioskFindSiswa($qrToken)
    {
        $siswa = Siswa::where('qr_token', $qrToken)
                      ->select('nama', 'nama_rombel') // Ambil kolom 'nama_rombel' dari tabel siswas
                      ->first();

        if (!$siswa) {
            return response()->json(['error' => 'Siswa tidak ditemukan'], 404);
        }

        return response()->json([
            'nama_siswa' => $siswa->nama,
            'nama_rombel' => $siswa->nama_rombel // Kirim nama rombel
        ]);
    }

    /**
     * 3. API [POST] untuk menyimpan pelanggaran dari Kios.
     */
    public function kioskStore(Request $request)
    {
        // Validasi data yang masuk
        $request->validate([
            'qr_token' => 'required|string|exists:siswas,qr_token',
            'pelanggaran_ids' => 'required|array|min:1',
            'pelanggaran_ids.*' => 'integer|exists:pelanggaran_poin,ID', // Pastikan semua ID valid
        ]);

        try {
            // Ambil data siswa (kita butuh rombel_id, semester_id, nipd)
            $siswa = Siswa::where('qr_token', $request->qr_token)->first();
            if (!$siswa) {
                return response()->json(['error' => 'Siswa tidak valid.'], 404);
            }

            // Ambil detail Rombel (kita butuh ID internalnya)
            $rombel = Rombel::where('rombongan_belajar_id', $siswa->rombongan_belajar_id)
                            ->first();
            if (!$rombel) {
                return response()->json(['error' => 'Data rombel siswa tidak sinkron.'], 404);
            }

            // Ambil detail semua poin pelanggaran yang dipilih
            $poinDetails = PelanggaranPoin::whereIn('ID', $request->pelanggaran_ids)
                                         ->pluck('poin', 'ID'); // Hasil: [ 'id' => 'poin' ]

            $dataToInsert = [];
            $totalPoin = 0;
            $now = now(); // Waktu kejadian

            // Siapkan data untuk 'bulk insert'
            foreach ($request->pelanggaran_ids as $pelanggaran_id) {
                $poin = $poinDetails->get($pelanggaran_id);
                $totalPoin += $poin;
                
                $dataToInsert[] = [
                    'nipd' => $siswa->nipd,
                    'semester_id' => $rombel->semester_id, // Ambil dari rombel
                    'rombongan_belajar_id' => $rombel->id, // ID internal rombel
                    'IDpelanggaran_poin' => $pelanggaran_id,
                    'tanggal' => $now->toDateString(),
                    'jam' => $now->toTimeString(),
                    'poin' => $poin,
                    'pembelajaran' => null, // Kios diasumsikan di luar jam
                ];
            }

            // Simpan semua data sekaligus dalam satu transaksi
            DB::transaction(function () use ($dataToInsert) {
                PelanggaranNilai::insert($dataToInsert);
            });
            
            // Kirim respon sukses
            return response()->json([
                'status' => 'success',
                'message' => 'Data pelanggaran berhasil disimpan.',
                'siswa_nama' => $siswa->nama,
                'siswa_rombel' => $siswa->nama_rombel,
                'jumlah_pelanggaran' => count($dataToInsert),
                'total_poin' => $totalPoin,
                'waktu' => $now->format('H:i')
            ]);

        } catch (Exception $e) {
            return response()->json(['error' => 'Gagal menyimpan data: ' . $e->getMessage()], 500);
        }
    }
}