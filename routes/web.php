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
use App\Http\Controllers\Admin\Kepegawaian\TugasPegawaiKcdController; // <--- Wajib ada

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

Route::get('/cek-injeksi', function() {
    $user = Illuminate\Support\Facades\Auth::user();
    if(!$user) return "Login dulu!";

    echo "<h3>1. CEK USER</h3>";
    echo "Role: " . $user->role . " (Harus 'Pegawai')<br>";
    echo "ID: " . $user->pegawai_kcd_id . " (Harus 8)<br>";
    
    // Cek Kesamaan Role (Case Insensitive)
    $isRoleMatch = strcasecmp($user->role, 'Pegawai') === 0;
    echo "Role Match? " . ($isRoleMatch ? "<b style='color:green'>YES</b>" : "<b style='color:red'>NO (Cek ejaan role di tabel users)</b>") . "<br>";

    echo "<hr><h3>2. CEK DATABASE TUGAS</h3>";
    $tugas = \App\Models\TugasPegawaiKcd::where('pegawai_kcd_id', $user->pegawai_kcd_id)
                                        ->where('is_active', 1)
                                        ->first();
    
    if($tugas) {
        echo "Tugas Ditemukan: <b style='color:green'>" . $tugas->kategori_layanan . "</b><br>";
    } else {
        echo "<b style='color:red'>TUGAS TIDAK DITEMUKAN / TIDAK AKTIF!</b><br>";
        return; // Stop diagnosa
    }

    echo "<hr><h3>3. CEK MAPPING</h3>";
    $mapKategoriToSlug = [
        'kenaikan-pangkat' => 'layanan-kp',
        'kgb'              => 'layanan-kgb',
        'mutasi'           => 'layanan-mutasi',
        'relokasi'         => 'layanan-relokasi',
        'satya-lencana'    => 'layanan-satya',
        'hukuman-disiplin' => 'layanan-hukdis',
        'verifikasi-surat' => 'verifikasi-surat',
    ];
    
    $kategori = $tugas->kategori_layanan;
    if(isset($mapKategoriToSlug[$kategori])) {
        echo "Mapping OK. Slug Target: <b>" . $mapKategoriToSlug[$kategori] . "</b><br>";
    } else {
        echo "<b style='color:red'>MAPPING GAGAL!</b> Kategori '".$kategori."' tidak ada di daftar array.<br>";
        echo "Cek ejaan 'kategori_layanan' di tabel tugas_pegawai_kcds. Harus persis sama.";
    }

    echo "<hr><h3>4. CEK CONFIG AKHIR</h3>";
    $config = config('sidebar_menu.role_map.Pegawai'); // Cek key standar
    echo "Menu Pegawai saat ini:<pre>";
    print_r($config);
    echo "</pre>";

    if(in_array('layanan-gtk', $config ?? [])) {
        echo "<h1>✅ STATUS: INJEKSI BERHASIL</h1>";
        echo "Kalau masih gak muncul di sidebar, berarti masalahnya di file <b>SidebarHelper.php</b>";
    } else {
        echo "<h1>❌ STATUS: INJEKSI GAGAL</h1>";
        echo "AppServiceProvider tidak berhasil menyuntikkan menu.";
    }
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
    | 2.A PENUGASAN PEGAWAI (TUGAS & AKSES LAYANAN) - [PRIORITAS UTAMA]
    |--------------------------------------------------------------------------
    | URL: admin/kepegawaian/tugas-internal
    | PENTING: Ditaruh DI ATAS Kepegawaian Data agar tidak dianggap sebagai {id}
    */
    Route::controller(TugasPegawaiKcdController::class)
        ->prefix('kepegawaian/tugas-internal')
        ->name('kepegawaian.tugas-kcd.')
        ->middleware('check_menu:kepegawaian-tugas') 
        ->group(function() {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });

    /*
    |--------------------------------------------------------------------------
    | 2.B KEPEGAWAIAN KCD (DATA PEGAWAI)
    |--------------------------------------------------------------------------
    | Karena ini punya route dinamis /{id}, harus ditaruh SETELAH route spesifik.
    */
    Route::prefix('kepegawaian')->name('kepegawaian.')->controller(PegawaiKcdController::class)->group(function() {
        
        // Route Khusus Menu 'Profil Saya' (Pegawai)
        Route::get('/profil-saya', 'showMe')->name('me');

        // Admin Only (Using new slug 'kepegawaian-data')
        Route::middleware('check_menu:kepegawaian-data')->group(function() {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::delete('/{id}', 'destroy')->name('destroy');
            Route::put('/{id}/reset', 'resetPassword')->name('reset');
        });

        // Update Profil (HATI-HATI: Route ini menangkap semua URL /kepegawaian/{apa-saja})
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
        // Tahap 1: Pegawai Atur Syarat
        Route::put('/{id}/set-syarat', [VerifikasiController::class, 'setSyarat'])->name('set_syarat');
        
        // Tahap 2: Pegawai Cek Berkas Upload
        Route::put('/{id}/process', [VerifikasiController::class, 'verifyProcess'])->name('process');
        
        // Tahap 3: Kasubag Validasi
        Route::put('/{id}/kasubag-process', [VerifikasiController::class, 'kasubagProcess'])->name('kasubag_process');
        
        // Tahap 4: Kepala Approval & Terbit SK
        Route::put('/{id}/kepala-process', [VerifikasiController::class, 'kepalaProcess'])->name('kepala_process');
    });

    // Halaman Under Construction
    Route::get('/underConstructions', function () {
        return view('admin.underConstruction');
    })->name('underConstructions');

});

require __DIR__ . '/auth.php';