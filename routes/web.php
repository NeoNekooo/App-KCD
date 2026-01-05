<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// --- Controller Utama ---
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\InstansiController;

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

    // 2. PROFIL INSTANSI
    Route::controller(InstansiController::class)->prefix('profil-instansi')->name('instansi.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::put('/', 'update')->name('update');
    });

    // 3. SATUAN PENDIDIKAN
    // Route Export ditaruh SEBELUM resource agar tidak dianggap ID
    Route::get('sekolah/export-excel', [SekolahMonitoringController::class, 'exportExcel'])->name('sekolah.export-excel');
    Route::resource('sekolah', SekolahMonitoringController::class)->only(['index', 'show']);

    // 4. GTK (GURU & TENDIK)
    Route::prefix('gtk')->name('gtk.')->controller(GtkController::class)->group(function () {
        // List Data
        Route::get('guru', 'indexGuru')->name('guru.index');
        Route::get('tenaga-kependidikan', 'indexTendik')->name('tendik.index');
        
        // Detail & Fitur Lain
        Route::get('show-multiple', 'showMultiple')->name('show-multiple'); 
        Route::get('{id}', 'show')->name('show');
    });

    // 5. KESISWAAN
    Route::prefix('kesiswaan')->name('kesiswaan.')->group(function() {
        // --- ROUTE TAMBAHAN (Wajib ditaruh SEBELUM resource) ---
        Route::get('siswa/export-excel', [SiswaController::class, 'exportExcel'])->name('siswa.export-excel');
        Route::get('siswa/show-multiple', [SiswaController::class, 'showMultiple'])->name('siswa.show-multiple');
        
        // --- Resource Standar ---
        Route::resource('siswa', SiswaController::class)->only(['index', 'show']);
    });

    // 6. ADMINISTRASI
    Route::prefix('administrasi')->name('administrasi.')->group(function () {
        
        // Tipe Surat
        Route::resource('tipe-surat', TipeSuratController::class);
        
        // Surat Keluar Siswa
        Route::get('surat-keluar-siswa/get-siswa/{nama_rombel}', [SuratKeluarSiswaController::class, 'getSiswaByKelas'])->name('surat-keluar-siswa.get-siswa');
        Route::resource('surat-keluar-siswa', SuratKeluarSiswaController::class)->only(['index', 'store']);

        // Surat Keluar Guru
        Route::resource('surat-keluar-guru', SuratKeluarGuruController::class)->only(['index', 'store']);
        
        // Surat Masuk
        Route::resource('surat-masuk', SuratMasukController::class);

        // Pengaturan Nomor Surat
        Route::post('pengaturan-nomor/reset/{id}', [NomorSuratSettingController::class, 'resetCounter'])->name('pengaturan-nomor.reset');
        Route::resource('pengaturan-nomor', NomorSuratSettingController::class)->except(['create', 'edit', 'show']);

        // Arsip Surat
        Route::resource('arsip-surat', ArsipSuratController::class)->only(['index', 'destroy']);
    });

    // Halaman Under Construction
    Route::get('/underConstructions', function () {
        return view('admin.underConstruction');
    })->name('underConstructions');

});

require __DIR__ . '/auth.php';