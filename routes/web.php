<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// --- Controller Utama ---
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\InstansiController; // <--- TAMBAHAN BARU

// --- Controller Monitoring ---
use App\Http\Controllers\Admin\Sekolah\SekolahController as SekolahMonitoringController;
use App\Http\Controllers\Admin\Kepegawaian\GtkController;
use App\Http\Controllers\Admin\Kesiswaan\SiswaController;

// --- Controller Administrasi ---
use App\Http\Controllers\Admin\Administrasi\TipeSuratController;
use App\Http\Controllers\Admin\Administrasi\SuratKeluarSiswaController;
use App\Http\Controllers\Admin\Administrasi\SuratKeluarGuruController;
use App\Http\Controllers\Admin\Administrasi\SuratMasukController;
use App\Http\Controllers\Admin\Administrasi\NomorSuratSettingController;
use App\Http\Controllers\Admin\Administrasi\ArsipSuratController;

/*
|--------------------------------------------------------------------------
| Rute Web Utama
|--------------------------------------------------------------------------
*/

// --- LOGIKA AUTH ---
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('admin.dashboard');
    }
    return view('auth.login-custom');
})->name('landing');

/*
|--------------------------------------------------------------------------
| PANEL ADMIN
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

    // 1. DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. PROFIL INSTANSI (KCD) -- [TAMBAHAN BARU]
    Route::controller(InstansiController::class)->prefix('profil-instansi')->name('instansi.')->group(function () {
        Route::get('/', 'index')->name('index');   // admin.instansi.index
        Route::put('/', 'update')->name('update'); // admin.instansi.update
    });

    // 3. SATUAN PENDIDIKAN (MONITORING)
    Route::get('sekolah', [SekolahMonitoringController::class, 'index'])->name('sekolah.index');
    Route::get('sekolah/{id}', [SekolahMonitoringController::class, 'show'])->name('sekolah.show');

    // 4. KEPEGAWAIAN
    Route::prefix('kepegawaian')->name('kepegawaian.')->group(function () {
        Route::controller(GtkController::class)->group(function () {
            // Data Guru & Tendik
            Route::get('guru', 'indexGuru')->name('guru.index');
            Route::get('guru/export/excel', 'exportGuruExcel')->name('guru.export.excel');
            Route::get('tenaga-kependidikan', 'indexTendik')->name('tendik.index');
            Route::get('tenaga-kependidikan/export/excel', 'exportTendikExcel')->name('tendik.export.excel');
            
            // Fitur Tambahan GTK (Specific Routes FIRST)
            Route::get('/gtk/show-multiple', 'showMultiple')->name('gtk.show-multiple');
            Route::get('gtk/inactive', 'inactive')->name('gtk.inactive'); // Pastikan ini sebelum {id}
            
            // Aksi GTK
            Route::put('gtk/{id}/update-data', 'updateData')->name('gtk.update_data');
            Route::post('gtk/{id}/upload-media', 'uploadMedia')->name('gtk.upload_media');
            Route::patch('gtk/{id}/register-keluar', 'registerKeluar')->name('gtk.register-keluar');
            Route::patch('gtk/{id}/unregister-keluar', 'unregisterKeluar')->name('gtk.unregister-keluar');

            // Detail Profil GTK (Route Parameterized ditaruh paling bawah agar aman)
            Route::get('gtk/{id}', 'show')->name('gtk.show'); 
        });
    });

    // 5. KESISWAAN
    Route::prefix('kesiswaan')->name('kesiswaan.')->group(function() {
        Route::controller(SiswaController::class)->group(function() {
            Route::get('siswa/export/excel', 'exportExcel')->name('siswa.export.excel');
            Route::get('siswa/show-multiple', 'showMultiple')->name('siswa.show-multiple');
            Route::get('siswa/inactive', 'inactive')->name('siswa.inactive');
            Route::patch('siswa/{id}/register-keluar', 'registerKeluar')->name('siswa.register-keluar');
            Route::patch('siswa/{id}/unregister-keluar', 'unregisterKeluar')->name('siswa.unregister-keluar');
            Route::post('siswa/{id}/upload-media', 'uploadMedia')->name('siswa.upload_media');

            // Opsional: Cetak
            Route::get('siswa/{id}/cetak-kartu', 'cetakKartu')->name('siswa.cetak_kartu');
            Route::get('siswa/{id}/cetak-pdf', 'cetakPdf')->name('siswa.cetak_pdf');
        });
        Route::resource('siswa', SiswaController::class);
    });

    // 6. ADMINISTRASI
    Route::prefix('administrasi')->name('administrasi.')->group(function () {
        Route::resource('tipe-surat', TipeSuratController::class);

        Route::controller(SuratKeluarSiswaController::class)->group(function () {
            Route::get('surat-keluar-siswa', 'index')->name('surat-keluar-siswa.index');
            Route::post('surat-keluar-siswa/store', 'store')->name('surat-keluar-siswa.store');
            Route::post('surat-keluar-siswa/cetak', 'cetak')->name('surat-keluar-siswa.cetak');
            Route::post('surat-keluar-siswa/pdf', 'downloadPdf')->name('surat-keluar-siswa.pdf');
            Route::get('get-siswa-by-kelas/{nama_rombel}', 'getSiswaByKelas')->name('get-siswa-by-kelas');
        });

        Route::controller(SuratKeluarGuruController::class)->group(function () {
            Route::get('surat-keluar-guru', 'index')->name('surat-keluar-guru.index');
            Route::post('surat-keluar-guru/store', 'store')->name('surat-keluar-guru.store');
            Route::post('surat-keluar-guru/cetak', 'cetak')->name('surat-keluar-guru.cetak');
            Route::post('surat-keluar-guru/pdf', 'downloadPdf')->name('surat-keluar-guru.pdf');
        });

        Route::resource('surat-masuk', SuratMasukController::class);

        Route::controller(NomorSuratSettingController::class)->prefix('pengaturan-nomor')->name('pengaturan-nomor.')->group(function() {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
            Route::post('/reset/{id}', 'resetCounter')->name('reset');
        });

        Route::controller(ArsipSuratController::class)->prefix('arsip-surat')->name('arsip-surat.')->group(function() {
            Route::get('/', 'index')->name('index');
            Route::get('/cetak/{id}', 'cetakUlang')->name('cetak');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });
    });

    Route::get('/underConstructions', function () {
        return view('admin.underConstruction');
    })->name('underConstructions');

});

require __DIR__ . '/auth.php';