<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// --- Controller Utama ---
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InstansiController;
use App\Http\Controllers\CetakSkController;

// --- Controller Internal KCD ---
use App\Http\Controllers\Admin\MyProfileController;
use App\Http\Controllers\Admin\Kepegawaian\PegawaiKcdController;
use App\Http\Controllers\Admin\Kepegawaian\TugasPegawaiKcdController;
use App\Http\Controllers\Admin\JabatanKcdController;

// --- Controller Monitoring ---
use App\Http\Controllers\Admin\Sekolah\SekolahController as SekolahMonitoringController;
use App\Http\Controllers\Admin\Kepegawaian\GtkController;
use App\Http\Controllers\Admin\Kesiswaan\SiswaController;

// 🔥 Import Controller Monitoring Sync 🔥
use App\Http\Controllers\Admin\SyncLogController;

// 🔥 [UPDATE] Import Controller Data Spasial 🔥
use App\Http\Controllers\Admin\DataSpasialController;

// --- Controller Administrasi ---
use App\Http\Controllers\Admin\Administrasi\TipeSuratController;
use App\Http\Controllers\Admin\Administrasi\SuratKeluarSiswaController;
use App\Http\Controllers\Admin\Administrasi\SuratKeluarGuruController;
use App\Http\Controllers\Admin\Administrasi\SuratKeluarInternalController; 
use App\Http\Controllers\Admin\Administrasi\SuratMasukController;
use App\Http\Controllers\Admin\Administrasi\NomorSuratSettingController;
use App\Http\Controllers\Admin\Administrasi\ArsipSuratController;

// --- Controller Layanan ---
use App\Http\Controllers\Admin\VerifikasiController;
use App\Http\Controllers\Admin\VerifikasiPdController;
use App\Http\Controllers\Admin\DokumenLayananController;

// --- Controller Pengaturan ---
use App\Http\Controllers\Admin\Settings\MenuManagementController;
use App\Http\Controllers\Admin\Settings\RoleAccessController;

// --- Controller Guest & Antrian ---
use App\Http\Controllers\GuestBookController;
use App\Http\Controllers\Admin\AntrianController;
use App\Http\Controllers\Admin\AntrianDisplayController;

// --- Controller Manajemen Website (Migrasi) ---
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\WelcomeMessageController;
use App\Http\Controllers\Admin\SettingController as WebSettingController;

