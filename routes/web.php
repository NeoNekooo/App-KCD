<?php

use Illuminate\Support\Facades\Route;

// Controller Settings
use App\Http\Controllers\Admin\Settings\ApiSettingsController;
use App\Http\Controllers\Admin\Settings\SekolahController;

// Controller Kepegawaian
use App\Http\Controllers\Admin\Kepegawaian\GtkController;
use App\Http\Controllers\Admin\Kepegawaian\TugasPegawaiController;

// Controller Akademik
use App\Http\Controllers\Admin\Akademik\SemesterController;
use App\Http\Controllers\Admin\Akademik\TapelController;
use App\Http\Controllers\Admin\Akademik\ProgramKeahlianController;
use App\Http\Controllers\Admin\Akademik\PaketKeahlianController; 
use App\Http\Controllers\Admin\Akademik\JurusanController;

// Controller Kesiswaan
use App\Http\Controllers\Admin\Kesiswaan\DaftarCalonPesertaDidikController;
use App\Http\Controllers\Admin\Kesiswaan\FormulirPendaftaranController;
use App\Http\Controllers\Admin\Kesiswaan\JalurController;
use App\Http\Controllers\Admin\Kesiswaan\LaporanPendaftaranController;
use App\Http\Controllers\Admin\Kesiswaan\LaporanQuotaController;
use App\Http\Controllers\Admin\Kesiswaan\PenempatanKelasController;
use App\Http\Controllers\Admin\Kesiswaan\QuotaController;
use App\Http\Controllers\Admin\Kesiswaan\SiswaController;
use App\Http\Controllers\Admin\Kesiswaan\SyaratController;
use App\Http\Controllers\Admin\Kesiswaan\TahunPpdbController;

/*
|--------------------------------------------------------------------------
| Rute Web Utama
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    // Arahkan ke dashboard admin saat membuka halaman utama
    return redirect()->route('admin.dashboard');
});


/*
|--------------------------------------------------------------------------
| Grup Rute untuk Panel Admin
|--------------------------------------------------------------------------
| Semua rute di bawah ini akan memiliki prefix 'admin' dan nama 'admin.'
| Contoh: route('admin.dashboard'), url('/admin/dashboard')
*/
Route::prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // --- GRUP PENGATURAN ---
    Route::prefix('pengaturan')->name('pengaturan.')->group(function() {
       Route::get('sekolah', [SekolahController::class, 'index'])->name('sekolah.index');
    Route::put('sekolah', [SekolahController::class, 'update'])->name('sekolah.update');
        Route::get('/webservice', [ApiSettingsController::class, 'index'])->name('webservice.index');
    });

    // --- GRUP KEPEGAWAIAN ---
     Route::prefix('kepegawaian')->name('kepegawaian.')->group(function() {
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
        // Route untuk Tugas Pegawai
        Route::resource('tugas-pegawai', TugasPegawaiController::class)->except(['create', 'edit', 'show']);
    });


    // --- GRUP AKADEMIK ---
    Route::prefix('akademik')->name('akademik.')->group(function () {
        Route::controller(TapelController::class)->prefix('tapel')->name('tapel.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::delete('/{tapel}', 'destroy')->name('destroy');
            Route::patch('/{tapel}/toggle', 'toggleStatus')->name('toggle');
        });

        Route::controller(SemesterController::class)->prefix('semester')->name('semester.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::patch('/{semester}/toggle', 'toggle')->name('toggle');
        });
    });

    // --- GRUP KESISWAAN ---
    Route::prefix('kesiswaan')->name('kesiswaan.')->group(function() {
        Route::resource('siswa', SiswaController::class);

        Route::prefix('ppdb')->name('ppdb.')->group(function () {
            Route::resource('tahun-ppdb', TahunPpdbController::class);
            Route::post('/tahun-ppdb/{id}/toggle-active', [TahunPpdbController::class, 'toggleActive'])->name('tahun-ppdb.toggleActive');
            
            Route::resource('jalur-ppdb', JalurController::class);
            Route::post('/jalur-ppdb/{id}/toggle-active', [JalurController::class, 'toggleActive'])->name('jalur-ppdb.toggleActive');

            Route::resource('quota-ppdb', QuotaController::class);

            Route::resource('syarat-ppdb', SyaratController::class);
            Route::post('/syarat-ppdb/{id}/toggle-active', [SyaratController::class, 'toggleActive'])->name('syarat-ppdb.toggleActive');

            Route::resource('formulir-ppdb', FormulirPendaftaranController::class);
            Route::resource('daftar-calon-peserta-didik', DaftarCalonPesertaDidikController::class);
            Route::resource('penempatan-kelas', PenempatanKelasController::class);
            Route::resource('laporan-pendaftaran', LaporanPendaftaranController::class);
            Route::resource('laporan-quota', LaporanQuotaController::class);
        });
    });

}); // Akhir dari grup 'admin'  

// Anda bisa menambahkan route untuk auth di sini jika perlu
// require __DIR__.'/auth.php';

