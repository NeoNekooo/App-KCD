<?php

use Illuminate\Support\Facades\Route;

// Controller Settings
use App\Http\Controllers\Admin\Settings\ApiSettingsController;
use App\Http\Controllers\Admin\Settings\SekolahController;

// Controller Kepegawaian
use App\Http\Controllers\Admin\Kepegawaian\GtkController;
use App\Http\Controllers\Admin\Kepegawaian\TugasPegawaiController;

// Controller Akademik
use App\Http\Controllers\Admin\Akademik\SemesterController;
use App\Http\Controllers\Admin\Akademik\TapelController;
use App\Http\Controllers\Admin\Akademik\ProgramKeahlianController;
use App\Http\Controllers\Admin\Akademik\PaketKeahlianController; 
use App\Http\Controllers\Admin\Akademik\JurusanController;

// Controller Kesiswaan
use App\Http\Controllers\Admin\Kesiswaan\DaftarCalonPesertaDidikController;
use App\Http\Controllers\Admin\Kesiswaan\FormulirPendaftaranController;
use App\Http\Controllers\Admin\Kesiswaan\JalurController;
use App\Http\Controllers\Admin\Kesiswaan\LaporanPendaftaranController;
use App\Http\Controllers\Admin\Kesiswaan\LaporanQuotaController;
use App\Http\Controllers\Admin\Kesiswaan\PenempatanKelasController;
use App\Http\Controllers\Admin\Kesiswaan\QuotaController;
use App\Http\Controllers\Admin\Kesiswaan\SiswaController;
use App\Http\Controllers\Admin\Kesiswaan\SyaratController;
use App\Http\Controllers\Admin\Kesiswaan\TahunPpdbController;


// Controller Rombongan Belajar
use App\Http\Controllers\Admin\Rombel\RombelRegulerController;
use App\Http\Controllers\Admin\Rombel\RombelPraktikController;
use App\Http\Controllers\Admin\Rombel\RombelEkstrakurikulerController;
use App\Http\Controllers\Admin\Rombel\RombelMapelPilihanController;
use App\Http\Controllers\Admin\Rombel\RombelWaliController;

/*
|--------------------------------------------------------------------------
| Rute Web Utama
|--------------------------------------------------------------------------
*/

// Menggunakan 'welcome' dari V2
Route::get('/', function () {
    return view('admin.dashboard');
});

