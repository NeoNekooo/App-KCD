<?php

use Illuminate\Support\Facades\Route;
use App\Models\SyaratPendaftaran; // <---- tambahkan baris ini

// Controller Utama
use App\Http\Controllers\TugasPegawaiController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\SekolahController;
use App\Http\Controllers\GtkController;
use App\Http\Controllers\LandingPpdbController;

// Controller Akademik
use App\Http\Controllers\Admin\Akademik\SemesterController;
use App\Http\Controllers\Admin\Akademik\TapelController;

// Controller Kesiswaan
use App\Http\Controllers\Admin\Kesiswaan\ppdb\DaftarPesertaDidikBaruController;
use App\Http\Controllers\Admin\Kesiswaan\ppdb\DaftarCalonPesertaDidikController;
use App\Http\Controllers\Admin\Kesiswaan\ppdb\PemberianNisController;
use App\Http\Controllers\Admin\Kesiswaan\ppdb\FormulirPendaftaranController;
use App\Http\Controllers\Admin\Kesiswaan\ppdb\JalurController;
use App\Http\Controllers\Admin\Kesiswaan\ppdb\TingkatPendaftaranController;
use App\Http\Controllers\Admin\Kesiswaan\ppdb\LaporanPendaftaranController;
use App\Http\Controllers\Admin\Kesiswaan\ppdb\LaporanQuotaController;
use App\Http\Controllers\Admin\Kesiswaan\ppdb\PenempatanKelasController;
use App\Http\Controllers\Admin\Kesiswaan\ppdb\QuotaController;
use App\Http\Controllers\Admin\Kesiswaan\ppdb\SyaratController;
use App\Http\Controllers\Admin\Kesiswaan\ppdb\TahunPpdbController;

use App\Http\Controllers\Admin\Landing\PpdbController;

use App\Http\Controllers\Admin\Settings\ApiSettingsController; // Pastikan ini di-import


/*
|--------------------------------------------------------------------------
| Rute Publik
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('admin.dashboard');
});


Route::post('/submitForm', [LandingPpdbController::class, 'submitForm'])->name('submitForm');


/*
|--------------------------------------------------------------------------
| Rute Panel Ppdb
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
| Rute Panel Admin
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::prefix('pengaturan')->name('pengaturan.')->group(function() {
        Route::get('/sekolah', [SekolahController::class, 'index'])->name('sekolah.index');

        Route::prefix('webservice')->name('webservice.')->group(function () {
            Route::get('/', [ApiSettingsController::class, 'index'])->name('index');
        });
    });

    Route::prefix('kepegawaian')->name('kepegawaian.')->group(function() {
        Route::get('/gtk/export/excel', [GtkController::class, 'exportExcel'])->name('gtk.export.excel');
        Route::resource('gtk', GtkController::class);
        Route::resource('tugas-pegawai', TugasPegawaiController::class)->except(['create', 'edit', 'show']);
});

    // --- GRUP AKADEMIK ---
    Route::prefix('akademik')->name('akademik.')->group(function () {
        Route::resource('tapel', TapelController::class)->only(['index', 'store', 'destroy']);
        Route::patch('tapel/{tapel}/toggle', [TapelController::class, 'toggleStatus'])->name('tapel.toggle');
        Route::resource('semester', SemesterController::class)->only(['index']);
        Route::patch('semester/{semester}/toggle', [SemesterController::class, 'toggle'])->name('semester.toggle');
    });

    /*
    |--------------------------------------------------------------------------
    | Kesiswaan
    |--------------------------------------------------------------------------
    */
    Route::prefix('kesiswaan')->name('kesiswaan.')->group(function() {
        Route::resource('siswa', SiswaController::class);
        
        /*
        |--------------------------------------------------------------------------
        | Kesiswaan (PPDB)
        |--------------------------------------------------------------------------
        */
        Route::prefix('ppdb')->name('ppdb.')->group(function () {
            // Tahun Pelajaran PPDB
            Route::resource('tahun-ppdb', TahunPpdbController::class);
            Route::post('/tahun-ppdb/{id}/toggle-active', [TahunPpdbController::class, 'toggleActive'])->name('tahun-ppdb.toggleActive');

            // Jalur Pendaftaran
            Route::resource('jalur-ppdb', JalurController::class);
            Route::post('/jalur-ppdb/{id}/toggle-active', [JalurController::class, 'toggleActive'])->name('jalur-ppdb.toggleActive');

            // Tingkat Pendaftaran
            Route::resource('tingkat-ppdb', TingkatPendaftaranController::class);
            Route::post('/tingkat-ppdb/{id}/toggle-active', [TingkatPendaftaranController::class, 'toggleActive'])->name('tingkat-ppdb.toggleActive');

            // Quota Pendaftaran
            Route::resource('quota-ppdb', QuotaController::class);

            // Syarat Pendaftaran
            Route::resource('syarat-ppdb', SyaratController::class);
            Route::post('/syarat-ppdb/{id}/toggle-active', [SyaratController::class, 'toggleActive'])->name('syarat-ppdb.toggleActive');

            // Formulir Pendaftaran
            Route::resource('formulir-ppdb', FormulirPendaftaranController::class);
            Route::get('/get-syarat/{jalurId}', [SyaratController::class, 'getByJalur'])->name('admin.kesiswaan.ppdb.get-syarat');
            Route::patch('update-status/{id}', [FormulirPendaftaranController::class, 'updateStatus'])->name('updateStatus');

            // Pemberian NIS
            Route::get('pemberian-nis/generate', 
                [PemberianNisController::class, 'generate']
            )->name('pemberian-nis.generate');
            Route::resource('pemberian-nis', PemberianNisController::class);

            Route::get('daftar-calon/resi/{id}', [DaftarCalonPesertaDidikController::class, 'resi'])->name('daftar_calon.resi');

            // Data Peserta Didik
            Route::resource('daftar-calon-peserta-didik', DaftarCalonPesertaDidikController::class);
            Route::resource('daftar-peserta-didik-baru', DaftarPesertaDidikBaruController::class);

            // Penempatan Kelas
            Route::resource('penempatan-kelas', PenempatanKelasController::class);
            Route::post('penempatan-kelas/update-kelas', 
                [PenempatanKelasController::class, 'updateKelas']
            )->name('penempatan-kelas.update-kelas');


            // Laporan
            Route::resource('laporan-pendaftaran', LaporanPendaftaranController::class);
            Route::resource('laporan-quota', LaporanQuotaController::class);


        });

    });

    /*
        |--------------------------------------------------------------------------
        | Kesiswaan (PPDB)
        |--------------------------------------------------------------------------
        */
    Route::prefix('ppdb')->name('ppdb.')->group(function () {

        Route::resource('landing', PpdbController::class);

        Route::post('submit', [PpdbController::class, 'submitForm'])->name('submit');
    });
});
