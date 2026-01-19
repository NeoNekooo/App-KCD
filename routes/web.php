<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// --- Controller Utama ---
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InstansiController;
use App\Http\Controllers\CetakSkController;

// --- Controller Internal KCD ---
use App\Http\Controllers\Admin\Kepegawaian\PegawaiKcdController;
use App\Http\Controllers\Admin\Kepegawaian\TugasPegawaiKcdController;

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

// --- Controller Layanan ---
use App\Http\Controllers\Admin\VerifikasiController;

// --- Controller Pengaturan ---
use App\Http\Controllers\Admin\Settings\MenuManagementController;
use App\Http\Controllers\Admin\Settings\RoleAccessController; // 🔥 PENTING: Tambahin ini biar gak error

/*
|--------------------------------------------------------------------------
| Rute Web Utama
|--------------------------------------------------------------------------
*/

// --- LANDING PAGE (Logic Auth) ---
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('admin.dashboard');
    }
    return view('auth.login-custom');
})->name('landing');

// --- CETAK SK (Global Auth) ---
Route::middleware(['auth'])->group(function() {
    Route::get('/cetak-sk/{uuid}', [CetakSkController::class, 'cetakSk'])->name('cetak.sk');
});

/*
|--------------------------------------------------------------------------
| PANEL ADMIN (Backend)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

    // 1. DASHBOARD (JANGAN PAKAI MIDDLEWARE CHECK_MENU)
    // Biarkan semua user login bisa akses dashboard. Menu sidebar yang akan memfilter tampilan.
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | KEPEGAWAIAN
    |--------------------------------------------------------------------------
    */
    
    // A. Tugas Pegawai (Penugasan Layanan)
    // Pastikan slug 'kepegawaian-tugas' ada di tabel 'menus'
    Route::controller(TugasPegawaiKcdController::class)
        ->prefix('kepegawaian/tugas-internal')
        ->name('kepegawaian.tugas-kcd.')
        ->middleware('check_menu:kepegawaian-tugas') 
        ->group(function() {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });

    // B. Data Pegawai Internal
    // Pastikan slug 'kepegawaian-data' ada di tabel 'menus'
    Route::prefix('kepegawaian')->name('kepegawaian.')->controller(PegawaiKcdController::class)->group(function() {
        
        // Profil Saya (Bebas Akses Semua User Login)
        Route::get('/profil-saya', 'showMe')->name('me');
        Route::put('/change-password', 'changePassword')->name('change-password');
        
        // Admin Only (Manajemen Data Pegawai)
        Route::middleware('check_menu:kepegawaian-data')->group(function() {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::get('/{id}', 'show')->name('show'); // Detail Pegawai
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
            Route::put('/{id}/reset', 'resetPassword')->name('reset');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | PROFIL INSTANSI
    |--------------------------------------------------------------------------
    */
    Route::controller(InstansiController::class)
        ->prefix('profil-instansi')
        ->name('instansi.')
        ->middleware('check_menu:profil-instansi') // Pastikan slug ini ada di DB
        ->group(function () {
            Route::get('/', 'index')->name('index'); 
            Route::put('/', 'update')->name('update'); 
        });

    /*
    |--------------------------------------------------------------------------
    | MONITORING (SEKOLAH, GTK, SISWA)
    |--------------------------------------------------------------------------
    */
    
    // Sekolah
    Route::middleware('check_menu:satuan-pendidikan')->group(function() {
        Route::get('sekolah/export-excel', [SekolahMonitoringController::class, 'exportExcel'])->name('sekolah.export-excel');
        Route::resource('sekolah', SekolahMonitoringController::class)->only(['index', 'show']);
    });

    // GTK (Guru & Tendik)
    Route::prefix('gtk')->name('gtk.')
        ->middleware('check_menu:gtk') // Pastikan slug 'gtk' ada di DB
        ->controller(GtkController::class)->group(function () {
            Route::get('guru', 'indexGuru')->name('guru.index');
            Route::get('tenaga-kependidikan', 'indexTendik')->name('tendik.index');
            Route::get('show-multiple', 'showMultiple')->name('show-multiple'); 
            Route::get('{id}', 'show')->name('show');
        });

    // Siswa
    Route::prefix('kesiswaan')->name('kesiswaan.')
        ->middleware('check_menu:peserta-didik') // Pastikan slug 'peserta-didik' ada di DB
        ->group(function() {
            Route::get('siswa/export-excel', [SiswaController::class, 'exportExcel'])->name('siswa.export-excel');
            Route::get('siswa/show-multiple', [SiswaController::class, 'showMultiple'])->name('siswa.show-multiple');
            Route::resource('siswa', SiswaController::class)->only(['index', 'show']);
        });

    /*
    |--------------------------------------------------------------------------
    | ADMINISTRASI SURAT
    |--------------------------------------------------------------------------
    */
    Route::prefix('administrasi')->name('administrasi.')
        ->middleware('check_menu:administrasi-surat') // Pastikan slug ini ada
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

    /*
    |--------------------------------------------------------------------------
    | LAYANAN & VERIFIKASI GTK (SENSITIF)
    |--------------------------------------------------------------------------
    */
    // PENTING: Slug di middleware ini menentukan Tugas Pegawai mana yang dicek.
    // Jika slug = 'layanan-kp', middleware akan cek apakah pegawai punya tugas 'Kenaikan Pangkat'.
    
    // Contoh: Route Layanan Kenaikan Pangkat
    Route::middleware('check_menu:layanan-kp')->prefix('layanan/kenaikan-pangkat')->name('layanan.kp.')->group(function () {
         Route::get('/', [VerifikasiController::class, 'index'])->name('index');
         // ... route proses lainnya ...
    });

    // NOTE: Karena controller kamu cuma satu (VerifikasiController), 
    // mungkin kamu pakai slug umum 'layanan-gtk'. 
    // Pastikan di Middleware logic Staff menangani slug 'layanan-gtk' dengan benar (misal: cek semua tugas).
    
    Route::middleware('check_menu:layanan-gtk')->prefix('verifikasi')->name('verifikasi.')->group(function () {
        Route::get('/', [VerifikasiController::class, 'index'])->name('index');
        Route::put('/{id}/set-syarat', [VerifikasiController::class, 'setSyarat'])->name('set_syarat');
        Route::put('/{id}/process', [VerifikasiController::class, 'verifyProcess'])->name('process');
        Route::put('/{id}/kasubag-process', [VerifikasiController::class, 'kasubagProcess'])->name('kasubag_process');
        Route::put('/{id}/kepala-process', [VerifikasiController::class, 'kepalaProcess'])->name('kepala_process');
    });

    /*
    |--------------------------------------------------------------------------
    | PENGATURAN SISTEM
    |--------------------------------------------------------------------------
    */
    // Menu Management & Role Access (Hanya Admin yang punya akses ini di DB)
    Route::prefix('settings')->name('settings.')
        ->middleware('check_menu:settings-menu') // Pastikan buat slug ini di DB
        ->group(function() {
            
            // 1. Manajemen Menu (CRUD)
            Route::resource('menus', MenuManagementController::class)->except(['show']);

            // 2. Hak Akses Role (Fitur Baru Checklist)
            Route::get('role-access', [RoleAccessController::class, 'index'])->name('role-access.index');
            Route::post('role-access', [RoleAccessController::class, 'update'])->name('role-access.update');
        });

    Route::get('/underConstructions', function () {
        return view('admin.underConstruction');
    })->name('underConstructions');

});

require __DIR__ . '/auth.php';