/*
|--------------------------------------------------------------------------
| Rute Panel Admin (Prefix 'admin')
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () { return view('admin.dashboard'); })->name('dashboard');

    // --- GRUP PENGATURAN ---
    Route::prefix('pengaturan')->name('pengaturan.')->group(function() {

        Route::get('/profil_sekolah', [ProfilSekolahController::class, 'edit'])->name('profil_sekolah.edit');
        Route::put('/profil_sekolah', [ProfilSekolahController::class, 'update'])->name('profil_sekolah.update');
        Route::prefix('webservice')->name('webservice.')->group(function () {
            Route::get('/', [ApiSettingsController::class, 'index'])->name('index');
        });
    });

    // --- GRUP KEPEGAWAIAN ---
    Route::prefix('kepegawaian')->name('kepegawaian.')->group(function() {
        Route::resource('pegawai', PegawaiController::class);

       Route::get('sekolah', [SekolahController::class, 'index'])->name('sekolah.index');
    Route::put('sekolah', [SekolahController::class, 'update'])->name('sekolah.update');
        Route::get('/webservice', [ApiSettingsController::class, 'index'])->name('webservice.index');
    });

    // --- GRUP KEPEGAWAIAN ---
     Route::prefix('kepegawaian')->name('kepegawaian.')->group(function() {
        // Route untuk Guru
        Route::prefix('guru')->name('guru.')->controller(GtkController::class)->group(function () {
            Route::get('/', 'indexGuru')->name('index');
            Route::get('/export/excel', 'exportGuruExcel')->name('export.excel');
        });

        // Route untuk Tenaga Kependidikan
        Route::prefix('tenaga-kependidikan')->name('tendik.')->controller(GtkController::class)->group(function () {
            Route::get('/', 'indexTendik')->name('index');
            Route::get('/export/excel', 'exportTendikExcel')->name('export.excel');
        });

        // Route untuk detail multi-GTK
        Route::get('/gtk/show-multiple', [GtkController::class, 'showMultiple'])->name('gtk.show-multiple');
        Route::get('gtk/cetak-pdf/{id}', [GtkController::class, 'cetakPdf'])->name('gtk.cetak_pdf');
        // Route untuk Tugas Pegawai

        Route::resource('tugas-pegawai', TugasPegawaiController::class)->except(['create', 'edit', 'show']);
    });


    // --- GRUP AKADEMIK ---
    Route::prefix('akademik')->name('akademik.')->group(function () {
        Route::resource('tapel', TapelController::class)->only(['index', 'store', 'destroy']);
        Route::patch('tapel/{tapel}/toggle', [TapelController::class, 'toggleStatus'])->name('tapel.toggle');
        Route::resource('semester', SemesterController::class)->only(['index']);
        Route::patch('semester/{semester}/toggle', [SemesterController::class, 'toggle'])->name('semester.toggle');
        Route::resource('program-keahlian', ProgramKeahlianController::class)->only(['index']);
        Route::resource('paket-keahlian', PaketKeahlianController::class)->only(['index']);
        Route::resource('jurusan', JurusanController::class)->only(['index']);
    });

    // --- GRUP KESISWAAN ---
    Route::prefix('kesiswaan')->name('kesiswaan.')->group(function() {
        Route::resource('siswa', SiswaController::class);

        Route::prefix('ppdb')->name('ppdb.')->group(function () {
            Route::resource('tahun-ppdb', TahunPpdbController::class);
            Route::post('/tahun-ppdb/{id}/toggle-active', [TahunPpdbController::class, 'toggleActive'])->name('tahun-ppdb.toggleActive');
            
            Route::resource('jalur-ppdb', JalurController::class);
            Route::post('/jalur-ppdb/{id}/toggle-active', [JalurController::class, 'toggleActive'])->name('jalur-ppdb.toggleActive');

            Route::resource('quota-ppdb', QuotaController::class);

            Route::resource('syarat-ppdb', SyaratController::class);
            Route::post('/syarat-ppdb/{id}/toggle-active', [SyaratController::class, 'toggleActive'])->name('syarat-ppdb.toggleActive');

            Route::resource('formulir-ppdb', FormulirPendaftaranController::class);
            Route::resource('daftar-calon-peserta-didik', DaftarCalonPesertaDidikController::class);
            Route::resource('penempatan-kelas', PenempatanKelasController::class);
            Route::resource('laporan-pendaftaran', LaporanPendaftaranController::class);
            Route::resource('laporan-quota', LaporanQuotaController::class);
        });

    });

    // --- GRUP ROMBONGAN BELAJAR ---
    Route::prefix('rombel')->name('rombel.')->group(function () {
        // Reguler
        Route::get('/reguler/create', [RombelRegulerController::class, 'create'])->name('reguler.create');
        Route::get('/reguler', [RombelRegulerController::class, 'index'])->name('reguler.index');

        // Praktik (ROUTE BARU DITAMBAHKAN)
        Route::get('/praktik/create', [RombelPraktikController::class, 'create'])->name('praktik.create');
        Route::get('/praktik', [RombelPraktikController::class, 'index'])->name('praktik.index');

        // Ekstrakurikuler (ROUTE BARU DITAMBAHKAN)
        Route::get('/ekstrakurikuler/create', [RombelEkstrakurikulerController::class, 'create'])->name('ekstrakurikuler.create');
        Route::get('/ekstrakurikuler', [RombelEkstrakurikulerController::class, 'index'])->name('ekstrakurikuler.index');

        // Mapel Pilihan (ROUTE BARU DITAMBAHKAN)
        Route::get('/mapel-pilihan/create', [RombelMapelPilihanController::class, 'create'])->name('mapel-pilihan.create');
        Route::get('/mapel-pilihan', [RombelMapelPilihanController::class, 'index'])->name('mapel-pilihan.index');

        // Wali (ROUTE BARU DITAMBAHKAN)
        Route::get('/wali/create', [RombelWaliController::class, 'create'])->name('wali.create');
        Route::get('/wali', [RombelWaliController::class, 'index'])->name('wali.index');
    });
});

require __DIR__.'/auth.php';