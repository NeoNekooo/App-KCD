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

        // --- RUTE BARU: CETAK KARTU ID ---
        // Note: Nama route otomatis menjadi 'admin.kepegawaian.gtk.index-cetak-kartu' karena berada dalam grup.
        Route::get('gtk/cetak-kartu', [GtkController::class, 'indexCetakKartu'])->name('gtk.index-cetak-kartu');
        Route::get('gtk/cetak-kartu/{id}/print', [GtkController::class, 'cetakKartu'])->name('gtk.print-kartu');
        Route::post('gtk/cetak-kartu/massal', [GtkController::class, 'cetakKartuMassal'])->name('gtk.print-kartu-massal');
        Route::get('gtk/cetak-kartu/print-all', [GtkController::class, 'cetakSemua'])->name('gtk.print-all');
        Route::post('gtk/cetak-kartu/upload-background', [GtkController::class, 'uploadBackgroundKartu'])->name('gtk.upload-background-kartu');
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
        Route::resource('ekstrakurikuler', EkstrakurikulerController::class)->except(['show', 'create', 'edit']); 
        Route::resource('ekstrakurikuler', EkstrakurikulerController::class);
        Route::resource('jadwal-pelajaran', JadwalPelajaranController::class);
        Route::get('jadwal-pelajaran/{id}/json', [JadwalPelajaranController::class, 'getJadwalJson'])->name('jadwal-pelajaran.json');

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
        | Kesiswaan (Siswa)
        |--------------------------------------------------------------------------
        */
        Route::get('/siswa/{siswa}/cetak-kartu', [SiswaController::class, 'cetakKartu'])->name('siswa.cetak_kartu');
        // Rute untuk menampilkan halaman pemilihan kelas
        Route::get('/cetak-kartu-massal', [SiswaController::class, 'showCetakMassalIndex'])->name('siswa.cetak_massal_index');
        // Rute untuk menampilkan halaman cetak untuk kelas yang dipilih
        Route::get('/cetak-kartu-massal/{rombel}', [SiswaController::class, 'cetakKartuMassal'])->name('siswa.cetak_massal_show');
        Route::resource('siswa', SiswaController::class);

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
    
    // ============================================================
    // --- TAMBAHKAN DUA BARIS INI UNTUK MEMPERBAIKI ERROR ---
    Route::put('/pengaturan/sanksi/{id}', [IndisiplinerGtkController::class, 'updateSanksi'])->name('pengaturan.sanksi.update');
    Route::delete('/pengaturan/sanksi/{id}', [IndisiplinerGtkController::class, 'destroySanksi'])->name('pengaturan.sanksi.destroy');
    // ============================================================
    
    Route::get('/rekapitulasi', [IndisiplinerGtkController::class, 'rekapitulasiIndex'])->name('rekapitulasi.index');
    Route::get('/rekapitulasi/cetak/semua', [IndisiplinerGtkController::class, 'cetakSemua'])->name('rekapitulasi.cetak.semua');
    Route::get('/rekapitulasi/cetak/{namaGuru}', [IndisiplinerGtkController::class, 'cetakIndividu'])->name('rekapitulasi.cetak.individu');
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

            // AJAX: GET SISWA BERDASARKAN ROMBEL
            // TAMBAHKAN INI:
            Route::get('get-rombel-details/{rombelId}', [IndisiplinerSiswaController::class, 'getRombelDetails'])->name('getRombelDetails');
            Route::get('get-rombels-by-tingkat', [IndisiplinerSiswaController::class, 'getRombelsByTingkat'])->name('getRombelsByTingkat');
            Route::get('get-siswa-by-qr/{qrToken}', [IndisiplinerSiswaController::class, 'findSiswaByQr'])->name('getSiswaByQr');
            // 1. Halaman untuk menampilkan Kios (untuk OSIS/Piket)
            Route::get('kiosk', [IndisiplinerSiswaController::class, 'kioskIndex'])->name('kiosk.index');

            // 2. API untuk mengambil nama siswa (dipanggil oleh scanner)
            Route::get('/api/kiosk-find-siswa/{qrToken}', [IndisiplinerSiswaController::class, 'kioskFindSiswa'])
                ->name('kiosk.findSiswa');

            // 3. API untuk menyimpan data pelanggaran dari Kios (dipanggil oleh tombol simpan)
            Route::post('/api/kiosk-store', [IndisiplinerSiswaController::class, 'kioskStore'])
                ->name('kiosk.store');

            // REKAPITULASI
            Route::get('rekapitulasi', [IndisiplinerSiswaController::class, 'rekapitulasiIndex'])->name('rekapitulasi.index');
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

        // Ekstrakurikuler
        Route::get('/ekstrakurikuler/create', [RombelEkstrakurikulerController::class, 'create'])->name('ekstrakurikuler.create');
        Route::get('/ekstrakurikuler', [RombelEkstrakurikulerController::class, 'index'])->name('ekstrakurikuler.index');

        // Mapel Pilihan
        Route::get('/mapel-pilihan/create', [RombelMapelPilihanController::class, 'create'])->name('mapel-pilihan.create');
        Route::get('/mapel-pilihan', [RombelMapelPilihanController::class, 'index'])->name('mapel-pilihan.index');

        // Wali
        Route::get('/wali/create', [RombelWaliController::class, 'create'])->name('wali.create');
        Route::get('/wali', [RombelWaliController::class, 'index'])->name('wali.index');
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