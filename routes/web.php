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

// --- Rute 2FA Google Authenticator ---
use App\Http\Controllers\Auth\Google2FAController;
Route::middleware(['auth', 'stealth'])->group(function () {
    Route::get('/2fa/verify', [Google2FAController::class, 'showVerifyForm'])->name('2fa.verify');
    Route::post('/2fa/verify', [Google2FAController::class, 'verify']);
    
    // Pengaturan 2FA di profil/settings
    Route::get('/admin/settings/security/2fa', [Google2FAController::class, 'showSettings'])->name('admin.settings.2fa');
    Route::post('/admin/settings/security/2fa/enable', [Google2FAController::class, 'enable'])->name('admin.settings.2fa.enable');
    Route::post('/admin/settings/security/2fa/disable', [Google2FAController::class, 'disable'])->name('admin.settings.2fa.disable');
});

/*
|--------------------------------------------------------------------------
| Rute Web Utama
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\WelcomeController;

// --- DOMAIN MANAJEMEN / ADMIN (kcd6.hexanusa.com) ---
Route::domain('mandala.hexanusa.com')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });
});

// --- DOMAIN PROFIL / FRONTEND (profilkcd6.hexanusa.com) ---
Route::domain('profilkcd6.hexanusa.com')->group(function () {
    Route::get('/', [WelcomeController::class, 'index'])->name('landing');
});

// --- RUTE FALLBACK (Lokal atau jika domain tidak cocok) ---
Route::get('/', function () {
    // Jika di lokal (localhost), tampilkan landing page
    if (app()->environment('local')) {
        return app(WelcomeController::class)->index();
    }
    return redirect()->route('login');
});

// --- FRONTEND ROUTES ---
Route::get('/tentang-kami', function () {
    $instansi = \App\Models\Instansi::first();
    return view('frontend.about', compact('instansi'));
});
Route::get('/struktur-organisasi', function () {
    $struktur = \App\Models\StrukturOrganisasi::orderBy('urutan', 'asc')->get();
    return view('frontend.org-chart', compact('struktur'));
});
Route::get('/layanan/pengaduan', function () { return view('frontend.services'); });
Route::get('/layanan/administrasi-ptk', function () { return view('frontend.services'); });
Route::get('/layanan/tata-kelola', function () { return view('frontend.services'); });

// --- Informasi (Dinamis dari DB) ---
Route::get('/berita', function () {
    $berita = \App\Models\Berita::where('status', 'publish')->orderBy('published_at', 'desc')->orderBy('created_at', 'desc')->get();
    return view('frontend.berita.index', compact('berita'));
});
Route::get('/berita/{slug}', function ($slug) {
    $berita = \App\Models\Berita::where('slug', $slug)->firstOrFail();
    return view('frontend.berita.show', compact('berita'));
});
Route::get('/pengumuman', function () {
    $pengumuman = \App\Models\Pengumuman::where('status', 'publish')->orderBy('created_at', 'desc')->get();
    return view('frontend.pengumuman', compact('pengumuman'));
});
Route::get('/galeri', function () {
    $galeri = \App\Models\Galeri::with('items')->orderBy('created_at', 'desc')->get();
    return view('frontend.gallery', compact('galeri'));
});
Route::get('/unduhan', function () {
    $unduhan = \App\Models\Unduhan::orderBy('created_at', 'desc')->get();
    return view('frontend.unduhan', compact('unduhan'));
});

// --- Lembaga (Satuan Pendidikan) ---
Route::get('/lembaga', function () {
    $query = \App\Models\Sekolah::query();
    
    // Filter Pencarian (Nama / NPSN)
    if (request('search')) {
        $query->where(function($q) {
            $q->where('nama', 'like', '%' . request('search') . '%')
              ->orWhere('npsn', 'like', '%' . request('search') . '%');
        });
    }

    // Filter Kabupaten/Kota
    if (request('kabupaten')) {
        $query->where('kabupaten_kota', request('kabupaten'));
    }

    // Filter Kecamatan
    if (request('kecamatan')) {
        $query->where('kecamatan', request('kecamatan'));
    }

    // Filter Jenjang
    if (request('jenjang')) {
        $query->where('bentuk_pendidikan_id_str', request('jenjang'));
    }

    // Filter Status
    if (request('status')) {
        $query->where('status_sekolah_str', request('status'));
    }
    
    $sekolah = $query->orderBy('nama', 'asc')->paginate(20)->withQueryString();
    
    // Data untuk dropdown filter
    $listKabupaten = \App\Models\Sekolah::select('kabupaten_kota')->distinct()->whereNotNull('kabupaten_kota')->orderBy('kabupaten_kota')->get();
    $listJenjang = \App\Models\Sekolah::select('bentuk_pendidikan_id_str')->distinct()->whereNotNull('bentuk_pendidikan_id_str')->orderBy('bentuk_pendidikan_id_str')->get();
    $listStatus = \App\Models\Sekolah::select('status_sekolah_str')->distinct()->whereNotNull('status_sekolah_str')->orderBy('status_sekolah_str')->get();

    return view('frontend.lembaga.index', [
        'sekolah' => $sekolah,
        'listKabupaten' => $listKabupaten,
        'listJenjang' => $listJenjang,
        'listStatus' => $listStatus,
        'filters' => request()->all()
    ]);
});
Route::get('/lembaga/{id}', function ($id) {
    $sekolah = \App\Models\Sekolah::findOrFail($id);
    return view('frontend.lembaga.show', compact('sekolah'));
});
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
Route::get('/buku-tamu/wilayah-{wilayah}', [GuestBookController::class, 'index'])->name('guest.buku-tamu');
Route::post('/buku-tamu/wilayah-{wilayah}', [GuestBookController::class, 'store'])
    ->name('guest.buku-tamu.store')
    ->middleware('throttle:5,1'); // Maksimal 5x submit per menit per IP
Route::post('/buku-tamu/{id}/print', [GuestBookController::class, 'requestPrint'])->name('guest.buku-tamu.print');


/*
|--------------------------------------------------------------------------
| PANEL ADMIN (Backend)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', '2fa', 'stealth'])->group(function () {

    // --- RUTE SILUMAN ASET (GHAIB & STABIL) ---
    Route::get('/system/assets/{encoded_name}', function ($encoded_name) {
        $filename = base64_decode($encoded_name);
        $path = public_path('build/' . $filename);

        if (!file_exists($path)) abort(404);

        $content = file_get_contents($path);
        // Buang jejak SourceMap biar folder webpack:// Lenyap
        $content = preg_replace('/(\/\/[#@]\s*sourceMappingURL=.*|\/\*[\s\S]*?sourceMappingURL=[\s\S]*?\*\/)/is', '', $content);

        $type = str_ends_with($filename, '.css') ? 'text/css' : 'application/javascript';
        
        return response($content)->header('Content-Type', $type);
    })->name('system.assets');

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

    // --- MONITOR ANTRIAN TV (PUBLIC/OFFICE) ---
    Route::get('/display-antrian/wilayah-{wilayah}', [AntrianDisplayController::class, 'index'])->name('display.antrian');
    Route::get('/display-antrian/wilayah-{wilayah}/updates', [AntrianDisplayController::class, 'getUpdates'])->name('display.antrian.updates');
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
            Route::get('nonaktif', 'indexNonaktif')->name('nonaktif.index');
            Route::get('show-multiple', 'showMultiple')->name('show-multiple');
            Route::get('{id}', 'show')->name('show');
        });

    Route::prefix('kesiswaan')->name('kesiswaan.')
        ->middleware('check_menu:peserta-didik')
        ->group(function() {
            Route::get('siswa/rekapitulasi', [SiswaController::class, 'rekapitulasi'])->name('siswa.rekapitulasi');
            Route::get('siswa/rekapitulasi/export-excel', [SiswaController::class, 'exportRekapitulasi'])->name('siswa.rekapitulasi.export-excel');
            Route::get('siswa/export-excel', [SiswaController::class, 'exportExcel'])->name('siswa.export-excel');
            Route::get('siswa/nonaktif', [SiswaController::class, 'indexNonaktif'])->name('siswa.nonaktif.index');
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
        Route::get('profil', [\App\Http\Controllers\Admin\ProfilWebsiteController::class, 'index'])->name('profil.index')->middleware('check_menu:web-profil');
        Route::put('profil', [\App\Http\Controllers\Admin\ProfilWebsiteController::class, 'update'])->name('profil.update')->middleware('check_menu:web-profil');

        // --- ROUTE STRUKTUR ORGANISASI ---
        Route::resource('struktur', \App\Http\Controllers\Admin\StrukturOrganisasiController::class)->except(['create', 'show', 'edit'])->middleware('check_menu:web-struktur');

        // --- KELOLA KONTEN ---
        Route::resource('berita', \App\Http\Controllers\Admin\BeritaController::class)->except(['create', 'show', 'edit'])->middleware('check_menu:web-berita');
        Route::resource('pengumuman', \App\Http\Controllers\Admin\PengumumanController::class)->except(['create', 'show', 'edit'])->middleware('check_menu:web-pengumuman');
        Route::resource('galeri', \App\Http\Controllers\Admin\GaleriController::class)->except(['create', 'show', 'edit'])->middleware('check_menu:web-galeri');
        Route::delete('galeri/item/{id}', [\App\Http\Controllers\Admin\GaleriController::class, 'destroyItem'])->name('galeri.item.destroy');
        Route::resource('unduhan', \App\Http\Controllers\Admin\UnduhanController::class)->except(['create', 'show', 'edit'])->middleware('check_menu:web-unduhan');

        Route::resource('sliders', SliderController::class)->middleware('check_menu:web-slider');
        Route::get('welcome', [WelcomeMessageController::class, 'index'])->name('welcome.index')->middleware('check_menu:web-welcome');
        Route::post('welcome', [WelcomeMessageController::class, 'update'])->name('welcome.update')->middleware('check_menu:web-welcome');
        Route::get('settings', [WebSettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [WebSettingController::class, 'update'])->name('settings.update');
        });
    Route::get('/underConstructions', function () {
        return view('admin.underConstruction');
    })->name('underConstructions');
});

require __DIR__ . '/auth.php';
