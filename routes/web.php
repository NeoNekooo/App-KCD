<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// --- Controller Utama ---
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\InstansiController;

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

// --- LOGIKA AUTH ---
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('admin.dashboard');
    }
    return view('auth.login-custom');
})->name('landing');

/*
|--------------------------------------------------------------------------
| PANEL ADMIN KCD
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

    // 1. DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. PROFIL INSTANSI
    Route::controller(InstansiController::class)->prefix('profil-instansi')->name('instansi.')->group(function () {
        Route::get('/', 'index')->name('index'); // Tambah Route::
        Route::put('/', 'update')->name('update'); // Tambah Route::
    });

    /*
    |--------------------------------------------------------------------------
    | 2.5 MANAJEMEN PEGAWAI INTERNAL KCD
    |--------------------------------------------------------------------------
    */
    Route::prefix('kcd/pegawai')->name('kcd.pegawai.')->controller(PegawaiKcdController::class)->group(function() {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
        Route::put('/{id}/reset', 'resetPassword')->name('reset');
    });

    // 3. SATUAN PENDIDIKAN
    Route::get('sekolah/export-excel', [SekolahMonitoringController::class, 'exportExcel'])->name('sekolah.export-excel');
    Route::resource('sekolah', SekolahMonitoringController::class)->only(['index', 'show']);

    // 4. GTK (GURU & TENDIK)
    Route::prefix('gtk')->name('gtk.')->controller(GtkController::class)->group(function () {
        Route::get('guru', 'indexGuru')->name('guru.index');
        Route::get('tenaga-kependidikan', 'indexTendik')->name('tendik.index');
        Route::get('show-multiple', 'showMultiple')->name('show-multiple'); 
        Route::get('{id}', 'show')->name('show');
    });

    // 5. KESISWAAN
    Route::prefix('kesiswaan')->name('kesiswaan.')->group(function() {
        Route::get('siswa/export-excel', [SiswaController::class, 'exportExcel'])->name('siswa.export-excel');
        Route::get('siswa/show-multiple', [SiswaController::class, 'showMultiple'])->name('siswa.show-multiple');
        Route::resource('siswa', SiswaController::class)->only(['index', 'show']);
    });

    /*
    |--------------------------------------------------------------------------
    | 7. LAYANAN GTK & VERIFIKASI SURAT (DINAMIS)
    |--------------------------------------------------------------------------
    */
    // Route Dinamis
    Route::get('/layanan/{kategori}', [VerifikasiController::class, 'indexByKategori'])
        ->name('layanan.kategori');

    Route::prefix('verifikasi')->name('verifikasi.')->group(function () {
        Route::get('/', [VerifikasiController::class, 'index'])->name('index');
        
        // LOGIC 1 & 2
        Route::post('/{id}/minta-syarat', [VerifikasiController::class, 'kirimPermintaan'])->name('minta_syarat');
        Route::post('/{id}/simpan-cek', [VerifikasiController::class, 'simpanPemeriksaan'])->name('simpan_cek');
    });

    // 6. ADMINISTRASI (Surat Internal KCD)
    Route::prefix('administrasi')->name('administrasi.')->group(function () {
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

    // Halaman Under Construction
    Route::get('/underConstructions', function () {
        return view('admin.underConstruction');
    })->name('underConstructions');

});

require __DIR__ . '/auth.php';