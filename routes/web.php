<?php

use Illuminate\Support\Facades\Route;

// --- Controller Utama & Auth ---
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;

// --- Controller Monitoring (KCD) ---
use App\Http\Controllers\Admin\Sekolah\SekolahController as SekolahMonitoringController; 
use App\Http\Controllers\Admin\Kepegawaian\GtkController;
use App\Http\Controllers\Admin\Kesiswaan\SiswaController;

/*
|--------------------------------------------------------------------------
| Rute Web Utama (Sistem Monitoring KCD)
|--------------------------------------------------------------------------
*/

// Halaman Login (Custom View)
Route::get('/', function () {
    return view('auth.login-custom');
})->name('login');

// Logout
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Panel Admin KCD (Protected Routes)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

    // 1. DASHBOARD UTAMA
    // Menampilkan statistik wilayah, total sekolah, siswa, gtk, dll.
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. MONITORING SATUAN PENDIDIKAN
    // Daftar sekolah binaan, filter jenjang/status, dan profil sekolah.
    Route::get('sekolah', [SekolahMonitoringController::class, 'index'])->name('sekolah.index');
    Route::get('sekolah/{id}', [SekolahMonitoringController::class, 'show'])->name('sekolah.show');

    // 3. MONITORING KESISWAAN
    // Pencarian data siswa lintas sekolah/wilayah (Read Only)
    Route::prefix('kesiswaan')->name('kesiswaan.')->group(function() {
        Route::get('siswa', [SiswaController::class, 'index'])->name('siswa.index');
        Route::get('siswa/{id}', [SiswaController::class, 'show'])->name('siswa.show');
        
        // Fitur Tambahan (Opsional: Cetak Profil/Laporan)
        Route::get('siswa/export/excel', [SiswaController::class, 'exportExcel'])->name('siswa.export.excel');
        Route::get('siswa/show-multiple', [SiswaController::class, 'showMultiple'])->name('siswa.show-multiple');
        Route::get('siswa/{id}/cetak-pdf', [SiswaController::class, 'cetakPdf'])->name('siswa.cetak_pdf');
    });

    // 4. MONITORING KEPEGAWAIAN (GTK)
    // Pencarian data Guru & Tendik lintas sekolah/wilayah (Read Only)
    Route::prefix('kepegawaian')->name('kepegawaian.')->group(function () {
        
        // Data Guru
        Route::prefix('guru')->name('guru.')->controller(GtkController::class)->group(function () {
            Route::get('/', 'indexGuru')->name('index');
            Route::get('/export/excel', 'exportGuruExcel')->name('export.excel');
        });

        // Data Tendik
        Route::prefix('tenaga-kependidikan')->name('tendik.')->controller(GtkController::class)->group(function () {
            Route::get('/', 'indexTendik')->name('index');
            Route::get('/export/excel', 'exportTendikExcel')->name('export.excel');
        });

        // Detail & Laporan GTK
        Route::get('gtk/show-multiple', [GtkController::class, 'showMultiple'])->name('gtk.show-multiple');
        Route::get('gtk/cetak-pdf/{id}', [GtkController::class, 'cetakPdf'])->name('gtk.cetak_pdf');
    });

    // 5. HALAMAN DALAM PENGEMBANGAN (Opsional)
    Route::get('/underConstructions', function () {
        return view('admin.underConstruction');
    })->name('underConstructions');

});

// Load Auth Routes bawaan Laravel (Login logic, Reset Password, dll)
require __DIR__ . '/auth.php';