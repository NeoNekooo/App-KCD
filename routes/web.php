<?php

use Illuminate\Support\Facades\Route;
use App\Models\SyaratPendaftaran; // <---- tambahkan baris ini

// Controller Utama
use App\Http\Controllers\LandingPpdbController;
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

// Controller Kesiswaan
use App\Http\Controllers\Admin\Kesiswaan\ppdb\DaftarPesertaDidikBaruController;
use App\Http\Controllers\Admin\Kesiswaan\ppdb\DaftarCalonPesertaDidikController;
use App\Http\Controllers\Admin\Kesiswaan\ppdb\PemberianNisController;
use App\Http\Controllers\Admin\Kesiswaan\ppdb\FormulirPendaftaranController;
use App\Http\Controllers\Admin\Kesiswaan\ppdb\JalurController;
use App\Http\Controllers\Admin\Kesiswaan\ppdb\TingkatPendaftaranController;
use App\Http\Controllers\Admin\Kesiswaan\ppdb\KompetensiPendaftaranController;
use App\Http\Controllers\Admin\Kesiswaan\ppdb\KelasPendaftaranController;
use App\Http\Controllers\Admin\Kesiswaan\ppdb\LaporanPendaftaranController;
use App\Http\Controllers\Admin\Kesiswaan\ppdb\LaporanQuotaController;
use App\Http\Controllers\Admin\Kesiswaan\ppdb\PenempatanKelasController;
use App\Http\Controllers\Admin\Kesiswaan\ppdb\QuotaController;
use App\Http\Controllers\Admin\Kesiswaan\ppdb\SyaratController;
use App\Http\Controllers\Admin\Kesiswaan\ppdb\TahunPpdbController;

// Controller Landing
use App\Http\Controllers\Admin\Landing\PpdbController;

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
| Rute Panel Ppdb
|--------------------------------------------------------------------------
*/
Route::prefix('ppdb')->name('ppdb.')->group(function() {
    Route::get('/', [LandingPpdbController::class, 'beranda'])->name('beranda');
    Route::get('/kompetensi-keahlian', [LandingPpdbController::class, 'kompetensiKeahlian'])->name('kompetensiKeahlian');
    Route::get('/daftar-calon-siswa', [LandingPpdbController::class, 'daftarCalonSiswa'])->name('daftarCalonSiswa');
    Route::get('/formulir-pendaftaran', [LandingPpdbController::class, 'formulirPendaftaran'])->name('formulirPendaftaran');
    Route::get('/kontak', [LandingPpdbController::class, 'kontak'])->name('kontak');

    Route::get('/api/syarat-by-jalur/{jalurId}', function ($jalurId) {
        return SyaratPendaftaran::where('is_active', true)
            ->where('jalurPendaftaran_id', $jalurId)
            ->select('id', 'syarat', 'is_active')
            ->get();
    });


    // Tambahkan route POST untuk submit form pendaftaran
    Route::post('/formulir-pendaftaran/store', [LandingPpdbController::class, 'formulirStore'])->name('formulir.store');
});

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

    /*
    |--------------------------------------------------------------------------
    | Kesiswaan
    |--------------------------------------------------------------------------
    */
    Route::prefix('kesiswaan')->name('kesiswaan.')->group(function() {
        Route::resource('siswa', SiswaController::class);
        
        /*
        |--------------------------------------------------------------------------
        | Kesiswaan (PPDB)
        |--------------------------------------------------------------------------
        */
        Route::prefix('ppdb')->name('ppdb.')->group(function () {
            // Tahun Pelajaran PPDB
            Route::resource('tahun-ppdb', TahunPpdbController::class);
            Route::post('/tahun-ppdb/{id}/toggle-active', [TahunPpdbController::class, 'toggleActive'])->name('tahun-ppdb.toggleActive');

            // Jalur Pendaftaran
            Route::resource('jalur-ppdb', JalurController::class);
            Route::post('/jalur-ppdb/{id}/toggle-active', [JalurController::class, 'toggleActive'])->name('jalur-ppdb.toggleActive');

            // Tingkat Pendaftaran
            Route::resource('tingkat-ppdb', TingkatPendaftaranController::class);
            Route::post('/tingkat-ppdb/{id}/toggle-active', [TingkatPendaftaranController::class, 'toggleActive'])->name('tingkat-ppdb.toggleActive');

            // Kompetensi Pendaftaran
            Route::resource('kompetensi-ppdb', KompetensiPendaftaranController::class);

            // Kelas Pendaftaran
            Route::resource('kelas-ppdb', KelasPendaftaranController::class);

            // Quota Pendaftaran
            Route::resource('quota-ppdb', QuotaController::class);

            // Syarat Pendaftaran
            Route::resource('syarat-ppdb', SyaratController::class);
            Route::post('/syarat-ppdb/{id}/toggle-active', [SyaratController::class, 'toggleActive'])->name('syarat-ppdb.toggleActive');

            // Formulir Pendaftaran
            Route::resource('formulir-ppdb', FormulirPendaftaranController::class);
            Route::get('/get-syarat/{jalurId}', [SyaratController::class, 'getByJalur'])->name('ppdb.get-syarat');
            Route::patch('update-status/{id}', [FormulirPendaftaranController::class, 'updateStatus'])->name('updateStatus');

            // Pemberian NIS
            Route::get('pemberian-nis/generate', 
                [PemberianNisController::class, 'generate']
            )->name('pemberian-nis.generate');
            Route::resource('pemberian-nis', PemberianNisController::class);

            Route::get('daftar-calon/resi/{id}', [DaftarCalonPesertaDidikController::class, 'resi'])->name('daftar_calon.resi');

            // Data Peserta Didik
            Route::resource('daftar-calon-peserta-didik', DaftarCalonPesertaDidikController::class);
            Route::resource('daftar-peserta-didik-baru', DaftarPesertaDidikBaruController::class);

            // Penempatan Kelas
            Route::resource('penempatan-kelas', PenempatanKelasController::class);
            Route::post('penempatan-kelas/update-kelas', 
                [PenempatanKelasController::class, 'updateKelas']
            )->name('penempatan-kelas.update-kelas');


            // Laporan
            Route::resource('laporan-pendaftaran', LaporanPendaftaranController::class);
            Route::resource('laporan-quota', LaporanQuotaController::class);


        });

    });

    /*
        |--------------------------------------------------------------------------
        | Kesiswaan (PPDB)
        |--------------------------------------------------------------------------
        */
    Route::prefix('ppdb')->name('ppdb.')->group(function () {

        Route::resource('landing', PpdbController::class);

        Route::post('submit', [PpdbController::class, 'submitForm'])->name('submit');
    });

}); // Akhir dari grup 'admin'


// Menggunakan file auth standar dari V2
require __DIR__ . '/auth.php';