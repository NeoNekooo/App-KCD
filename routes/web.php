<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthenticatedSessionController;

// --- Controller dari V1 ---
use App\Http\Controllers\Admin\Settings\ApiSettingsController;
use App\Http\Controllers\Admin\Settings\SekolahController;
use App\Http\Controllers\Admin\Kepegawaian\GtkController;
use App\Http\Controllers\Admin\Kepegawaian\TugasPegawaiController;
use App\Http\Controllers\Admin\Kepegawaian\TempalateSuratSkController;

// Controller Akademik
use App\Http\Controllers\Admin\Akademik\TapelController;
use App\Http\Controllers\Admin\Akademik\JurusanController;
use App\Http\Controllers\Admin\Akademik\MapelController; 
use App\Http\Controllers\Admin\Akademik\DaftarEkstrakurikulerController;
use App\Http\Controllers\Admin\Akademik\JadwalPelajaranController;

// Controller Kesiswaan
use App\Http\Controllers\Admin\Kesiswaan\SiswaController;

// Controller ppdb

// Controller Ppdb
use App\Http\Controllers\Admin\Ppdb\DaftarPesertaDidikBaruController;
use App\Http\Controllers\Admin\Ppdb\DaftarCalonPesertaDidikController;
use App\Http\Controllers\Admin\Ppdb\PemberianNisController;
use App\Http\Controllers\Admin\Ppdb\FormulirPendaftaranController;
use App\Http\Controllers\Admin\Ppdb\JalurController;
use App\Http\Controllers\Admin\Ppdb\TingkatPendaftaranController;
use App\Http\Controllers\Admin\Ppdb\KompetensiPendaftaranController;
use App\Http\Controllers\Admin\Ppdb\KelasPendaftaranController;
use App\Http\Controllers\Admin\Ppdb\LaporanPendaftaranController;
use App\Http\Controllers\Admin\Ppdb\LaporanQuotaController;
use App\Http\Controllers\Admin\Ppdb\PenempatanKelasController;
use App\Http\Controllers\Admin\Ppdb\QuotaController;
use App\Http\Controllers\Admin\Ppdb\SyaratController;
use App\Http\Controllers\Admin\Ppdb\TahunPpdbController;

// Controller Alumni
use App\Http\Controllers\Admin\Alumni\AlumniController;

// Controller Indisipliner
use App\Http\Controllers\Admin\Indisipliner\IndisiplinerGtkController;
use App\Http\Controllers\Admin\Indisipliner\IndisiplinerSiswaController;
    
// Controller Absensi
use App\Http\Controllers\Admin\Absensi\AbsensiSiswaController;
use App\Http\Controllers\Admin\Absensi\AbsensiGtkController;
use App\Http\Controllers\Admin\Absensi\AbsensiMapelController;
use App\Http\Controllers\Admin\Pengaturan\HariLiburController;
use App\Http\Controllers\Admin\Pengaturan\PengaturanAbsensiController;
use App\Http\Controllers\Admin\Laporan\LaporanAbsensiController;

// Controller Rombongan Belajar
use App\Http\Controllers\Admin\Rombel\RombelRegulerController;
use App\Http\Controllers\Admin\Rombel\RombelPraktikController;
use App\Http\Controllers\Admin\Rombel\RombelEkstrakurikulerController;
use App\Http\Controllers\Admin\Rombel\RombelMapelPilihanController;
use App\Http\Controllers\Admin\Rombel\RombelWaliController;

// Controller Landing
use App\Http\Controllers\Admin\Landing\PpdbController;

// Controller Keuangan
use App\Http\Controllers\Bendahara\Keuangan\IuranController;
use App\Http\Controllers\Bendahara\Keuangan\KasController;
use App\Http\Controllers\Bendahara\Keuangan\PembayaranController;
use App\Http\Controllers\Bendahara\Keuangan\PengeluaranController;
use App\Http\Controllers\Bendahara\Keuangan\VoucherController;
use App\Http\Controllers\Bendahara\Keuangan\TagihanController;
use App\Http\Controllers\Bendahara\Keuangan\MasterKasController;


