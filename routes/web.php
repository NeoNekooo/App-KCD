<?php

use Illuminate\Support\Facades\Route;
use App\Models\SyaratPendaftaran; // <---- tambahkan baris ini

// --- CONTROLLER UTAMA ---
use App\Http\Controllers\ProfilSekolahController;

// --- AKADEMIK ---
use App\Http\Controllers\Admin\Akademik\SemesterController;
use App\Http\Controllers\Admin\Akademik\TapelController;
use App\Http\Controllers\Admin\Akademik\ProgramKeahlianController; // Unused, but kept for clarity
use App\Http\Controllers\Admin\Akademik\PaketKeahlianController; // Unused, but kept for clarity
use App\Http\Controllers\Admin\Akademik\JurusanController;
use App\Http\Controllers\Admin\Akademik\MapelController;
use App\Http\Controllers\Admin\Akademik\EkstrakurikulerController;
use App\Http\Controllers\Admin\Akademik\JadwalPelajaranController;

// --- KESISWAAN/PPDB ---
use App\Http\Controllers\Admin\Kesiswaan\SiswaController;
use App\Http\Controllers\Admin\Kesiswaan\ppdb\DaftarCalonPesertaDidikController;
use App\Http\Controllers\Admin\Kesiswaan\ppdb\FormulirPendaftaranController;
use App\Http\Controllers\Admin\Kesiswaan\ppdb\JalurController;
use App\Http\Controllers\Admin\Kesiswaan\ppdb\LaporanPendaftaranController;
use App\Http\Controllers\Admin\Kesiswaan\ppdb\LaporanQuotaController;
use App\Http\Controllers\Admin\Kesiswaan\ppdb\PenempatanKelasController;
use App\Http\Controllers\Admin\Kesiswaan\ppdb\QuotaController;
use App\Http\Controllers\Admin\Kesiswaan\ppdb\SyaratController;
use App\Http\Controllers\Admin\Kesiswaan\ppdb\TahunPpdbController;
use App\Http\Controllers\Admin\Kesiswaan\ppdb\DaftarPesertaDidikBaruController;
use App\Http\Controllers\Admin\Kesiswaan\ppdb\PemberianNisController;
use App\Http\Controllers\Admin\Kesiswaan\ppdb\TingkatPendaftaranController;

// --- INDISIPLINER ---
use App\Http\Controllers\Admin\Indisipliner\IndisiplinerSiswaController;

// --- ABSENSI & PENGATURAN ---
use App\Http\Controllers\Guru\GuruAbsensiController;
use App\Http\Controllers\Admin\Absensi\AbsensiSiswaController;
use App\Http\Controllers\Admin\Absensi\IzinSiswaController;
use App\Http\Controllers\Admin\Pengaturan\HariLiburController;
use App\Http\Controllers\Admin\Pengaturan\PengaturanAbsensiController;
use App\Http\Controllers\Admin\Laporan\LaporanAbsensiController;

// --- PENGATURAN LAINNYA ---
use App\Http\Controllers\Admin\Settings\ApiSettingsController;
use App\Http\Controllers\Admin\Settings\SekolahController;

// --- KEPEGAWAIAN (Consolidated) ---
use App\Http\Controllers\Admin\Kepegawaian\GtkController; // Assuming GtkController handles both Guru and Tendik

// --- LANDING/PPDB ---
use App\Http\Controllers\LandingPpdbController;

// --- ROMBEL (Rombongan Belajar) ---
use App\Http\Controllers\Admin\Rombel\RombelRegulerController;
use App\Http\Controllers\Admin\Rombel\RombelPraktikController;
use App\Http\Controllers\Admin\Rombel\RombelEkstrakurikulerController;
use App\Http\Controllers\Admin\Rombel\RombelMapelPilihanController;
use App\Http\Controllers\Admin\Rombel\RombelWaliController;


use App\Http\Controllers\Admin\Landing\PpdbController;


/*
|--------------------------------------------------------------------------
| Rute Web Utama
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Rute PPDB Landing Page (Public)
|--------------------------------------------------------------------------
*/
Route::prefix('ppdb')->name('ppdb.')->group(function() {
    Route::get('/', [LandingPpdbController::class, 'beranda'])->name('beranda');
    Route::get('/kompetensi-keahlian', [LandingPpdbController::class, 'kompetensiKeahlian'])->name('kompetensiKeahlian');
    Route::get('/daftar-calon-siswa', [LandingPpdbController::class, 'daftarCalonSiswa'])->name('daftarCalonSiswa');
    Route::get('/formulir-pendaftaran', [LandingPpdbController::class, 'formulirPendaftaran'])->name('formulirPendaftaran');
    Route::get('/kontak', [LandingPpdbController::class, 'kontak'])->name('kontak');

    Route::get('/api/syarat-by-jalur/{jalurId}', function ($jalurId) {
        return SyaratPendaftaran::where('is_active', true)
            ->where('jalurPendaftaran_id', $jalurId)
            ->select('id', 'syarat', 'is_active')
            ->get();
    });


    // Tambahkan route POST untuk submit form pendaftaran
    Route::post('/formulir-pendaftaran/store', [LandingPpdbController::class, 'formulirStore'])->name('formulir.store');
});

