<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ROLE MAPPING
    |--------------------------------------------------------------------------
    | Pastikan slug di sini cocok dengan slug di sidebar_menu
    */
    'role_map' => [
        'Admin' => [
            '*', 
        ],
        'Operator KCD' => [
            'dashboard',
            'profil-instansi',
            'kepegawaian',       // Baru
            'satuan-pendidikan',
            'gtk',
            'peserta-didik',     // Dulu kesiswaan
            'data-pensiun',      // Baru
            'administrasi-surat',
            'web-profile',       // Baru
            'pengaturan-sistem',
        ],
        'Sekolah' => [
            'dashboard',
            'gtk',
            'peserta-didik',
            'administrasi-surat',
            'web-profile',
            'pengaturan-sistem',
        ],
    ],

    'sub_role_map' => [],

    /*
    |--------------------------------------------------------------------------
    | MENU STRUCTURE
    |--------------------------------------------------------------------------
    */
    'sidebar_menu' => [
        // 1. DASHBOARD
        [
            'title' => 'Dashboard',
            'slug' => 'dashboard',
            'icon' => 'bx bx-home-circle',
            'route' => 'admin.dashboard',
            'is_active' => 'request()->routeIs("admin.dashboard")',
        ],

        // 2. PROFIL INSTANSI
        [
            'title' => 'Profil Instansi',
            'slug' => 'profil-instansi', 
            'icon' => 'bx bxs-landmark',
            'route' => 'admin.instansi.index',
            'is_active' => 'request()->routeIs("admin.instansi.*")',
        ],

        // 3. KEPEGAWAIAN (Placeholder / Menu Baru)
        [
            'title' => 'Kepegawaian',
            'slug' => 'kepegawaian',
            'icon' => 'bx bxs-id-card', // Icon Kartu Pegawai
            'route' => '#', 
            'is_active' => 'false', 
        ],

        // 4. SATUAN PENDIDIKAN
        [
            'title' => 'Satuan Pendidikan',
            'slug' => 'satuan-pendidikan',
            'icon' => 'bx bxs-school',
            'route' => 'admin.sekolah.index',
            'is_active' => 'request()->routeIs("admin.sekolah.*")',
        ],

        // 5. GTK (Guru & Tendik)
        [
            'title' => 'GTK',
            'slug' => 'gtk',
            'icon' => 'bx bxs-user-badge',
            'is_toggle' => true,
            'is_open' => 'request()->is("admin/gtk*")', 
            'submenu' => [
                [
                    'title' => 'Guru', 
                    'route' => 'admin.gtk.guru.index', 
                    'is_active' => 'request()->routeIs("admin.gtk.guru.*")'
                ],
                [
                    'title' => 'Tendik', 
                    'route' => 'admin.gtk.tendik.index', 
                    'is_active' => 'request()->routeIs("admin.gtk.tendik.*")'
                ]
            ]
        ],

        // 6. PESERTA DIDIK (Label diganti dari Kesiswaan)
        [
            'title' => 'Peserta Didik',
            'slug' => 'peserta-didik',
            'icon' => 'bx bx-user', // Icon User
            'is_toggle' => true,
            'is_open' => 'request()->is("admin/kesiswaan*")', // Tetap detect URL kesiswaan
            'submenu' => [
                [
                    'title' => 'Peserta Didik', 
                    'route' => 'admin.kesiswaan.siswa.index', 
                    'is_active' => 'request()->routeIs("admin.kesiswaan.siswa.*")'
                ],
            ]
        ],

        // 7. KELOLA DATA PENSIUN (Placeholder / Menu Baru)
        [
            'title' => 'Kelola Data Pensiun',
            'slug' => 'data-pensiun',
            'icon' => 'bx bx-archive-out', // Icon Archive/Pensiun
            'route' => '#',
            'is_active' => 'false',
        ],

        // 8. ADMINISTRASI SURAT
        [
            'title' => 'Administrasi Surat',
            'slug' => 'administrasi-surat',
            'icon' => 'bx bx-envelope',
            'is_toggle' => true,
            'is_open' => 'request()->is("admin/administrasi*")',
            'submenu' => [
                [
                    'title' => 'Surat Masuk', 
                    'route' => 'admin.administrasi.surat-masuk.index', 
                    'is_active' => 'request()->routeIs("admin.administrasi.surat-masuk.*")'
                ],
                [
                    'title' => 'Surat Keluar (Siswa)', 
                    'route' => 'admin.administrasi.surat-keluar-siswa.index', 
                    'is_active' => 'request()->routeIs("admin.administrasi.surat-keluar-siswa.*")'
                ],
                [
                    'title' => 'Surat Keluar (Guru)', 
                    'route' => 'admin.administrasi.surat-keluar-guru.index', 
                    'is_active' => 'request()->routeIs("admin.administrasi.surat-keluar-guru.*")'
                ],
                [
                    'title' => 'Arsip Surat', 
                    'route' => 'admin.administrasi.arsip-surat.index', 
                    'is_active' => 'request()->routeIs("admin.administrasi.arsip-surat.*")'
                ],
                [
                    'title' => 'Template Surat', 
                    'route' => 'admin.administrasi.tipe-surat.index', 
                    'is_active' => 'request()->routeIs("admin.administrasi.tipe-surat.*")'
                ],
            ]
        ],

        // 9. WEB PROFILE (Placeholder / Menu Baru)
        [
            'title' => 'Web Profile',
            'slug' => 'web-profile',
            'icon' => 'bx bx-globe', // Icon Globe/Web
            'route' => '#',
            'is_active' => 'false',
        ],

        // 10. PENGATURAN
        [
            'title' => 'Lainnya',
            'slug' => 'pengaturan-sistem-header',
            'is_header' => true,
        ],
        [
            'title' => 'Pengaturan',
            'slug' => 'pengaturan-sistem',
            'icon' => 'bx bx-cog',
            'is_toggle' => true,
            'is_open' => 'request()->is("admin/pengaturan-nomor*")',
            'submenu' => [
                [
                    'title' => 'Nomor Surat', 
                    'route' => 'admin.administrasi.pengaturan-nomor.index', 
                    'is_active' => 'request()->routeIs("admin.administrasi.pengaturan-nomor.*")'
                ],
            ]
        ],
    ],
];