// --- Controller Administrasi (Surat) ---
use App\Http\Controllers\Admin\Administrasi\TipeSuratController;
use App\Http\Controllers\Admin\Administrasi\SuratKeluarSiswaController;
use App\Http\Controllers\Admin\Administrasi\SuratKeluarGuruController; // Controller Baru
use App\Http\Controllers\Admin\Administrasi\SuratMasukController;
        
/*
|--------------------------------------------------------------------------
| Rute Web Utama
|--------------------------------------------------------------------------
*/

// Menggunakan 'welcome' dari V2
Route::get('/', function () {
    return view('auth.login-custom');
});

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
         ->middleware('auth')
         ->name('logout');


/*
|--------------------------------------------------------------------------
| Grup Rute untuk Panel Admin
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

    // Dashboard (dari V1)
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Personal
    |--------------------------------------------------------------------------
    */
    Route::prefix('personal')->name('personal.')->group(function () {
        Route::prefix('gtk')->name('gtk.')->group(function () {
            Route::get('profil', [GtkController::class, 'profil'])->name('profil');
            Route::get('pelanggaran',[GtkController::class, 'pelanggaran'])->name('pelanggaran');
        });
        Route::prefix('siswa')->name('siswa.')->group(function () {
            Route::get('profil', [SiswaController::class, 'profil'])->name('profil');
            Route::get('pelanggaran',[SiswaController::class, 'pelanggaran'])->name('pelanggaran');
        });
    });

    // --- GRUP PENGATURAN --- (dari V1)
    Route::prefix('pengaturan')->name('pengaturan.')->group(function () {
        Route::get('sekolah', [SekolahController::class, 'index'])->name('sekolah.index');
        Route::put('sekolah', [SekolahController::class, 'update'])->name('sekolah.update');
        Route::get('/webservice', [ApiSettingsController::class, 'index'])->name('webservice.index');
        Route::put('/webservice', [ApiSettingsController::class, 'update'])->name('webservice.update');
    });
    
    // --- GRUP KEPEGAWAIAN --- (Struktur dari V1)
    Route::prefix('kepegawaian')->name('kepegawaian.')->group(function () {
        // === PERBAIKAN: HAPUS ->names([...]) ===
        // Otomatis menjadi: admin.kepegawaian.TemplateSk.index, dll
        Route::resource('TemplateSk', TempalateSuratSkController::class);
        
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
        Route::get('gtk/cetak-pdf-multiple', [GtkController::class, 'cetakPdfMultiple'])->name('gtk.cetak_pdf_multiple');
        Route::put('gtk/{id}/update-data', [GtkController::class, 'updateData'])->name('gtk.update_data');
        Route::post('gtk/{id}/upload-media', [GtkController::class, 'uploadMedia'])->name('gtk.upload_media');

       // 4. Route Cetak Kartu ID GTK
        Route::controller(GtkController::class)->prefix('gtk/cetak-kartu')->name('gtk.')->group(function() {
            Route::get('/', 'indexCetakKartu')->name('index-cetak-kartu');
            Route::get('/print/{id}', 'cetakKartu')->name('print-kartu');
            Route::get('/print-all', 'cetakSemua')->name('print-all');
            Route::post('/upload-background', 'uploadBackgroundKartu')->name('upload-background-kartu');
        });

        Route::prefix('tugas-pegawai')->name('tugas-pegawai.')->group(function () {
        Route::get('/', [TugasPegawaiController::class, 'index'])->name('index');
        Route::post('/sync', [TugasPegawaiController::class, 'syncDariRombel'])->name('sync');
        Route::post('/store', [TugasPegawaiController::class, 'store'])->name('store');
        Route::post('/update-sk', [TugasPegawaiController::class, 'updateSk'])->name('update-sk');
        Route::get('/detail/{id}', [TugasPegawaiController::class, 'getDetail'])->name('detail');
        Route::delete('/destroy/{id}', [TugasPegawaiController::class, 'destroy'])->name('destroy');
        Route::post('/cetak/{id}', [TugasPegawaiController::class, 'cetak'])->name('cetak');
        Route::delete('/destroy-detail/{id}', [TugasPegawaiController::class, 'destroyDetail'])->name('destroy-detail');
        Route::put('/update/{id}', [TugasPegawaiController::class, 'update'])->name('update');
    });

    });

    // 2. --- GRUP AKADEMIK ---
    Route::prefix('akademik')->name('akademik.')->group(function () {
        
        // Tapel (Tahun Pelajaran)
        Route::controller(TapelController::class)->group(function () {
            Route::get('tapel', 'index')->name('tapel.index');
            Route::get('tapel/sinkron', 'sinkron')->name('tapel.sinkron');
        });

        // Jurusan (Hanya Index)
        Route::resource('jurusan', JurusanController::class)->only(['index']);
        
        // Mata Pelajaran
        Route::get('mapel', [MapelController::class, 'index'])->name('mapel.index'); 
        
        // Ekstrakurikuler (Kecuali Show, Create, Edit karena mungkin pakai Modal/Ajax)
        Route::resource('daftar-ekstrakurikuler', DaftarEkstrakurikulerController::class)
             ->except(['show', 'create', 'edit']); 


        Route::resource('jadwal-pelajaran', JadwalPelajaranController::class);
        Route::get('jadwal-pelajaran/{id}/json', [JadwalPelajaranController::class, 'getJadwalJson'])->name('jadwal-pelajaran.json');
    });

    /*
    |--------------------------------------------------------------------------
    | Kesiswaan
    |--------------------------------------------------------------------------
    */
    Route::prefix('kesiswaan')->name('kesiswaan.')->group(function() {
        // 1. ROUTE SPESIFIK (JANGAN PAKAI {id} DISINI) - WAJIB DI ATAS RESOURCE
    Route::get('siswa/export/excel', [SiswaController::class, 'exportExcel'])->name('siswa.export.excel');
    Route::get('siswa/show-multiple', [SiswaController::class, 'showMultiple'])->name('siswa.show-multiple');
    
    // INI YANG TADI ERROR, SEKARANG PINDAH KE ATAS:
    Route::get('siswa/cetak-pdf-multiple', [SiswaController::class, 'cetakPdfMultiple'])->name('siswa.cetak_pdf_multiple'); 

    // 2. ROUTE CETAK MASSAL (Beda prefix, jadi aman di mana saja, tapi simpan di atas biar rapi)
    Route::get('/cetak-kartu-massal', [SiswaController::class, 'showCetakMassalIndex'])->name('siswa.cetak_massal_index');
    Route::get('/cetak-kartu-massal/{rombel}', [SiswaController::class, 'cetakKartuMassal'])->name('siswa.cetak_massal_show');

    // 3. ROUTE RESOURCE (Standar CRUD: index, create, store, show {id}, edit {id}, update {id}, destroy {id})
    // Route ini menangkap pola 'siswa/{id}'
    Route::resource('siswa', SiswaController::class);

    // 4. ROUTE DENGAN ID TAPI ADA TAMBAHAN URL (Ini aman di bawah resource)
    // Pola: siswa/{id}/sesuatu
    Route::get('siswa/{id}/cetak-kartu', [SiswaController::class, 'cetakKartu'])->name('siswa.cetak_kartu');
    Route::get('siswa/{id}/cetak-pdf', [SiswaController::class, 'cetakPdf'])->name('siswa.cetak_pdf');

    });

    /*
    |--------------------------------------------------------------------------
    | (PPDB)
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

        // Landing Ppdb
        Route::resource('landing', PpdbController::class);

        Route::post('submit', [PpdbController::class, 'submitForm'])->name('submit');
    });

    /*
    |--------------------------------------------------------------------------
    | (Alumni)
    |--------------------------------------------------------------------------
    */
    Route::prefix('alumni')->name('alumni.')->group(function() {
        
        Route::resource('dataAlumni', AlumniController::class)
        ->only(['index', 'show', 'update']);

        Route::get('show-multiple', [AlumniController::class, 'showMultiple'])
            ->name('show-multiple');

        Route::get('/rekapDataAlumni', [AlumniController::class,'rekapDataAlumni']);

        // Halaman index + filter kelas
        Route::get('/pelulusan', [AlumniController::class, 'lulus'])->name('pelulusan');

        // Proses pelulusan
        Route::post('/process', [AlumniController::class, 'process'])->name('process');
    });

    // --- GRUP INDISIPLINER ---
    Route::prefix('indisipliner')->name('indisipliner.')->group(function () {

        // === INDISIPLINER GURU & TENAGA KEPENDIDIKAN ===
        Route::prefix('guru')->name('guru.')->group(function () {
            Route::get('/daftar', [IndisiplinerGtkController::class, 'daftarIndex'])->name('daftar.index');
            Route::post('/daftar', [IndisiplinerGtkController::class, 'store'])->name('daftar.store');
            Route::delete('/daftar/{pelanggaran}', [IndisiplinerGtkController::class, 'destroy'])->name('daftar.destroy');
        
            Route::get('/pengaturan', [IndisiplinerGtkController::class, 'pengaturanIndex'])->name('pengaturan.index');
            Route::post('/pengaturan/kategori', [IndisiplinerGtkController::class, 'storeKategori'])->name('pengaturan.kategori.store');
            Route::post('/pengaturan/poin', [IndisiplinerGtkController::class, 'storePoin'])->name('pengaturan.poin.store');
            Route::put('/pengaturan/poin/{id}', [IndisiplinerGtkController::class, 'updatePoin'])->name('pengaturan.poin.update');
            Route::delete('/pengaturan/poin/{id}', [IndisiplinerGtkController::class, 'destroyPoin'])->name('pengaturan.poin.destroy');
        
            Route::post('/pengaturan/sanksi', [IndisiplinerGtkController::class, 'storeSanksi'])->name('pengaturan.sanksi.store');
            Route::put('/pengaturan/sanksi/{id}', [IndisiplinerGtkController::class, 'updateSanksi'])->name('pengaturan.sanksi.update');
            Route::delete('/pengaturan/sanksi/{id}', [IndisiplinerGtkController::class, 'destroySanksi'])->name('pengaturan.sanksi.destroy');
        
            Route::get('/rekapitulasi', [IndisiplinerGtkController::class, 'rekapitulasiIndex'])->name('rekapitulasi.index');
            Route::get('/rekapitulasi/cetak/semua', [IndisiplinerGtkController::class, 'cetakSemua'])->name('rekapitulasi.cetak.semua');
            Route::get('/rekapitulasi/cetak/{namaGuru}', [IndisiplinerGtkController::class, 'cetakIndividu'])->name('rekapitulasi.cetak.individu');
            Route::get('/rekapitulasi/cetak-surat/{namaGuru}', [IndisiplinerGtkController::class, 'cetakSurat'])->name('rekapitulasi.cetak.surat');
        
        });


        // === INDISIPLINER SISWA ===
        Route::prefix('siswa')->name('siswa.')->group(function () {

            // PENGATURAN
            Route::get('pengaturan', [IndisiplinerSiswaController::class, 'pengaturanIndex'])->name('pengaturan.index');
            Route::post('pengaturan/kategori', [IndisiplinerSiswaController::class, 'storeKategori'])->name('pengaturan.kategori.store');
            Route::put('pengaturan/kategori/{pelanggaranKategori}', [IndisiplinerSiswaController::class, 'updateKategori'])->name('pengaturan.kategori.update');
            Route::delete('pengaturan/kategori/{pelanggaranKategori}', [IndisiplinerSiswaController::class, 'destroyKategori'])->name('pengaturan.kategori.destroy');

            Route::post('pengaturan/poin', [IndisiplinerSiswaController::class, 'storePoin'])->name('pengaturan.poin.store');
            Route::put('pengaturan/poin/{pelanggaranPoin}', [IndisiplinerSiswaController::class, 'updatePoin'])->name('pengaturan.poin.update');
            Route::delete('pengaturan/poin/{pelanggaranPoin}', [IndisiplinerSiswaController::class, 'destroyPoin'])->name('pengaturan.poin.destroy');

            Route::post('pengaturan/sanksi', [IndisiplinerSiswaController::class, 'storeSanksi'])->name('pengaturan.sanksi.store');
            Route::put('pengaturan/sanksi/{pelanggaranSanksi}', [IndisiplinerSiswaController::class, 'updateSanksi'])->name('pengaturan.sanksi.update');
            Route::delete('pengaturan/sanksi/{pelanggaranSanksi}', [IndisiplinerSiswaController::class, 'destroySanksi'])->name('pengaturan.sanksi.destroy');

            // DAFTAR PELANGGARAN
            Route::get('daftar', [IndisiplinerSiswaController::class, 'daftarIndex'])->name('daftar.index');
            Route::post('daftar', [IndisiplinerSiswaController::class, 'store'])->name('daftar.store');
            Route::delete('daftar/{pelanggaran}', [IndisiplinerSiswaController::class, 'destroy'])->name('daftar.destroy');

            // AJAX & API
            Route::get('get-rombel-details/{rombelId}', [IndisiplinerSiswaController::class, 'getRombelDetails'])->name('getRombelDetails');
            Route::get('get-rombels-by-tingkat', [IndisiplinerSiswaController::class, 'getRombelsByTingkat'])->name('getRombelsByTingkat');
            Route::get('get-siswa-by-qr/{qrToken}', [IndisiplinerSiswaController::class, 'findSiswaByQr'])->name('getSiswaByQr');

            // KIOS HIBRIDA
            Route::get('kiosk', [IndisiplinerSiswaController::class, 'kioskIndex'])->name('kiosk.index');
            Route::get('/api/kiosk-find-siswa/{qrToken}', [IndisiplinerSiswaController::class, 'kioskFindSiswa'])->name('kiosk.findSiswa');
            Route::post('/api/kiosk-store', [IndisiplinerSiswaController::class, 'kioskStore'])->name('kiosk.store');

            // REKAPITULASI & CETAK
            Route::get('rekapitulasi', [IndisiplinerSiswaController::class, 'rekapitulasiIndex'])->name('rekapitulasi.index');

            // PERBAIKAN DI SINI:
            // Cukup gunakan 'rekapitulasi/cetak/{nipd}' dan 'rekapitulasi.cetak'
            // Karena sudah di dalam grup 'admin' -> 'indisipliner' -> 'siswa'
            Route::get('rekapitulasi/cetak/{nipd}', [IndisiplinerSiswaController::class, 'rekapitulasiCetak'])
                ->name('rekapitulasi.cetak');
            Route::get('rekapitulasi/cetak-sp/{nipd}/{sanksiId}', [IndisiplinerSiswaController::class, 'cetakSp'])
                ->name('rekapitulasi.cetak-sp');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Laporan Absensi
    |--------------------------------------------------------------------------
    */
    Route::prefix('laporan')->name('laporan.')->group(function() {
        Route::get('absensi', [LaporanAbsensiController::class, 'index'])->name('absensi.index');
        Route::get('absensi/dashboard', [LaporanAbsensiController::class, 'dashboard'])->name('absensi.dashboard');
        Route::get('absensi/export', [LaporanAbsensiController::class, 'export'])->name('absensi.export');
            Route::get('absensi/tanpa-pulang', [LaporanAbsensiController::class, 'laporanTanpaPulang'])->name('absensi.tanpa_pulang');
            Route::get('absensi/bulanan', [LaporanAbsensiController::class, 'laporanBulanan'])->name('absensi.bulanan');
    });

    /*
    |--------------------------------------------------------------------------
    | Absensi
    |--------------------------------------------------------------------------
    */
    Route::prefix('absensi')->name('absensi.')->group(function () {
        
        Route::prefix('gtk')->name('gtk.')->group(function() {
            // --- Route KiosK (Sudah ada) ---
            Route::get('scanner', [AbsensiGtkController::class, 'showScanner'])->name('scanner');
            Route::post('scan-handler', [AbsensiGtkController::class, 'handleScan'])->name('scan');
            Route::get('api/get_todays_scans', [AbsensiGtkController::class, 'getTodaysScans'])->name('get_todays_scans');
            Route::get('api/get_recent_scans', [AbsensiGtkController::class, 'getRecentScans'])->name('get_recent_scans');
            Route::get('api/get_unscanned_data', [AbsensiGtkController::class, 'getUnscanned_data'])->name('get_unscanned_data');

            // --- [BARU] Route Absensi Manual GTK ---
            Route::get('manual', [AbsensiGtkController::class, 'index'])->name('index');
            Route::post('manual', [AbsensiGtkController::class, 'store'])->name('store');

            // --- [BARU] Route Laporan Absensi GTK ---
            Route::get('laporan', [AbsensiGtkController::class, 'laporan'])->name('laporan');
                });
        Route::prefix('siswa')->name('siswa.')->group(function() {
            Route::get('/todays-scans', [AbsensiSiswaController::class, 'getTodaysScans'])->name('get_todays_scans');
            Route::get('/get-recent-scans', [AbsensiSiswaController::class, 'getRecentScans'])->name('get_recent_scans');
            Route::get('/absensi/get-unscanned-data', [AbsensiSiswaController::class, 'getUnscannedData'])->name('get_unscanned_data');
            
            // Rute untuk menampilkan halaman PILIH KELAS
            // URL: /admin/absensi/siswa
            Route::get('/', [AbsensiSiswaController::class, 'index'])->name('index');
        
            // Rute untuk menampilkan FORM ABSENSI untuk kelas & tanggal tertentu
            // URL: /admin/absensi/siswa/form?rombel_id=...&tanggal=...
            Route::get('/form', [AbsensiSiswaController::class, 'show'])->name('show_form');
        
            // Rute untuk MENYIMPAN data absensi dari form
            // URL: /admin/absensi/siswa (Method: POST)
            Route::post('/', [AbsensiSiswaController::class, 'store'])->name('store');
            
            // Rute untuk scanner QR Code
            // URL: /admin/absensi/siswa/scanner
            Route::get('/scanner', [AbsensiSiswaController::class, 'showScanner'])->name('show_scanner');
            
            // Rute untuk menangani data dari scanner
            // URL: /admin/absensi/siswa/handle-scan (Method: POST)
            Route::post('/handle-scan', [AbsensiSiswaController::class, 'handleScan'])->name('handle_scan');
        });

        Route::resource('izin-siswa', \App\Http\Controllers\Admin\Absensi\IzinSiswaController::class);
        Route::get('mapel', [AbsensiMapelController::class, 'index'])->name('mapel.index');
        Route::get('mapel/show', [AbsensiMapelController::class, 'show'])->name('mapel.show'); // Pakai GET agar bisa di-bookmark
        Route::post('mapel/store', [AbsensiMapelController::class, 'store'])->name('mapel.store');
    });




    /*
    |--------------------------------------------------------------------------
    | Rombel
    |--------------------------------------------------------------------------
    */
    Route::prefix('rombel')->name('rombel.')->group(function () {
        // Reguler
        Route::get('/reguler/create', [RombelRegulerController::class, 'create'])->name('reguler.create');
        Route::get('/reguler', [RombelRegulerController::class, 'index'])->name('reguler.index');

        // Praktik
        Route::get('/praktik/create', [RombelPraktikController::class, 'create'])->name('praktik.create');
        Route::get('/praktik', [RombelPraktikController::class, 'index'])->name('praktik.index');

        // Ekstrakurikuler - Full Resource (index, create, store, edit, update, destroy)
        Route::resource('ekstrakurikuler', RombelEkstrakurikulerController::class)->except(['show']);

        // Mapel Pilihan
        Route::get('/mapel-pilihan/create', [RombelMapelPilihanController::class, 'create'])->name('mapel-pilihan.create');
        Route::get('/mapel-pilihan', [RombelMapelPilihanController::class, 'index'])->name('mapel-pilihan.index');

        // Wali
        Route::get('/wali/create', [RombelWaliController::class, 'create'])->name('wali.create');
        Route::get('/wali', [RombelWaliController::class, 'index'])->name('wali.index');
    });

    // 3. --- GRUP ADMINISTRASI (FITUR SURAT BARU) ---
    Route::prefix('administrasi')->name('administrasi.')->group(function () {
        Route::resource('tipe-surat', TipeSuratController::class);
        Route::resource('surat-keluar-siswa', SuratKeluarSiswaController::class);
        Route::get('get-siswa-by-kelas/{nama_rombel}', [SuratKeluarSiswaController::class, 'getSiswaByKelas'])
             ->name('get-siswa-by-kelas');
        Route::get('surat-keluar-guru', [SuratKeluarGuruController::class, 'index'])->name('surat-keluar-guru.index');
        Route::post('surat-keluar-guru/store', [SuratKeluarGuruController::class, 'store'])->name('surat-keluar-guru.store');
        Route::resource('surat-masuk', SuratMasukController::class);

    });


    /*
    |--------------------------------------------------------------------------
    | Pengaturan
    |--------------------------------------------------------------------------
    */
    Route::prefix('pengaturan')->name('pengaturan.')->group(function() {
        
        Route::get('absensi', [PengaturanAbsensiController::class, 'edit'])->name('absensi.edit');
        Route::put('absensi', [PengaturanAbsensiController::class, 'update'])->name('absensi.update');


        // Rute untuk Manajemen Hari Libur
        Route::resource('hari-libur', HariLiburController::class)->except(['show', 'edit', 'update']);
    });

    Route::get('/underConstructions', function () {
        return view('admin.underConstruction');
    })->name('underConstructions');

}); // Akhir dari grup 'admin'

Route::prefix('bendahara')->name('bendahara.')->group(function () {
    Route::prefix('keuangan')->name('keuangan.')->group(function () {
        Route::get('/penerimaan', [PembayaranController::class, 'index'])->name('penerimaan.index');
        Route::post('/penerimaan', [PembayaranController::class, 'store'])->name('penerimaan.store');
        Route::get('/kas', [KasController::class, 'index'])->name('kas.index');
        Route::resource('/iuran', IuranController::class)->except(['create', 'edit', 'show']);
        Route::resource('/voucher', VoucherController::class)->only(['index', 'store', 'destroy']);
        Route::resource('/pengeluaran', PengeluaranController::class)->except(['create', 'edit', 'show']);
        Route::resource('/kas-master', MasterKasController::class);
        Route::get('tagihan/generate', [TagihanController::class, 'create'])->name('tagihan.create');
        Route::post('tagihan/generate', [TagihanController::class, 'store'])->name('tagihan.store');
    });
});


// Menggunakan file auth standar dari V2
require __DIR__ . '/auth.php';