<?php
// File: config/menu_access.php

return [
    /*
    |--------------------------------------------------------------------------
    | ROLE MAPPING
    |--------------------------------------------------------------------------
    */
    'role_map' => [
        'Admin' => [
            '*', 
        ],
        'Operator KCD' => [
            'dashboard',
            'profil-instansi', // <--- AKSES BARU
            'satuan-pendidikan',
            'kepegawaian',
            'kesiswaan',
            'administrasi-surat',
            'pengaturan-sistem',
        ],
        'Sekolah' => [
            'dashboard',
            'kepegawaian',
            'kesiswaan',
            'administrasi-surat',
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

        // 2. PROFIL INSTANSI (MENU BARU)
        [
            'title' => 'Profil Instansi',
            'slug' => 'profil-instansi', 
            'icon' => 'bx bxs-landmark', // <--- GANTI ICON (Gedung Pemerintahan/KCD)
            'route' => 'admin.instansi.index',
            'is_active' => 'request()->routeIs("admin.instansi.*")',
        ],

        // 3. SATUAN PENDIDIKAN
        [
            'title' => 'Satuan Pendidikan',
            'slug' => 'satuan-pendidikan',
            'icon' => 'bx bxs-school', // <--- GANTI ICON (Ikon Sekolah Spesifik)
            'route' => 'admin.sekolah.index',
            'is_active' => 'request()->routeIs("admin.sekolah.*")',
        ],

        // 4. KEPEGAWAIAN (GTK)
        [
            'title' => 'Kepegawaian',
            'slug' => 'kepegawaian',
            'icon' => 'bx bxs-user-badge',
            'is_toggle' => true,
            'is_open' => 'request()->is("admin/kepegawaian*")',
            'submenu' => [
                [
                    'title' => 'Data Guru', 
                    'route' => 'admin.kepegawaian.guru.index', 
                    'is_active' => 'request()->routeIs("admin.kepegawaian.guru.index")'
                ]
            ]
        ],

        // 5. KESISWAAN
        [
            'title' => 'Kesiswaan',
            'slug' => 'kesiswaan',
            'icon' => 'bx bx-user',
            'is_toggle' => true,
            'is_open' => 'request()->is("admin/kesiswaan*")',
            'submenu' => [
                [
                    'title' => 'Data Siswa', 
                    'route' => 'admin.kesiswaan.siswa.index', 
                    'is_active' => 'request()->routeIs("admin.kesiswaan.siswa.*")'
                ],
            ]
        ],

        // 6. ADMINISTRASI SURAT
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

        // 7. PENGATURAN LAINNYA
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