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
        'Operator KCD' => [ // Ganti nama role jika perlu
            'dashboard',
            'satuan-pendidikan',
            'kepegawaian',
            'kesiswaan',
            'administrasi-surat',
            'pengaturan-sistem',
        ],
    ],

    'sub_role_map' => [],

    /*
    |--------------------------------------------------------------------------
    | MENU STRUCTURE (KHUSUS MONITORING KCD)
    |--------------------------------------------------------------------------
    */
    'sidebar_menu' => [
        // 1. DASHBOARD
        [
            'title' => 'Dashboard',
            'slug' => 'dashboard',
            'icon' => 'bx bx-home-circle',
            'route' => 'admin.dashboard',
            'is_active' => 'request()->is("admin/dashboard")',
        ],

        // 2. SATUAN PENDIDIKAN (BARU)
        [
            'title' => 'Satuan Pendidikan',
            'slug' => 'satuan-pendidikan',
            'icon' => 'bx bx-buildings',
            'route' => 'admin.sekolah.index',
            'is_active' => 'request()->is("admin/sekolah*")',
        ],

        // 3. KEPEGAWAIAN (GTK)
        [
            'title' => 'Kepegawaian',
            'slug' => 'kepegawaian',
            'icon' => 'bx bxs-user-badge',
            'is_toggle' => true,
            'is_open' => 'request()->is("admin/kepegawaian*")',
            'submenu' => [
                ['title' => 'Data Guru', 'route' => 'admin.kepegawaian.guru.index', 'is_active' => 'request()->routeIs("admin.kepegawaian.guru.*")'],
            ]
        ],

        // 4. KESISWAAN
        [
            'title' => 'Kesiswaan',
            'slug' => 'kesiswaan',
            'icon' => 'bx bx-user-check',
            'is_toggle' => true,
            'is_open' => 'request()->is("admin/kesiswaan*")',
            'submenu' => [
                ['title' => 'Data Siswa', 'route' => 'admin.kesiswaan.siswa.index', 'is_active' => 'request()->is("admin/kesiswaan/siswa*")'],
            ]
        ],

        // 5. ADMINISTRASI SURAT (Jika KCD perlu memantau/mengelola surat)
        [
            'title' => 'Administrasi Surat',
            'slug' => 'administrasi-surat',
            'icon' => 'bx bx-envelope',
            'is_toggle' => true,
            'is_open' => 'request()->is("admin/administrasi*")',
            'submenu' => [
                ['title' => 'Surat Masuk', 'route' => 'admin.administrasi.surat-masuk.index', 'is_active' => 'request()->routeIs("admin.administrasi.surat-masuk.*")'],
                ['title' => 'Surat Keluar', 'route' => 'admin.administrasi.surat-keluar-guru.index', 'is_active' => 'request()->routeIs("admin.administrasi.surat-keluar-guru.*")'],
                ['title' => 'Arsip Surat', 'route' => 'admin.administrasi.arsip-surat.index', 'is_active' => 'request()->routeIs("admin.administrasi.arsip-surat.*")'],
            ]
        ],

        // 6. PENGATURAN
        [
            'title' => 'Pengaturan Sistem',
            'slug' => 'pengaturan-sistem-header',
            'is_header' => true,
        ],
        [
            'title' => 'Pengaturan',
            'slug' => 'pengaturan-sistem',
            'icon' => 'bx bx-cog',
            'is_toggle' => true,
            'is_open' => 'request()->is("admin/pengaturan*")',
            'submenu' => [
                ['title' => 'Profil Instansi', 'route' => 'admin.pengaturan.sekolah.index', 'is_active' => 'request()->routeIs("admin.pengaturan.sekolah.*")'],
            ]
        ],

        // 7. KELUAR
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