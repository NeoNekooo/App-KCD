<?php
// File: config/menu_access.php

return [
    /*
    |--------------------------------------------------------------------------
    | ROLE MAPPING: Konfigurasi Akses Menu Berdasarkan Role
    |--------------------------------------------------------------------------
    | Di sini Anda definisikan role mana yang dapat mengakses SLUG menu apa saja.
    | - Kunci Array: Nama Role di aplikasi Anda (contoh: 'Admin', 'Bendahara').
    | - Nilai Array: Daftar SLUG menu yang diizinkan. Gunakan ['*'] untuk akses penuh.
    */
    'role_map' => [
        // ROLE CONTOH
        'Admin' => [
            '*',
            '!profil-guru',
            '!profil-siswa',
            '!pelanggaran-guru',
            '!pelanggaran-siswa',
            '!input-data-alumni',
        ],
        'Operator Sekolah' => [
            '*',
            '!pelanggaran-siswa',
        ],
        // Akses ke semua menu
        'Kepala Sekolah' => [
            'dashboard',
            'profil-sekolah',
            'ppdb',
            'sarpras',
            'kepegawaian',
            'kesiswaan',
            'indisipliner',
        ],
        'Peserta Didik' => [
            'dashboard',
            'profil-sekolah',
            'profil-siswa',
            'pelanggaran-siswa',
            'input-data-alumni',
        ],
    ],


    'sub_role_map' => [
        'Guru' => [
            'dashboard',
            'profil-sekolah',
            'profil-guru',
            'pelanggaran-guru',
            'ppdb-formulir',
            'kesiswaan',
            'kurikulum',
            'humas',
            'sarpras',
            'indisipliner',
            'indisipliner-siswa'

        ],
        'Tenaga Kependidikan' => [
            'dashboard',
            'profil-sekolah',
            'profil-guru',
            'pelanggaran-guru',
            'ppdb',
            'ppdb-formulir',
            'sarpras',
            'kepegawaian',
            'kesiswaan',

        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | MENU STRUCTURE: Struktur Menu Sidebar
    |--------------------------------------------------------------------------
    | Setiap menu utama dan menu toggle tingkat 2 harus memiliki kunci 'slug'.
    | Link akhir/item tanpa sub-menu tidak memerlukan slug, karena akan diwarisi
    | aksesnya dari menu induk terdekat yang memiliki slug.
    */
    'sidebar_menu' => [
        // DASHBOARD
        [
            'title' => 'Dashboard',
            'slug' => 'dashboard',
            'icon' => 'bx bx-home-circle',
            'route' => 'admin.dashboard',
            'is_active' => 'request()->is("admin/dashboard")',
        ],
        // PROFIL SEKOLAH
        [
            'title' => 'Profil Sekolah',
            'slug' => 'profil-sekolah',
            'icon' => 'bx bxs-school',
            'route' => 'admin.pengaturan.sekolah.index',
            'is_active' => 'request()->is("admin/pengaturan/sekolah*")',
        ],
        // PROFIL GURU
        [
            'title' => 'Profil Guru',
            'slug' => 'profil-guru',
            'icon' => 'bx bxs-user',
            'route' => 'admin.personal.gtk.profil',
            'is_active' => 'request()->is("admin/personal/gtk/profil")',
        ],
        // PROFIL SISWA
        [
            'title' => 'Profil Siswa',
            'slug' => 'profil-siswa',
            'icon' => 'bx bxs-user',
            'route' => 'admin.personal.siswa.profil',
            'is_active' => 'request()->is("admin/personal/siswa/profil")',
        ],
        // PELANGGARAN GURU
        [
            'title' => 'Pelanggaran Guru',
            'slug' => 'pelanggaran-guru',
            'icon' => 'bx bxs-user-x',
            'route' => 'admin.personal.gtk.pelanggaran',
            'is_active' => 'request()->is("admin/personal/gtk/pelanggaran")',
        ],
        // PELANGGARAN SISWA
        [
            'title' => 'Pelanggaran Siswa',
            'slug' => 'pelanggaran-siswa',
            'icon' => 'bx bxs-user-x',
            'route' => 'admin.personal.siswa.pelanggaran',
            'is_active' => 'request()->is("admin/personal/siswa/pelanggaran")',
        ],
        [
            'title' => 'Input Data',
            'slug' => 'input-data-alumni',
            'icon' => 'bx bx-id-card',
            'route' => 'admin.alumni.create',
            'is_active' => 'request()->is("admin/alumni/create)',
        ],
        // --- AKADEMIK ---
        [
            'title' => 'Akademik',
            'slug' => 'akademik',
            'icon' => 'bx bxs-book-content',
            'is_toggle' => true,
            'is_open' => 'request()->is("admin/akademik*") && !request()->is("admin/akademik/jadwal-pelajaran*")',
            'submenu' => [
                ['title' => 'Tahun Pelajaran', 'route' => 'admin.akademik.tapel.index', 'is_active' => 'request()->is("admin/akademik/tapel*")'],
                ['title' => 'Konsentrasi Keahlian', 'route' => 'admin.akademik.jurusan.index', 'is_active' => 'request()->is("admin/akademik/jurusan*")'],
                ['title' => 'Mata Pelajaran', 'route' => 'admin.akademik.mapel.index', 'is_active' => 'request()->is("admin/akademik/mapel*")'],
                ['title' => 'Daftar Ekstrakurikuler', 'route' => 'admin.akademik.daftar-ekstrakurikuler.index', 'is_active' => 'request()->is("admin/akademik/daftar-ekstrakurikuler*")'],
            ]
        ],
        // --- KEPEGAWAIAN ---
        [
            'title' => 'Kepegawaian',
            'slug' => 'kepegawaian',
            'icon' => 'bx bxs-user-badge',
            'is_toggle' => true,
            'is_open' => 'request()->is("admin/kepegawaian*")',
         'submenu' => [
                ['title' => 'Data Guru', 'route' => 'admin.kepegawaian.guru.index', 'is_active' => 'request()->routeIs("admin.kepegawaian.guru.*")'],
                ['title' => 'Tenaga Kependidikan', 'route' => 'admin.kepegawaian.tendik.index', 'is_active' => 'request()->routeIs("admin.kepegawaian.tendik.*")'],
                ['title' => 'Template Surat SK', 'route' => 'admin.kepegawaian.TemplateSk.index', 'is_active' => 'request()->is("admin.kepegawaian.TemplateSk.*")'],
                ['title' => 'Tugas Pegawai', 'route' => 'admin.kepegawaian.tugas-pegawai.index', 'is_active' => 'request()->is("admin/kepegawaian/tugas-pegawai*")'],
                ['title' => 'Cetak Kartu ID', 'route' => 'admin.kepegawaian.gtk.index-cetak-kartu', 'is_active' => 'request()->routeIs("gtk.index-cetak-kartu")'],
                ['title' => 'GTK Nonaktif', 'route' => 'admin.kepegawaian.gtk.inactive', 'is_active' => 'request()->is("admin/kepegawaian/gtk/inactive*")'],
            ]
        ],
        // --- KURIKULUM ---
        [
            'title' => 'Kurikulum',
            'slug' => 'kurikulum',
            'icon' => 'bx bx-book-open',
            'is_toggle' => true,
            'is_open' => 'request()->is("admin/rombel*") || request()->is("admin/akademik/jadwal-pelajaran*")',
            'submenu' => [
                ['title' => 'Rombongan Belajar', 'is_toggle' => true, 'is_open' => 'request()->is("admin/rombel*")', 'submenu' => [
                    ['title' => 'Reguler', 'route' => 'admin.rombel.reguler.index', 'is_active' => 'request()->is("admin/rombel/reguler*")'],
                    ['title' => 'Praktik', 'route' => 'admin.rombel.praktik.index', 'is_active' => 'request()->is("admin/rombel/praktik*")'],
                    ['title' => 'Ekstrakurikuler', 'route' => 'admin.rombel.ekstrakurikuler.index', 'is_active' => 'request()->is("admin/rombel/ekstrakurikuler*")'],
                    ['title' => 'Mapel Pilihan', 'route' => 'admin.rombel.mapel-pilihan.index', 'is_active' => 'request()->is("admin/rombel/mapel-pilihan*")'],
                    ['title' => 'Wali', 'route' => 'admin.rombel.wali.index', 'is_active' => 'request()->is("admin/rombel/wali*")'],
                ]],
                ['title' => 'Jadwal Pelajaran', 'is_toggle' => true, 'is_open' => 'request()->is("admin/kurikulum*")', 'submenu' => [
                    ['title' => 'Pengaturan Jam', 'route' => 'admin.kurikulum.jam-pelajaran.index', 'is_active' => 'request()->is("admin/kurikulum/jam-pelajaran*")'],
                    ['title' => 'Penyusunan Jadwal', 'route' => 'admin.kurikulum.jadwal-pelajaran.index', 'is_active' => 'request()->is("admin/akurikulum/jadwal-pelajaran*")'],
                    ['title' => 'Rekap & Cetak', 'route' => 'admin.kurikulum.jadwal-pelajaran.rekap', 'is_active' => 'request()->is("admin/akurikulum/jadwal-pelajaran*")'],
                ]],
            ]
        ],
        // --- KESISWAAN ---
        [
            'title' => 'Kesiswaan',
            'slug' => 'kesiswaan',
            'icon' => 'bx bx-user-check',
            'is_toggle' => true,
            'is_open' => 'request()->is("admin/kesiswaan*")',
                'submenu' => [
                ['title' => 'Data Siswa', 'route' => 'admin.kesiswaan.siswa.index', 'is_active' => 'request()->is("admin/kesiswaan/siswa*")'],
                ['title' => 'Rekap Jumlah Siswa', 'route' => 'admin.laporan.rekap_siswa', 'is_active' => 'request()->is("admin/laporan/rekap-siswa*")'],
                ['title' => 'Cetak Kartu Massal', 'route' => 'admin.kesiswaan.siswa.cetak_massal_index', 'is_active' => 'request()->routeIs("admin.kesiswaan.siswa.cetak_massal_index")'],
                ['title' => 'Buku Induk', 'route' => 'admin.kesiswaan.buku_induk.index', 'is_active' => 'request()->is("admin/kesiswaan/buku-induk*")'],
                ['title' => 'PD Nonaktif', 'route' => 'admin.kesiswaan.siswa.inactive', 'is_active' => 'request()->is("admin/kesiswaan/siswa/inactive*")'],
            ]
        ],
        // --- PPDB ---
        [
            'title' => 'PPDB',
            'slug' => 'ppdb',
            'icon' => 'bx bxs-graduation',
            'is_toggle' => true,
            'is_open' => 'request()->is("admin/ppdb*")',
            'submenu' => [

                [
                    'title' => 'Tahun PPDB',
                    'slug'  => 'ppdb-tahun',
                    'route' => 'admin.ppdb.tahun-ppdb.index',
                    'is_active' => 'request()->routeIs("admin.ppdb.tahun-ppdb.index")'
                ],

                [
                    'title' => 'Pengaturan Tingkat',
                    'slug'  => 'ppdb-tingkat',
                    'route' => 'admin.ppdb.tingkat-ppdb.index',
                    'is_active' => 'request()->routeIs("admin.ppdb.tingkat-ppdb.index")'
                ],

                [
                    'title' => 'Kompetensi',
                    'slug'  => 'ppdb-kompetensi',
                    'route' => 'admin.ppdb.kompetensi-ppdb.index',
                    'is_active' => 'request()->routeIs("admin.ppdb.kompetensi-ppdb.index")'
                ],

                [
                    'title' => 'Kelas',
                    'slug'  => 'ppdb-kelas',
                    'route' => 'admin.ppdb.kelas-ppdb.index',
                    'is_active' => 'request()->routeIs("admin.ppdb.kelas-ppdb.index")'
                ],

                [
                    'title' => 'Quota Pendaftaran',
                    'slug'  => 'ppdb-quota',
                    'route' => 'admin.ppdb.quota-ppdb.index',
                    'is_active' => 'request()->routeIs("admin.ppdb.quota-ppdb.index")'
                ],

                [
                    'title' => 'Jalur Pendaftaran',
                    'slug'  => 'ppdb-jalur',
                    'route' => 'admin.ppdb.jalur-ppdb.index',
                    'is_active' => 'request()->routeIs("admin.ppdb.jalur-ppdb.index")'
                ],

                [
                    'title' => 'Syarat Pendaftaran',
                    'slug'  => 'ppdb-syarat',
                    'route' => 'admin.ppdb.syarat-ppdb.index',
                    'is_active' => 'request()->routeIs("admin.ppdb.syarat-ppdb.index")'
                ],

                [
                    'title' => 'Formulir Pendaftaran',
                    'slug'  => 'ppdb-formulir',
                    'route' => 'admin.ppdb.formulir-ppdb.index',
                    'is_active' => 'request()->routeIs("admin.ppdb.formulir-ppdb.index")'
                ],

                [
                    'title' => 'Calon Peserta Didik',
                    'slug'  => 'ppdb-calon',
                    'route' => 'admin.ppdb.daftar-calon-peserta-didik.index',
                    'is_active' => 'request()->routeIs("admin.ppdb.daftar-calon-peserta-didik.index")'
                ],

                [
                    'title' => 'Pemberian NIS',
                    'slug'  => 'ppdb-nis',
                    'route' => 'admin.ppdb.pemberian-nis.index',
                    'is_active' => 'request()->routeIs("admin.ppdb.pemberian-nis.index")'
                ],

                [
                    'title' => 'Peserta Didik Baru',
                    'slug'  => 'ppdb-peserta-baru',
                    'route' => 'admin.ppdb.daftar-peserta-didik-baru.index',
                    'is_active' => 'request()->routeIs("admin.ppdb.daftar-peserta-didik-baru.index")'
                ],

                [
                    'title' => 'Penempatan Kelas',
                    'slug'  => 'ppdb-penempatan',
                    'route' => 'admin.ppdb.penempatan-kelas.index',
                    'is_active' => 'request()->routeIs("admin.ppdb.penempatan-kelas.index")'
                ],

                [
                    'title' => 'Laporan Pendaftaran',
                    'slug'  => 'ppdb-laporan',
                    'route' => 'admin.ppdb.laporan-pendaftaran.index',
                    'is_active' => 'request()->routeIs("admin.ppdb.laporan-pendaftaran.index")'
                ],

                [
                    'title' => 'Laporan Quota',
                    'slug'  => 'ppdb-laporan-quota',
                    'route' => 'admin.ppdb.laporan-quota.index',
                    'is_active' => 'request()->routeIs("admin.ppdb.laporan-quota.index")'
                ],
            ]
        ],
        // --- ALUMNI ---
        [
            'title' => 'Alumni',
            'slug' => 'alumni',
            'icon' => 'bx bx-user-pin',
            'is_toggle' => true,
            'is_open' => 'request()->is("admin/alumni*")',
            'submenu' => [
                ['title' => 'Penetapan Kelulusan', 'route' => 'admin.alumni.pelulusan', 'is_active' => 'false'],
                ['title' => 'Data Alumni', 'route' => 'admin.alumni.dataAlumni.index', 'is_active' => 'false'],
                ['title' => 'Rekap Data Alumni', 'route' => 'admin.alumni.rekapDataAlumni', 'is_active' => 'false'],
                ['title' => 'Testimoni Alumni', 'route' => 'admin.alumni.testimoni.index', 'is_active' => 'false'],
                ['title' => 'Penelusuran Kerja', 'route' => 'admin.alumni.tracer.index', 'is_active' => 'false'],
            ]
        ],
        // --- KEUANGAN ---
        [
            'title' => 'Keuangan',
            'slug' => 'keuangan',
            'icon' => 'bx bx-money',
            'is_toggle' => true,
            'is_open' => 'request()->routeIs("bendahara.keuangan*")',
            'submenu' => [
                ['title' => 'Pengaturan Iuran', 'route' => 'bendahara.keuangan.iuran.index', 'is_active' => 'request()->routeIs("bendahara.keuangan.iuran.index")'],
                ['title' => 'Manajemen Beasiswa', 'route' => 'bendahara.keuangan.voucher.index', 'is_active' => 'request()->routeIs("bendahara.keuangan.voucher.index")'],
                ['title' => 'Penerimaan', 'route' => 'bendahara.keuangan.penerimaan.index', 'is_active' => 'request()->routeIs("bendahara.keuangan.penerimaan.index")'],
                ['title' => 'Pengeluaran', 'route' => 'bendahara.keuangan.pengeluaran.index', 'is_active' => 'request()->routeIs("bendahara.keuangan.pengeluaran.index")'],
                ['title' => 'Buku Kas', 'route' => 'bendahara.keuangan.kas.index', 'is_active' => 'request()->routeIs("bendahara.keuangan.kas.index")'],
                ['title' => 'Tagihan', 'route' => 'bendahara.keuangan.tagihan.create', 'is_active' => 'request()->routeIs("bendahara.keuangan.tagihan.create")'],
                ['title' => 'Master kas', 'route' => 'bendahara.keuangan.kas-master.index', 'is_active' => 'request()->routeIs("bendahara.keuangan.kas-master.index")'],
            ]
        ],
        // --- KEHADIRAN (ABSENSI) ---
        [
            'title' => 'Kehadiran',
            'slug'  => 'kehadiran',
            'icon' => 'bx bx-calendar-check',
            'is_toggle' => true,
            'is_open' => 'request()->is("admin/absensi*")',
            'submenu' => [
                // Submenu Siswa
                [
                    'title' => 'Siswa',
                    'slug' => 'absensi-siswa',
                    'is_toggle' => true,
                    'is_open' => 'request()->routeIs("admin.absensi.siswa.*") || request()->routeIs("admin.absensi.mapel.* || request()->is("admin/pengaturan/absensi*") || request()->is("admin/pengaturan/hari-libur*")")',
                    'submenu' => [
                        ['title' => 'Absensi Harian', 'route' => 'admin.absensi.siswa.index', 'is_active' => 'request()->routeIs("admin.absensi.siswa.index")'],
                        ['title' => 'Absensi Mapel', 'route' => 'admin.absensi.mapel.index', 'is_active' => 'request()->routeIs("admin.absensi.mapel.index")'],
                        ['title' => 'KiosK Scanner', 'route' => 'admin.absensi.siswa.show_scanner', 'target' => '_blank', 'is_active' => 'request()->routeIs("admin.absensi.siswa.show_scanner")'],
                        ['title' => 'Jam & Aturan', 'route' => 'admin.pengaturan.absensi.edit', 'is_active' => 'request()->is("admin/pengaturan/absensi*")'],
                        ['title' => 'Manajemen Hari Libur', 'route' => 'admin.pengaturan.hari-libur.index', 'is_active' => 'request()->is("admin/pengaturan/hari-libur*")'],
                        ['title' => 'Izin Siswa', 'route' => 'admin.absensi.izin-siswa.index', 'is_active' => 'request()->routeIs("admin.absensi.izin-siswa.index")'],
                    ]
                ],
                // Submenu Guru & GTK
                [
                    'title' => 'Guru & GTK',
                    'slug' => 'absensi-gtk',
                    'is_toggle' => true,
                    'is_open' => 'request()->routeIs("admin.absensi.gtk.*")',
                    'submenu' => [
                        ['title' => 'Absensi Manual', 'route' => 'admin.absensi.gtk.index', 'is_active' => 'request()->routeIs("admin.absensi.gtk.index")'],
                        ['title' => 'Laporan Absensi', 'route' => 'admin.absensi.gtk.laporan', 'is_active' => 'request()->routeIs("admin.absensi.gtk.laporan")'],
                        ['title' => 'KiosK Scanner', 'route' => 'admin.absensi.gtk.scanner', 'target' => '_blank', 'is_active' => 'request()->routeIs("admin.absensi.gtk.scanner")'],
                    ]
                ],
            ]
        ],
        // --- LAPORAN ---
        [
            'title' => 'Laporan',
            'slug' => 'laporan',
            'icon' => 'bx bxs-report',
            'is_toggle' => true,
            'is_open' => 'request()->is("admin/laporan*")',
            'submenu' => [
                ['title' => 'Dashboard Absensi', 'route' => 'admin.laporan.absensi.dashboard', 'is_active' => 'request()->is("admin/laporan/absensi/dashboard")'],
                ['title' => 'Laporan Absensi', 'route' => 'admin.laporan.absensi.index', 'is_active' => 'request()->is("admin/laporan/absensi")'],
                ['title' => 'Laporan Absensi Bulanan', 'route' => 'admin.laporan.absensi.bulanan', 'is_active' => 'request()->is("admin/laporan/absensi/bulanan")'],
                ['title' => 'Tanpa Absen Pulang', 'route' => 'admin.laporan.absensi.tanpa_pulang', 'is_active' => 'request()->routeIs("admin.laporan.absensi.tanpa_pulang")'],
            ]
        ],
        // --- INDISIPLINER ---
        [
            'title' => 'Indisipliner',
            'icon' => 'bx bx-shield-quarter',
            'slug' => 'indisipliner',
            'is_toggle' => true,
            'is_open' => 'request()->is("admin/indisipliner*")',
            'submenu' => [
                // Indisipliner Guru
                [
                    'title' => 'Guru',
                    'slug' => 'indisipliner-guru',
                    'is_toggle' => true,
                    'is_open' => 'request()->is("admin/indisipliner/guru*")',
                    'submenu' => [
                        ['title' => 'Pengaturan', 'route' => 'admin.indisipliner.guru.pengaturan.index', 'is_active' => 'request()->is("admin/indisipliner-guru/pengaturan*")'],
                        ['title' => 'Daftar Indisipliner', 'route' => 'admin.indisipliner.guru.daftar.index', 'is_active' => 'request()->is("admin/indisipliner-guru/daftar*")'],
                        ['title' => 'Rekapitulasi', 'route' => 'admin.indisipliner.guru.rekapitulasi.index', 'is_active' => 'request()->is("admin/indisipliner-guru/rekapitulasi*")'],
                    ]
                ],
                // Indisipliner Siswa
                [
                    'title' => 'Siswa',
                    'slug' => 'indisipliner-siswa',
                    'is_toggle' => true,
                    'is_open' => 'request()->is("admin/indisipliner-siswa*")',
                    'submenu' => [
                        ['title' => 'Pengaturan', 'route' => 'admin.indisipliner.siswa.pengaturan.index', 'is_active' => 'request()->routeIs("admin.indisipliner.siswa.pengaturan.index")'],
                        ['title' => 'Kios Pelanggaran', 'route' => 'admin.indisipliner.siswa.kiosk.index', 'is_active' => 'request()->routeIs("admin.indisipliner.siswa.kiosk.*")'],
                        ['title' => 'Daftar Indisipliner', 'route' => 'admin.indisipliner.siswa.daftar.index', 'is_active' => 'request()->routeIs("admin.indisipliner.siswa.daftar.*")'],
                        ['title' => 'Rekapitulasi', 'route' => 'admin.indisipliner.siswa.rekapitulasi.index', 'is_active' => 'request()->routeIs("admin.indisipliner.siswa.rekapitulasi.*")'],
                    ]
                ],
            ]
        ],
        // --- ADMINISTRASI SURAT ---
        [
            'title' => 'Administrasi Surat',
            'slug' => 'administrasi-surat',
            'icon' => 'bx bx-envelope',
            'is_toggle' => true,
            'is_open' => 'request()->is("admin/administrasi*")',
            'submenu' => [
                ['title' => 'Pengaturan Nomor', 'route' => 'admin.administrasi.pengaturan-nomor.index', 'is_active' => 'request()->routeIs("admin.administrasi.pengaturan-nomor.*")'],
                ['title' => 'Template Surat', 'route' => 'admin.administrasi.tipe-surat.index', 'is_active' => 'request()->routeIs("admin.administrasi.tipe-surat.*")'],
                ['title' => 'Surat Masuk', 'route' => 'admin.administrasi.surat-masuk.index', 'is_active' => 'request()->routeIs("admin.administrasi.surat-masuk.*")'],
                ['title' => 'Surat Keluar (Siswa)', 'route' => 'admin.administrasi.surat-keluar-siswa.index', 'is_active' => 'request()->routeIs("admin.administrasi.surat-keluar-siswa.*")'],
                ['title' => 'Surat Keluar (Guru)', 'route' => 'admin.administrasi.surat-keluar-guru.index', 'is_active' => 'request()->routeIs("admin.administrasi.surat-keluar-guru.*")'],
            ]
        ],
        // --- SARPRAS ---
        [
            'title' => 'Sarpras',
            'slug' => 'sarpras',
            'icon' => 'bx bx-buildings',
            'is_toggle' => true,
            'is_open' => 'request()->is("admin/sarpras*")',
            'submenu' => [
                ['title' => 'Tanah', 'route' => 'admin.underConstructions', 'is_active' => 'false'],
                ['title' => 'Ruang', 'route' => 'admin.underConstructions', 'is_active' => 'false'],
                ['title' => 'Alat', 'route' => 'admin.underConstructions', 'is_active' => 'false'],
            ]
        ],
        // --- HUMAS ---
        [
            'title' => 'Humas',
            'slug' => 'humas',
            'icon' => 'bx bx-message-dots',
            'is_toggle' => true,
            'is_open' => 'request()->is("admin/humas*")',
            'submenu' => [
                ['title' => 'PKL', 'route' => 'admin.underConstructions', 'is_active' => 'false'],
                ['title' => 'Daftar Tamu', 'route' => 'admin.underConstructions', 'is_active' => 'false'],
            ]
        ],

        // --- MANAJEMEN LANDING ---
        [
            'title' => 'Manajemen Web',
            'slug' => 'manajemen-landing-header',
            'is_header' => true,
        ],
        [
            'title' => 'Web SPMB',
            'slug' => 'landing-page',
            'icon' => 'bx bxs-book-content',
            'is_toggle' => true,
            'is_open' => 'request()->is("admin/landing-page*")',
            'submenu' => [
                ['title' => 'Pengaturan Web SPMB', 'route' => 'admin.ppdb.landing.index', 'is_active' => 'request()->routeIs("admin.ppdb.landing")'],
            ]
        ],

        // --- LANDING WEB ---
        [
            'title' => 'Web Profile',
            'slug' => 'landing',
            'icon' => 'bx bx-globe',
            'is_toggle' => true,
            'is_open' => 'request()->is("admin/landing*")',
            'submenu' => [
                [
                    'title' => 'Slider Utama',
                    'route' => 'admin.landing.slider.index',
                    'is_active' => 'request()->routeIs("admin.landing.slider.*")',
                ],
                [
                    'title' => 'Sambutan Kepsek',
                    'route' => 'admin.landing.sambutan.index',
                    'is_active' => 'request()->routeIs("admin.landing.sambutan.index")',
                ],
                [
                    'title' => 'Fasilitas Sekolah',
                    'route' => 'admin.landing.fasilitas.index',
                    'is_active' => 'request()->routeIs("admin.landing.fasilitas.index")',
                ],
                [
                    'title' => 'Kompetensi Keahlian',
                    'route' => 'admin.landing.jurusan.index',
                    'is_active' => 'request()->routeIs("admin.landing.jurusan.index")',
                ],
                [
                    'title' => 'Berita & Artikel',
                    'route' => 'admin.landing.berita.index',
                    'is_active' => 'request()->routeIs("admin.landing.berita.index")',
                ],
                [
                    'title' => 'Prestasi Sekolah',
                    'route' => 'admin.landing.prestasi.index',
                    'is_active' => 'request()->routeIs("admin.landing.prestasi.index")',
                ],
                [
                    'title' => 'Galeri Kegiatan',
                    'route' => 'admin.landing.galeri.index',
                    'is_active' => 'request()->routeIs("admin.landing.galeri.index")',
                ],
                [
                    'title' => 'Mitra Industri',
                    'route' => 'admin.landing.mitra.index',
                    'is_active' => 'request()->routeIs("admin.landing.mitra.index")',
                ],
                [
                    'title' => 'Testimoni',
                    'route' => 'admin.landing.testimoni.index',
                    'is_active' => 'request()->routeIs("admin.landing.testimoni.index")',
                ],
                [
                    'title' => 'Ekstrakurikuler',
                    'route' => 'admin.landing.ekstrakurikuler.index',
                    'is_active' => 'request()->routeIs("admin.landing.ekstrakurikuler.index")',
                ],
                [
                    'title' => 'Agenda Sekolah',
                    'route' => 'admin.landing.agenda.index',
                    'is_active' => 'request()->routeIs("admin.landing.agenda.index")',
                ],
            ]
        ],


        // --- PENGATURAN SISTEM ---
        [
            'title' => 'Pengaturan Sistem',
            'slug' => 'pengaturan-sistem-header',
            'is_header' => true,
        ],
        [
            'title' => 'Pengaturan Sistem',
            'slug' => 'pengaturan-sistem',
            'icon' => 'bx bx-cog',
            'is_toggle' => true,
            'is_open' => 'request()->is("admin/pengaturan*")',
            'submenu' => [
                ['title' => 'Pengguna', 'route' => 'admin.underConstructions', 'is_active' => 'false'],
                ['title' => 'Backup Data', 'route' => 'admin.underConstructions', 'is_active' => 'false'],
                ['title' => 'Umum', 'route' => 'admin.underConstructions', 'is_active' => 'false'],
            ]
        ],

        // WEB SERVICE
        [
            'title' => 'Pengaturan Web Service',
            'slug' => 'pengaturan-web-service',
            'icon' => 'bx bx-cog',
            'route' => 'admin.pengaturan.webservice.index',
            'is_active' => 'request()->is("admin/pengaturan/webservice")',
        ],


        // --- KELUAR ---
        [
            'title' => 'Keluar',
            'slug' => 'logout',
            'icon' => 'bx bx-log-out',
            'route' => 'logout',
            'is_active' => 'false',
            'is_danger' => true,
        ],
    ],
];