/*
|--------------------------------------------------------------------------
| Rute Web Utama
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\WelcomeController;

// --- LANDING PAGE ---
Route::get('/', [WelcomeController::class, 'index'])->name('landing');

// --- FRONTEND ROUTES ---
Route::get('/tentang-kami', function () { return view('frontend.about'); });
Route::get('/struktur-organisasi', function () { return view('frontend.about'); });
Route::get('/layanan/pengaduan', function () { return view('frontend.services'); });
Route::get('/layanan/administrasi-ptk', function () { return view('frontend.services'); });
Route::get('/layanan/tata-kelola', function () { return view('frontend.services'); });
Route::get('/berita', function () { return view('frontend.gallery'); });
Route::get('/pengumuman', function () { return view('frontend.gallery'); });
Route::get('/galeri', function () { return view('frontend.gallery'); });
Route::get('/unduhan', function () { return view('frontend.gallery'); });
Route::get('/kontak', function () { return view('frontend.contact'); });

// --- DASHBOARD REDIRECT (Logic Auth) ---
Route::get('/home', function () {
    if (Auth::check()) {
        $user = Auth::user();
        $isAdmin = in_array(strtolower($user->role ?? ''), ['admin', 'administrator', 'operator kcd']);
        if ($isAdmin) {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('admin.dashboard.pegawai');
        }
    }
    return redirect()->route('login');
})->name('home');

// --- CETAK SK (PUBLIC) ---
Route::get('/cetak-sk/{uuid}', [CetakSkController::class, 'cetakSk'])->name('cetak.sk');

// --- BUKU TAMU & TIKET ANTRIAN (PUBLIC) ---
Route::get('/buku-tamu', [GuestBookController::class, 'index'])->name('guest.buku-tamu');
Route::post('/buku-tamu', [GuestBookController::class, 'store'])->name('guest.buku-tamu.store');
Route::post('/buku-tamu/{id}/print', [GuestBookController::class, 'requestPrint'])->name('guest.buku-tamu.print');


/*
|--------------------------------------------------------------------------
| PANEL ADMIN (Backend)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

    // 1. DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard-pegawai', [DashboardController::class, 'indexPegawai'])->name('dashboard.pegawai');

    /*
    |--------------------------------------------------------------------------
    | KEPEGAWAIAN
    |--------------------------------------------------------------------------
    */
    
    // A. Tugas Pegawai
    Route::controller(TugasPegawaiKcdController::class)
        ->prefix('kepegawaian/tugas-internal')
        ->name('kepegawaian.tugas-kcd.')
        ->middleware('check_menu:kepegawaian-tugas') 
        ->group(function() {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });

    // B. My Profile (Separated)
    Route::prefix('profil-saya')->name('profil-saya.')->middleware('auth')->controller(MyProfileController::class)->group(function() {
        Route::get('/', 'show')->name('show');
        Route::put('/', 'update')->name('update');
        Route::put('/change-password', 'changePassword')->name('change-password');
    });

    // DAFTAR ANTRIAN & TAMU RESEPSIONIS
    Route::controller(AntrianController::class)
        ->prefix('antrian')
        ->name('antrian.')
        ->middleware('check_menu:antrian')
        ->group(function() {
            Route::get('/', 'index')->name('index');
            Route::get('/partial', 'getPartial')->name('partial');
            Route::get('/export', 'export')->name('export');
            Route::put('/{id}/panggil', 'panggil')->name('panggil');
            Route::put('/{id}/selesai', 'selesai')->name('selesai');
            Route::delete('/{id}', 'destroy')->name('destroy');
            
            // Kategori Keperluan
            Route::post('/kategori', 'storeCategory')->name('kategori.store');
            Route::delete('/kategori/{id}', 'destroyCategory')->name('kategori.destroy');
        });

    // LAYAR TV DISPLAY (Admin Only / Ruang KCD)
    Route::get('/display-antrian', [AntrianDisplayController::class, 'index'])->name('display.antrian');
    Route::get('/display-antrian/updates', [AntrianDisplayController::class, 'getUpdates'])->name('display.antrian.updates');
    Route::get('/display-antrian/ticket/{id}', [AntrianDisplayController::class, 'ticketThermal'])->name('display.antrian.ticket');
    Route::put('/display-antrian/mark-printed/{id}', [AntrianDisplayController::class, 'markAsPrinted'])->name('display.antrian.mark-printed');

    // C. Data Pegawai Internal (Admin-only)
    Route::prefix('kepegawaian')->name('kepegawaian.')->controller(PegawaiKcdController::class)->group(function() {
        Route::middleware('check_menu:kepegawaian-data')->group(function() {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::get('/{id}', 'show')->name('show');
            Route::put('/{id}', 'update')->name('update'); 
            Route::delete('/{id}', 'destroy')->name('destroy');
            Route::put('/{id}/reset', 'resetPassword')->name('reset');
        });
    });

    // D. Pengaturan Jabatan KCD
    Route::group(['prefix' => 'kepegawaian_kcd', 'as' => 'kepegawaian_kcd.', 'middleware' => 'check_menu:kepegawaian-jabatan'], function() {
        Route::resource('jabatan', JabatanKcdController::class)->except(['show']);
    });

    /*
    |--------------------------------------------------------------------------
    | PROFIL INSTANSI
    |--------------------------------------------------------------------------
    */
    Route::controller(InstansiController::class)
        ->prefix('profil-instansi')
        ->name('instansi.')
        ->middleware('check_menu:profil-instansi')
        ->group(function () {
            Route::get('/', 'index')->name('index'); 
            Route::put('/', 'update')->name('update'); 
        });

    /*
    |--------------------------------------------------------------------------
    | MONITORING
    |--------------------------------------------------------------------------
    */
    
    // 🔥 Route Monitoring Sync Log 🔥
    Route::get('monitoring-sync', [SyncLogController::class, 'index'])->name('monitoring-sync.index');

    // 🔥 ROUTE AJAX FILTER BERJENJANG SEKOLAH 🔥
    Route::prefix('ajax')->name('ajax.')->group(function() {
        Route::get('/get-kecamatan', [SekolahMonitoringController::class, 'getKecamatan'])->name('kecamatan');
        Route::get('/get-jenjang', [SekolahMonitoringController::class, 'getJenjang'])->name('jenjang');
        Route::get('/get-status', [SekolahMonitoringController::class, 'getStatus'])->name('status');
    });

    Route::middleware('check_menu:satuan-pendidikan')->group(function() {
        Route::get('sekolah/export-excel', [SekolahMonitoringController::class, 'exportExcel'])->name('sekolah.export-excel');
        Route::get('sekolah/rekapitulasi', [SekolahMonitoringController::class, 'rekapitulasi'])->name('sekolah.rekapitulasi'); // Route Baru
        Route::get('sekolah/rekapitulasi/export-excel', [SekolahMonitoringController::class, 'exportRekapitulasi'])->name('sekolah.rekapitulasi.export-excel'); // Tombol Cetak Rekap
        Route::resource('sekolah', SekolahMonitoringController::class)->only(['index', 'show']);
    });

    Route::prefix('gtk')->name('gtk.')
        ->middleware('check_menu:gtk')
        ->controller(GtkController::class)->group(function () {
            Route::get('rekapitulasi', 'rekapitulasi')->name('rekapitulasi'); // Route Rekap GTK Baru
            Route::get('rekapitulasi/export-excel', 'exportRekapitulasi')->name('rekapitulasi.export-excel'); // Cetak Excel GTK
            Route::get('guru', 'indexGuru')->name('guru.index');
            Route::get('tenaga-kependidikan', 'indexTendik')->name('tendik.index');
            Route::get('show-multiple', 'showMultiple')->name('show-multiple'); 
            Route::get('{id}', 'show')->name('show');
        });

    Route::prefix('kesiswaan')->name('kesiswaan.')
        ->middleware('check_menu:peserta-didik')
        ->group(function() {
            Route::get('siswa/rekapitulasi', [SiswaController::class, 'rekapitulasi'])->name('siswa.rekapitulasi');
            Route::get('siswa/rekapitulasi/export-excel', [SiswaController::class, 'exportRekapitulasi'])->name('siswa.rekapitulasi.export-excel');
            Route::get('siswa/export-excel', [SiswaController::class, 'exportExcel'])->name('siswa.export-excel');
            Route::get('siswa/show-multiple', [SiswaController::class, 'showMultiple'])->name('siswa.show-multiple');
            Route::resource('siswa', SiswaController::class)->only(['index', 'show']);
        });

    /*
    |--------------------------------------------------------------------------
    | 🔥 DATA SPASIAL (PETA) 🔥
    |--------------------------------------------------------------------------
    */
    // Jika nanti mau dipasang permission menu, tambahkan middleware check_menu disini
    Route::get('dataspasial', [DataSpasialController::class, 'index'])->name('dataspasial.index');


    /*
    |--------------------------------------------------------------------------
    | ADMINISTRASI SURAT
    |--------------------------------------------------------------------------
    */
    Route::prefix('administrasi')->name('administrasi.')
        ->middleware('check_menu:administrasi-surat')
        ->group(function () {
            
            // Route Copy Template
            Route::post('tipe-surat/{id}/duplicate', [TipeSuratController::class, 'duplicate'])->name('tipe-surat.duplicate');
            Route::resource('tipe-surat', TipeSuratController::class);
            
            // Surat Keluar Siswa & Guru
            Route::get('surat-keluar-siswa/get-siswa/{nama_rombel}', [SuratKeluarSiswaController::class, 'getSiswaByKelas'])->name('surat-keluar-siswa.get-siswa');
            Route::resource('surat-keluar-siswa', SuratKeluarSiswaController::class)->only(['index', 'store']);
            Route::resource('surat-keluar-guru', SuratKeluarGuruController::class)->only(['index', 'store']);

            // Surat Keluar Internal
            Route::controller(SuratKeluarInternalController::class)
                ->prefix('surat-keluar-internal')
                ->name('surat-keluar-internal.')
                ->group(function() {
                    Route::get('/', 'index')->name('index');
                    Route::post('/', 'store')->name('store');
                    Route::post('/cetak', 'cetak')->name('cetak');
                    Route::post('/pdf', 'downloadPdf')->name('pdf');
                });
            
            // Surat Masuk & Pengaturan Nomor
            Route::resource('surat-masuk', SuratMasukController::class);
            Route::post('pengaturan-nomor/reset/{id}', [NomorSuratSettingController::class, 'resetCounter'])->name('pengaturan-nomor.reset');
            Route::resource('pengaturan-nomor', NomorSuratSettingController::class)->except(['create', 'edit', 'show']);
            
            // Arsip
            Route::resource('arsip-surat', ArsipSuratController::class)->only(['index', 'destroy']);
            Route::get('arsip-surat/{id}/cetak', [ArsipSuratController::class, 'cetak'])->name('arsip-surat.cetak');
        });

    /*
    |--------------------------------------------------------------------------
    | LAYANAN & VERIFIKASI GTK
    |--------------------------------------------------------------------------
    */
    
    Route::middleware('check_menu:layanan-gtk')->prefix('verifikasi')->name('verifikasi.')->group(function () {
        Route::get('/', [VerifikasiController::class, 'index'])->name('index');
        Route::post('/{id}/approve-initial', [VerifikasiController::class, 'approveInitial'])->name('approve_initial');
        Route::post('/{id}/reject', [VerifikasiController::class, 'reject'])->name('reject');
        Route::post('/{id}/resend-acc', [VerifikasiController::class, 'resendAcc'])->name('resend_acc');
        Route::put('/{id}/set-syarat', [VerifikasiController::class, 'setSyarat'])->name('set_syarat');
        Route::put('/{id}/process', [VerifikasiController::class, 'verifyProcess'])->name('process');
        Route::put('/{id}/kasubag-process', [VerifikasiController::class, 'kasubagProcess'])->name('kasubag_process');
        Route::put('/{id}/kepala-process', [VerifikasiController::class, 'kepalaProcess'])->name('kepala_process');
    });


    /*
    |--------------------------------------------------------------------------
    | 🔥 [NEW] LAYANAN & VERIFIKASI PESERTA DIDIK (PD) 🔥
    |--------------------------------------------------------------------------
    */
    Route::middleware('check_menu:layanan-pd')->prefix('verifikasi-pd')->name('verifikasi_pd.')->group(function () {
        // Halaman Utama Daftar Pengajuan PD
        Route::get('/', [VerifikasiPdController::class, 'index'])->name('index');

        // Validasi Awal (Setujui Permohonan Sekolah)
        Route::post('/{id}/approve-initial', [VerifikasiPdController::class, 'approveInitial'])->name('approve_initial');

        // Simpan Daftar Persyaratan
        Route::put('/{id}/set-syarat', [VerifikasiPdController::class, 'setSyarat'])->name('set_syarat');

        // Proses Verifikasi Akhir (ACC / Revisi Berkas)
        Route::put('/{id}/process', [VerifikasiPdController::class, 'verifyProcess'])->name('process');
        
        // Opsional: Jika butuh route reject mandiri di tahap awal
        Route::post('/{id}/reject', [VerifikasiPdController::class, 'reject'])->name('reject');
    });

    /*
    |--------------------------------------------------------------------------
    | ARSIP DOKUMEN LAYANAN
    |--------------------------------------------------------------------------
    */
    Route::prefix('dokumen-layanan')->name('dokumen-layanan.')
        ->middleware('check_menu:dokumen-layanan')
        ->group(function () {
            Route::get('/', [DokumenLayananController::class, 'index'])->name('index');
        });

    /*
    |--------------------------------------------------------------------------
    | PENGATURAN SISTEM
    |--------------------------------------------------------------------------
    */
    Route::prefix('settings')->name('settings.')
        ->middleware('is_admin')
        ->group(function() {
            Route::resource('menus', MenuManagementController::class)->except(['show']);
            Route::get('role-access', [RoleAccessController::class, 'index'])->name('role-access.index');
            Route::post('role-access', [RoleAccessController::class, 'update'])->name('role-access.update');
        });

    /*
    |--------------------------------------------------------------------------
    | MANAJEMEN WEBSITE (MIGRASI)
    |--------------------------------------------------------------------------
    */
    Route::prefix('website')->name('website.')->group(function () {
        Route::resource('sliders', SliderController::class)->middleware('check_menu:web-slider');
        Route::get('welcome', [WelcomeMessageController::class, 'index'])->name('welcome.index')->middleware('check_menu:web-welcome');
        Route::post('welcome', [WelcomeMessageController::class, 'update'])->name('welcome.update')->middleware('check_menu:web-welcome');
        Route::get('settings', [WebSettingController::class, 'index'])->name('settings.index')->middleware('check_menu:web-settings');
        Route::post('settings', [WebSettingController::class, 'update'])->name('settings.update')->middleware('check_menu:web-settings');
    });

    Route::get('/underConstructions', function () {
        return view('admin.underConstruction');
    })->name('underConstructions');
});

require __DIR__ . '/auth.php';