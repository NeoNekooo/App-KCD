<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// --- Controller Utama ---
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\InstansiController;
use App\Http\Controllers\CetakSkController; 

// --- Controller Internal KCD ---
use App\Http\Controllers\Admin\Kepegawaian\PegawaiKcdController;

// --- Controller Monitoring (Data Sekolah/GTK) ---
use App\Http\Controllers\Admin\Sekolah\SekolahController as SekolahMonitoringController;
use App\Http\Controllers\Admin\Kepegawaian\GtkController;
use App\Http\Controllers\Admin\Kesiswaan\SiswaController;

// --- Controller Administrasi Internal KCD ---
use App\Http\Controllers\Admin\Administrasi\TipeSuratController;
use App\Http\Controllers\Admin\Administrasi\SuratKeluarSiswaController;
use App\Http\Controllers\Admin\Administrasi\SuratKeluarGuruController;
use App\Http\Controllers\Admin\Administrasi\SuratMasukController;
use App\Http\Controllers\Admin\Administrasi\NomorSuratSettingController;
use App\Http\Controllers\Admin\Administrasi\ArsipSuratController;

// --- Controller Verifikasi & Layanan GTK ---
use App\Http\Controllers\Admin\VerifikasiController;

/*
|--------------------------------------------------------------------------
| Rute Web Utama
|--------------------------------------------------------------------------
*/

// --- LOGIKA AUTH (Landing Page) ---
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('admin.dashboard');
    }
    return view('auth.login-custom');
})->name('landing');

/*
|--------------------------------------------------------------------------
| ROUTE CETAK SK (GLOBAL AUTH)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function() {
    Route::get('/cetak-sk/{uuid}', [CetakSkController::class, 'cetakSk'])->name('cetak.sk');
});

/*
|--------------------------------------------------------------------------
| PANEL ADMIN KCD
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

    // 1. DASHBOARD (Bebas Akses Semua Role)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | 2. KEPEGAWAIAN KCD (FLEXIBLE ACCESS)
    |--------------------------------------------------------------------------
    | Logic: 
    | - Admin (Punya Menu): Bisa akses Index, Tambah, Hapus.
    | - Pegawai (Via Menu Profil Saya): Akses showMe tanpa middleware menu.
    */
    Route::prefix('kepegawaian')->name('kepegawaian.')->controller(PegawaiKcdController::class)->group(function() {
        
        // [BARU] Route Khusus Menu 'Profil Saya' (Pegawai)
        // URL: /admin/kepegawaian/profil-saya
        // REVISI: Middleware menu dilepas agar tidak 403, keamanan via Controller
        Route::get('/profil-saya', 'showMe')->name('me');

        // GRUP A: Hanya yang punya menu 'kepegawaian-kcd' (Admin)
        Route::middleware('check_menu:kepegawaian-kcd')->group(function() {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::delete('/{id}', 'destroy')->name('destroy');
            Route::put('/{id}/reset', 'resetPassword')->name('reset');
        });

        // GRUP B: Bebas Middleware Menu (Tapi diproteksi Controller by ID)
        // Untuk handle Update data profil (baik Admin maupun Pegawai)
        Route::put('/change-password', 'changePassword')->name('change-password'); 
        Route::get('/{id}', 'show')->name('show'); 
        Route::put('/{id}', 'update')->name('update');
    });

    // 3. PROFIL INSTANSI (Khusus Role: Operator KCD)
    Route::controller(InstansiController::class)->prefix('profil-instansi')->name('instansi.')
        ->middleware('check_menu:profil-instansi')
        ->group(function () {
            Route::get('/', 'index')->name('index'); 
            Route::put('/', 'update')->name('update'); 
        });

    // 4. SATUAN PENDIDIKAN (Khusus Role: Operator KCD)
    Route::middleware('check_menu:satuan-pendidikan')->group(function() {
        Route::get('sekolah/export-excel', [SekolahMonitoringController::class, 'exportExcel'])->name('sekolah.export-excel');
        Route::resource('sekolah', SekolahMonitoringController::class)->only(['index', 'show']);
    });

    // 5. GTK (Khusus Role: Operator KCD, Sekolah)
    Route::prefix('gtk')->name('gtk.')
        ->middleware('check_menu:gtk')
        ->controller(GtkController::class)->group(function () {
            Route::get('guru', 'indexGuru')->name('guru.index');
            Route::get('tenaga-kependidikan', 'indexTendik')->name('tendik.index');
            Route::get('show-multiple', 'showMultiple')->name('show-multiple'); 
            Route::get('{id}', 'show')->name('show');
        });

    // 6. KESISWAAN (Khusus Role: Operator KCD, Sekolah)
    Route::prefix('kesiswaan')->name('kesiswaan.')
        ->middleware('check_menu:peserta-didik')
        ->group(function() {
            Route::get('siswa/export-excel', [SiswaController::class, 'exportExcel'])->name('siswa.export-excel');
            Route::get('siswa/show-multiple', [SiswaController::class, 'showMultiple'])->name('siswa.show-multiple');
            Route::resource('siswa', SiswaController::class)->only(['index', 'show']);
        });

    // 7. ADMINISTRASI SURAT (Khusus Role: Operator KCD, Sekolah)
    Route::prefix('administrasi')->name('administrasi.')
        ->middleware('check_menu:administrasi-surat')
        ->group(function () {
            Route::resource('tipe-surat', TipeSuratController::class);
            
            Route::get('surat-keluar-siswa/get-siswa/{nama_rombel}', [SuratKeluarSiswaController::class, 'getSiswaByKelas'])->name('surat-keluar-siswa.get-siswa');
            Route::resource('surat-keluar-siswa', SuratKeluarSiswaController::class)->only(['index', 'store']);

            Route::resource('surat-keluar-guru', SuratKeluarGuruController::class)->only(['index', 'store']);
            Route::resource('surat-masuk', SuratMasukController::class);

            Route::post('pengaturan-nomor/reset/{id}', [NomorSuratSettingController::class, 'resetCounter'])->name('pengaturan-nomor.reset');
            Route::resource('pengaturan-nomor', NomorSuratSettingController::class)->except(['create', 'edit', 'show']);

            Route::resource('arsip-surat', ArsipSuratController::class)->only(['index', 'destroy']);
            Route::get('arsip-surat/{id}/cetak', [ArsipSuratController::class, 'cetak'])->name('arsip-surat.cetak');
        });

    // 8. LAYANAN GTK (Khusus Role: Operator KCD)
    Route::middleware('check_menu:layanan-gtk')->prefix('verifikasi')->name('verifikasi.')->group(function () {
        Route::get('/', [VerifikasiController::class, 'index'])->name('index');
        Route::put('/{id}/set-syarat', [VerifikasiController::class, 'setSyarat'])->name('set_syarat');
        Route::put('/{id}/process-admin', [VerifikasiController::class, 'verifyProcess'])->name('process');
        Route::put('/{id}/process-kasubag', [VerifikasiController::class, 'kasubagProcess'])->name('kasubag_process');
        Route::put('/{id}/process-kepala', [VerifikasiController::class, 'kepalaProcess'])->name('kepala_process');
    });

    // Halaman Under Construction
    Route::get('/underConstructions', function () {
        return view('admin.underConstruction');
    })->name('underConstructions');

});

require __DIR__ . '/auth.php';