/*
|--------------------------------------------------------------------------
| Rute Panel Guru (Authenticated)
|--------------------------------------------------------------------------
*/
Route::prefix('guru')->middleware(['auth'])->name('guru.')->group(function () {
    Route::get('/absensi', [GuruAbsensiController::class, 'index'])->name('absensi.index');
    Route::get('/absensi/kelas/{jadwal}', [GuruAbsensiController::class, 'show'])->name('absensi.show');
    Route::post('/absensi/kelas', [GuruAbsensiController::class, 'store'])->name('absensi.store');
});

/*
|--------------------------------------------------------------------------
| Rute Panel Admin (Prefix 'admin')
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () { // Added middleware('auth') assuming admin routes are protected
    // Dashboard
      Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // --- GRUP LAPORAN ---
    Route::prefix('laporan')->name('laporan.')->group(function() {
        Route::get('absensi', [LaporanAbsensiController::class, 'index'])->name('absensi.index');
        Route::get('absensi/dashboard', [LaporanAbsensiController::class, 'dashboard'])->name('absensi.dashboard');
        Route::get('absensi/export', [LaporanAbsensiController::class, 'export'])->name('absensi.export');
        Route::get('absensi/tanpa-pulang', [LaporanAbsensiController::class, 'laporanTanpaPulang'])->name('absensi.tanpa_pulang');
    });

    // --- GRUP ABSENSI ---
    Route::prefix('absensi')->name('absensi.')->group(function () {
        // Grup Absensi Siswa
        Route::prefix('siswa')->name('siswa.')->group(function() {
            Route::get('/', [AbsensiSiswaController::class, 'index'])->name('index');
            Route::get('/form', [AbsensiSiswaController::class, 'show'])->name('show_form');
            Route::post('/', [AbsensiSiswaController::class, 'store'])->name('store');
            Route::get('/scanner', [AbsensiSiswaController::class, 'showScanner'])->name('show_scanner');
            Route::post('/handle-scan', [AbsensiSiswaController::class, 'handleScan'])->name('handle_scan');
            Route::get('/todays-scans', [AbsensiSiswaController::class, 'getTodaysScans'])->name('get_todays_scans');
        });
        // Resource Izin Siswa
        Route::resource('izin-siswa', IzinSiswaController::class);
    });

    // --- GRUP PENGATURAN ---
    Route::prefix('pengaturan')->name('pengaturan.')->group(function() {
        // Pengaturan Absensi
        Route::get('absensi', [PengaturanAbsensiController::class, 'edit'])->name('absensi.edit');
        Route::put('absensi', [PengaturanAbsensiController::class, 'update'])->name('absensi.update');
        // Hari Libur
        Route::resource('hari-libur', HariLiburController::class)->except(['show', 'edit', 'update']);
        // Profil Sekolah
        Route::get('/profil_sekolah', [ProfilSekolahController::class, 'edit'])->name('profil_sekolah.edit');
        Route::put('/profil_sekolah', [ProfilSekolahController::class, 'update'])->name('profil_sekolah.update');
        // Data Sekolah
        Route::get('/sekolah', [SekolahController::class, 'index'])->name('sekolah.index');
        Route::put('/sekolah', [SekolahController::class, 'update'])->name('sekolah.update');
        // Web Service / API Settings
        Route::get('/webservice', [ApiSettingsController::class, 'index'])->name('webservice.index');
    });

    // --- GRUP KEPEGAWAIAN (Consolidated) ---
    Route::prefix('kepegawaian')->name('kepegawaian.')->group(function() {
        // Consolidated GtkController for Guru/Tendik based on menu structure
        Route::get('/guru', [GtkController::class, 'indexGuru'])->name('guru.index'); // <-- Rute GET Kustom
    Route::get('/guru/export/excel', [GtkController::class, 'exportGuruExcel'])->name('guru.export.excel');
       Route::get('/tendik', [GtkController::class, 'indexTendik'])->name('tendik.index'); // <-- Rute GET Kustom
    Route::get('/tendik/export/excel', [GtkController::class, 'exportTendikExcel'])->name('tendik.export.excel');

Route::get('/gtk/multiple-show', [GtkController::class, 'showMultiple'])
    ->name('gtk.show-multiple')
    ;
    });

    // --- GRUP AKADEMIK (Consolidated) ---
    Route::prefix('akademik')->name('akademik.')->group(function () {
        // Tapel (Tahun Pelajaran) - Consolidated all Tapel routes
        Route::resource('tapel', TapelController::class)->except(['show', 'edit', 'update']);
        Route::get('tapel/sinkron', [TapelController::class, 'sinkron'])->name('tapel.sinkron');
        Route::post('tapel/aktif/{id}', [TapelController::class, 'setAktif'])->name('tapel.aktif');
        Route::patch('tapel/{tapel}/toggle', [TapelController::class, 'toggleStatus'])->name('tapel.toggle');

        // Semester (Incomplete resource, kept as is)
        Route::resource('semester', SemesterController::class)->only(['index']);
        Route::patch('semester/{semester}/toggle', [SemesterController::class, 'toggle'])->name('semester.toggle');

        // Kurikulum/Struktur (Incomplete resources, kept as is)
        Route::resource('program-keahlian', ProgramKeahlianController::class)->only(['index']);
        Route::resource('paket-keahlian', PaketKeahlianController::class)->only(['index']);
        Route::resource('jurusan', JurusanController::class)->only(['index']);

        // Mapel (Using resource->only for cleaner code)
        Route::resource('mapel', MapelController::class)->only(['index']); // Replaced manual get route

        // Ekstrakurikuler (Using resource->only for cleaner code)
Route::resource('ekstrakurikuler', EkstrakurikulerController::class)
    ->only(['index'])
    ->names(['index' => 'ekskul.index']); // Use names() to map specific actions
        // Jadwal Pelajaran
        Route::get('jadwal', [JadwalPelajaranController::class, 'index'])->name('jadwal.index');
        Route::get('jadwal/{rombel}/edit', [JadwalPelajaranController::class, 'edit'])->name('jadwal.edit');
        Route::post('jadwal', [JadwalPelajaranController::class, 'store'])->name('jadwal.store');
        Route::delete('jadwal/{jadwal}', [JadwalPelajaranController::class, 'destroy'])->name('jadwal.destroy');
    });

    // --- GRUP KESISWAAN ---
    Route::prefix('kesiswaan')->name('kesiswaan.')->group(function() {
        // Siswa
        Route::get('/siswa/{siswa}/cetak-kartu', [SiswaController::class, 'cetakKartu'])->name('siswa.cetak_kartu');
        Route::get('/cetak-kartu-massal', [SiswaController::class, 'showCetakMassalIndex'])->name('siswa.cetak_massal_index');
        Route::get('/cetak-kartu-massal/{rombel}', [SiswaController::class, 'cetakKartuMassal'])->name('siswa.cetak_massal_show');
        Route::resource('siswa', SiswaController::class);

        // PPDB (Penerimaan Peserta Didik Baru)
        Route::prefix('ppdb')->name('ppdb.')->group(function () {
            // Pengaturan
            Route::resource('tahun-ppdb', TahunPpdbController::class);
            Route::post('/tahun-ppdb/{id}/toggle-active', [TahunPpdbController::class, 'toggleActive'])->name('tahun-ppdb.toggleActive');
            Route::resource('jalur-ppdb', JalurController::class);
            Route::post('/jalur-ppdb/{id}/toggle-active', [JalurController::class, 'toggleActive'])->name('jalur-ppdb.toggleActive');
            Route::resource('tingkat-ppdb', TingkatPendaftaranController::class);
            Route::resource('quota-ppdb', QuotaController::class);
            Route::resource('syarat-ppdb', SyaratController::class);
            Route::post('/syarat-ppdb/{id}/toggle-active', [SyaratController::class, 'toggleActive'])->name('syarat-ppdb.toggleActive');

            // Pendaftaran & Proses
            Route::resource('formulir-ppdb', FormulirPendaftaranController::class);
            Route::get('/get-syarat/{jalurId}', [SyaratController::class, 'getByJalur'])->name('get-syarat');
            Route::patch('update-status/{id}', [FormulirPendaftaranController::class, 'updateStatus'])->name('updateStatus');
            Route::get('pemberian-nis/generate', [PemberianNisController::class, 'generate'])->name('pemberian-nis.generate');
            Route::resource('pemberian-nis', PemberianNisController::class);

            // Data Calon & Peserta Didik
            Route::get('daftar-calon/resi/{id}', [DaftarCalonPesertaDidikController::class, 'resi'])->name('daftar_calon.resi');
            Route::resource('daftar-calon-peserta-didik', DaftarCalonPesertaDidikController::class);
            Route::resource('daftar-peserta-didik-baru', DaftarPesertaDidikBaruController::class);

            // Penempatan Kelas
            Route::resource('penempatan-kelas', PenempatanKelasController::class);
            Route::post('penempatan-kelas/update-kelas', [PenempatanKelasController::class, 'updateKelas'])->name('penempatan-kelas.update-kelas');

            // Laporan PPDB
            Route::resource('laporan-pendaftaran', LaporanPendaftaranController::class);
            Route::resource('laporan-quota', LaporanQuotaController::class);
        });
    });
    
    // --- GRUP ROMBONGAN BELAJAR ---
    Route::prefix('rombel')->name('rombel.')->group(function () {
        // All routes kept as they are custom index/create views
        Route::get('/reguler', [RombelRegulerController::class, 'index'])->name('reguler.index');
        Route::get('/reguler/create', [RombelRegulerController::class, 'create'])->name('reguler.create');
        Route::get('/praktik', [RombelPraktikController::class, 'index'])->name('praktik.index');
        Route::get('/praktik/create', [RombelPraktikController::class, 'create'])->name('praktik.create');
        Route::get('/ekstrakurikuler', [RombelEkstrakurikulerController::class, 'index'])->name('ekstrakurikuler.index');
        Route::get('/ekstrakurikuler/create', [RombelEkstrakurikulerController::class, 'create'])->name('ekstrakurikuler.create');
        Route::get('/mapel-pilihan', [RombelMapelPilihanController::class, 'index'])->name('mapel-pilihan.index');
        Route::get('/mapel-pilihan/create', [RombelMapelPilihanController::class, 'create'])->name('mapel-pilihan.create');
        Route::get('/wali', [RombelWaliController::class, 'index'])->name('wali.index');
        Route::get('/wali/create', [RombelWaliController::class, 'create'])->name('wali.create');
    });

    // --- GRUP INDISIPLINER SISWA ---
    Route::prefix('indisipliner-siswa')->name('indisipliner.siswa.')->group(function () {
        // Pengaturan
        Route::get('pengaturan', [IndisiplinerSiswaController::class, 'pengaturanIndex'])->name('pengaturan.index');
        Route::post('pengaturan/kategori', [IndisiplinerSiswaController::class, 'storeKategori'])->name('pengaturan.kategori.store');
        Route::put('pengaturan/kategori/{pelanggaranKategori}', [IndisiplinerSiswaController::class, 'updateKategori'])->name('pengaturan.kategori.update');
        Route::delete('pengaturan/kategori/{pelanggaranKategori}', [IndisiplinerSiswaController::class, 'destroyKategori'])->name('pengaturan.kategori.destroy');
        Route::post('pengaturan/poin', [IndisiplinerSiswaController::class, 'storePoin'])->name('pengaturan.poin.store');
        Route::put('pengaturan/poin/{pelanggaranPoin}', [IndisiplinerSiswaController::class, 'updatePoin'])->name('pengaturan.poin.update');
        Route::delete('pengaturan/poin/{pelanggaranPoin}', [IndisiplinerSiswaController::class, 'destroyPoin'])->name('pengaturan.poin.destroy');
        Route::post('pengaturan/sanksi', [IndisiplinerSiswaController::class, 'storeSanksi'])->name('pengaturan.sanksi.store');
        Route::put('pengaturan/sanksi/{pelanggaranSanksi}', [IndisiplinerSiswaController::class, 'updateSanksi'])->name('pengaturan.sanksi.update');
        Route::delete('pengaturan/sanksi/{pelanggaranSanksi}', [IndisiplinerSiswaController::class, 'destroySanksi'])->name('pengaturan.sanksi.destroy');

        // Daftar Pelanggaran
        Route::get('daftar', [IndisiplinerSiswaController::class, 'daftarIndex'])->name('daftar.index');
        Route::get('daftar/input', [IndisiplinerSiswaController::class, 'createPelanggaran'])->name('daftar.create');
        Route::post('daftar', [IndisiplinerSiswaController::class, 'storePelanggaran'])->name('daftar.store');
        Route::delete('daftar/{pelanggaranNilai}', [IndisiplinerSiswaController::class, 'destroyPelanggaran'])->name('daftar.destroy');

        // Rekapitulasi & Utilities
        Route::get('get-siswa-by-rombel/{rombel}', [IndisiplinerSiswaController::class, 'getSiswaByRombel'])->name('getSiswaByRombel');
        Route::get('rekapitulasi', [IndisiplinerSiswaController::class, 'rekapitulasiIndex'])->name('rekapitulasi.index');
    });

    Route::prefix('ppdb')->name('ppdb.')->group(function () {
    
        Route::resource('landing', PpdbController::class);
        
        Route::post('submit', [PpdbController::class, 'submitForm'])->name('submit');
    });
});



require __DIR__.'/auth.php';
