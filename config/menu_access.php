<?php

return [
    // 1. MAPPING AKSES MENU BERDASARKAN ROLE
    'role_map' => [
        'Admin' => ['*'], 
        
        'Operator KCD' => [
            'dashboard', 
            'profil-instansi', 
            'kepegawaian',           // Induk Menu
            'kepegawaian-data',      // Submenu Data Pegawai
            'kepegawaian-tugas',     // Submenu Penugasan
            'satuan-pendidikan', 
            'gtk', 
            'peserta-didik', 
            'layanan-gtk', 
            'administrasi-surat', 
            'web-profile', 
            'pengaturan-sistem-header', 
            'pengaturan-sistem',
        ],

        'Kepala' => [
            'dashboard', 
            'profil-saya',
            'layanan-gtk', 
            'layanan-kp',
            'layanan-kgb',
            'layanan-mutasi',
            'layanan-relokasi',
            'layanan-satya',
            'layanan-hukdis',
            'verifikasi-surat',
            'pengaturan-sistem-header',
        ],

        'Kasubag' => [
            'dashboard', 
            'profil-saya',
            'kepegawaian',           // Agar bisa melihat data pegawai yang divalidasi
            'kepegawaian-data',
            'layanan-gtk', 
            'layanan-kp',
            'layanan-kgb',
            'layanan-mutasi',
            'layanan-relokasi',
            'layanan-satya',
            'layanan-hukdis',
            'verifikasi-surat',
            'pengaturan-sistem-header',
        ],
        
        'Sekolah' => [
            'dashboard', 'gtk', 'peserta-didik', 'administrasi-surat', 
            'web-profile', 'pengaturan-sistem-header', 'pengaturan-sistem',
        ],

        'Pegawai' => [
            'dashboard', 
            'profil-saya',
            'pengaturan-sistem-header',

            // Menu-menu ini harus ada di sini agar sistem canAccessMenu bernilai true
            // Middleware di atas yang akan membatasi siapa yang boleh klik (Kasubag vs Pegawai Biasa)
            // 'layanan-gtk', 
            // 'layanan-kp',
            // 'layanan-kgb',
            // 'layanan-mutasi',
            // 'layanan-relokasi',
            // 'layanan-satya',
            // 'layanan-hukdis',
            // 'verifikasi-surat',
            
            // Catatan: Menu 'layanan-gtk' dan sub-layanan lainnya 
            // akan otomatis disuntikkan oleh Middleware untuk Pegawai biasa.
        ],
    ],

    'sub_role_map' => [],

    // 2. STRUKTUR SIDEBAR
    'sidebar_menu' => [
        
        // 1. DASHBOARD
        [
            'title' => 'Dashboard',
            'slug' => 'dashboard',
            'icon' => 'bx bx-home-circle',
            'route' => 'admin.dashboard',
        ],

        // 2. PROFIL SAYA (KHUSUS PEGAWAI)
        [
            'title' => 'Profil Saya',
            'slug'  => 'profil-saya',
            'icon'  => 'bx bxs-user-detail', 
            'route' => 'admin.profil-saya.show', 
        ],

        // 3. PROFIL INSTANSI
        [
            'title' => 'Profil Instansi',
            'slug' => 'profil-instansi',
            'icon' => 'bx bxs-landmark',
            'route' => 'admin.instansi.index',
        ],

        // 4. KEPEGAWAIAN (INDUK MENU)
        [
            'title' => 'Kepegawaian',
            'slug'  => 'kepegawaian',
            'icon'  => 'bx bxs-id-card',
            'submenu' => [
                [
                    'title' => 'Data Pegawai',
                    'slug'  => 'kepegawaian-data',
                    'route' => 'admin.kepegawaian.index', 
                ],
                [
                    'title' => 'Tugas Pegawai',
                    'slug'  => 'kepegawaian-tugas',
                    'route' => 'admin.kepegawaian.tugas-kcd.index',
                ],
            ],
        ],

        // 5. SATUAN PENDIDIKAN
        [
            'title' => 'Satuan Pendidikan',
            'slug' => 'satuan-pendidikan',
            'icon' => 'bx bxs-school',
            'route' => 'admin.sekolah.index',
        ],

        // 6. GTK
        [
            'title' => 'GTK',
            'slug' => 'gtk',
            'icon' => 'bx bxs-user-badge',
            'submenu' => [
                ['title' => 'Guru', 'route' => 'admin.gtk.guru.index'],
                ['title' => 'Tendik', 'route' => 'admin.gtk.tendik.index'],
            ],
        ],

        // 7. PESERTA DIDIK
        [
            'title' => 'Peserta Didik',
            'slug' => 'peserta-didik',
            'icon' => 'bx bx-user',
            'submenu' => [
                ['title' => 'Peserta Didik', 'route' => 'admin.kesiswaan.siswa.index'],
            ],
        ],

        // 8. LAYANAN GTK
        [
            'title' => 'Layanan GTK',
            'slug' => 'layanan-gtk',
            'icon' => 'bx bx-briefcase-alt-2',
            'badge_key' => 'total_layanan_gtk',
            'submenu' => [
                [
                    'title'  => 'Kenaikan Pangkat',
                    'slug'   => 'layanan-kp',
                    'route'  => 'admin.verifikasi.index',
                    'params' => ['kategori' => 'kenaikan-pangkat'],
                    'badge_key' => 'notif_kp',
                ],
                [
                    'title'  => 'KGB (Gaji Berkala)',
                    'slug'   => 'layanan-kgb',
                    'route'  => 'admin.verifikasi.index',
                    'params' => ['kategori' => 'kgb'],
                    'badge_key' => 'notif_kgb',
                ],
                [
                    'title'  => 'Mutasi',
                    'slug'   => 'layanan-mutasi',
                    'route'  => 'admin.verifikasi.index',
                    'params' => ['kategori' => 'mutasi'],
                    'badge_key' => 'notif_mutasi',
                ],
                [
                    'title'  => 'Relokasi / Penempatan',
                    'slug'   => 'layanan-relokasi',
                    'route'  => 'admin.verifikasi.index',
                    'params' => ['kategori' => 'relokasi'],
                    'badge_key' => 'notif_relokasi',
                ],
                [
                    'title'  => 'Satya Lencana',
                    'slug'   => 'layanan-satya',
                    'route'  => 'admin.verifikasi.index',
                    'params' => ['kategori' => 'satya-lencana'],
                    'badge_key' => 'notif_satya',
                ],
                [
                    'title'  => 'Hukuman Disiplin',
                    'slug'   => 'layanan-hukdis',
                    'route'  => 'admin.verifikasi.index',
                    'params' => ['kategori' => 'hukuman-disiplin'],
                    'badge_key' => 'notif_hukdis',
                ],
                [
                    'title'  => 'Verifikasi Surat Lainnya',
                    'slug'   => 'verifikasi-surat',
                    'route'  => 'admin.verifikasi.index',
                    'params' => [],
                ],
            ],
        ],

        // 9. ADMINISTRASI SURAT
        [
            'title' => 'Administrasi Surat',
            'slug' => 'administrasi-surat',
            'icon' => 'bx bx-envelope',
            'submenu' => [
                ['title' => 'Surat Masuk', 'route' => 'admin.administrasi.surat-masuk.index'],
                ['title' => 'Surat Keluar (Siswa)', 'route' => 'admin.administrasi.surat-keluar-siswa.index'],
                ['title' => 'Surat Keluar (Guru)', 'route' => 'admin.administrasi.surat-keluar-guru.index'],
                ['title' => 'Arsip Surat', 'route' => 'admin.administrasi.arsip-surat.index'],
                ['title' => 'Template Surat', 'route' => 'admin.administrasi.tipe-surat.index'],
            ],
        ],

        // 10. WEB PROFILE
        [
            'title' => 'Web Profile',
            'slug' => 'web-profile',
            'icon' => 'bx bx-globe',
            'route' => '#',
        ],

        // 11. PENGATURAN
        [
            'title' => 'Lainnya',
            'slug' => 'pengaturan-sistem-header',
            'is_header' => true,
        ],
        [
            'title' => 'Pengaturan',
            'slug' => 'pengaturan-sistem',
            'icon' => 'bx bx-cog',
            'submenu' => [
                ['title' => 'Nomor Surat', 'route' => 'admin.administrasi.pengaturan-nomor.index'],
            ],
        ],
    ],
];