<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
// --- Controller dari V1 ---
use App\Http\Controllers\Admin\Settings\ApiSettingsController;
use App\Http\Controllers\Admin\Settings\SekolahController;
use App\Http\Controllers\Admin\Kepegawaian\GtkController;
use App\Http\Controllers\Admin\Kepegawaian\TugasPegawaiController;

// Controller Akademik
use App\Http\Controllers\Admin\Akademik\TapelController;
use App\Http\Controllers\Admin\Akademik\JurusanController;
use App\Http\Controllers\Admin\Akademik\MapelController; 
use App\Http\Controllers\Admin\Akademik\EkstrakurikulerController;

/*
|--------------------------------------------------------------------------
| Rute Web Utama
|--------------------------------------------------------------------------
*/

// Menggunakan 'welcome' dari V2
Route::get('/', function () {
    return view('welcome');
});

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
         ->middleware('auth')
         ->name('logout');
/*
|--------------------------------------------------------------------------
| Grup Rute untuk Panel Admin
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {

    // Dashboard (dari V1)
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // --- GRUP PENGATURAN --- (dari V1)
    Route::prefix('pengaturan')->name('pengaturan.')->group(function () {
        Route::get('sekolah', [SekolahController::class, 'index'])->name('sekolah.index');
        Route::put('sekolah', [SekolahController::class, 'update'])->name('sekolah.update');
        Route::get('/webservice', [ApiSettingsController::class, 'index'])->name('webservice.index');
    });

    // --- GRUP KEPEGAWAIAN --- (Struktur dari V1)
    Route::prefix('kepegawaian')->name('kepegawaian.')->group(function () {
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
        Route::get('gtk/cetak-pdf-multiple', [GtkController::class, 'cetakPdfMultiple'])
            ->name('gtk.cetak_pdf_multiple');

        // --- RUTE BARU UNTUK UPLOAD FOTO & TTD ---
        Route::post('gtk/{id}/upload-media', [GtkController::class, 'uploadMedia'])->name('gtk.upload_media');

        // Route untuk Tugas Pegawai
        Route::resource('tugas-pegawai', TugasPegawaiController::class)->except(['create', 'edit', 'show']);
    });

    // --- GRUP AKADEMIK ---
    Route::prefix('akademik')->name('akademik.')->group(function () {
        Route::get('tapel', [TapelController::class, 'index'])->name('tapel.index');
        Route::get('tapel/sinkron', [TapelController::class, 'sinkron'])->name('tapel.sinkron');
        Route::post('tapel/aktif/{id}', [TapelController::class, 'setAktif'])->name('tapel.aktif');
        Route::resource('jurusan', JurusanController::class)->only(['index']);
        Route::get('mapel', [MapelController::class, 'index'])->name('mapel.index'); 
        Route::get('/ekstrakurikuler', [EkstrakurikulerController::class, 'index'])->name('ekskul.index');
    });

}); // Akhir dari grup 'admin'


// Menggunakan file auth standar dari V2
require __DIR__ . '/auth.php';