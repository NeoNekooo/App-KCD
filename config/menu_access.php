<?php

return [
    // 1. MAPPING AKSES MENU BERDASARKAN ROLE
    'role_map' => [
        'Admin' => ['*'], // Admin akses semua
        
        'Operator KCD' => [
            'dashboard', 'profil-instansi', 'kepegawaian-kcd', 'satuan-pendidikan', 
            'gtk', 'peserta-didik', 'layanan-gtk', 'administrasi-surat', 
            'web-profile', 'pengaturan-sistem-header', 'pengaturan-sistem',
        ],
        
        'Sekolah' => [
            'dashboard', 'gtk', 'peserta-didik', 'administrasi-surat', 
            'web-profile', 'pengaturan-sistem-header', 'pengaturan-sistem',
        ],

        // [BARU] Role Pegawai: Akses Dashboard & Profil Saya
        'Pegawai' => [
            'dashboard', 
            'profil-saya', // <-- Slug baru agar bisa buka menu Profil Saya
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

        // [BARU] MENU PROFIL SAYA (KHUSUS PEGAWAI)
        // Route ini mengarah ke method showMe() di controller
        [
            'title' => 'Profil Saya',
            'slug'  => 'profil-saya',
            'icon'  => 'bx bxs-user-detail', 
            'route' => 'admin.kepegawaian.me', 
        ],

        // 2. PROFIL INSTANSI
        [
            'title' => 'Profil Instansi',
            'slug' => 'profil-instansi',
            'icon' => 'bx bxs-landmark',
            'route' => 'admin.instansi.index',
        ],

        // 3. KEPEGAWAIAN (KCD) - Ini tetap ada buat Admin
        [
            'title' => 'Kepegawaian (KCD)',
            'slug' => 'kepegawaian-kcd',
            'icon' => 'bx bxs-id-card',
            'route' => 'admin.kepegawaian.index', 
        ],

        // 4. SATUAN PENDIDIKAN
        [
            'title' => 'Satuan Pendidikan',
            'slug' => 'satuan-pendidikan',
            'icon' => 'bx bxs-school',
            'route' => 'admin.sekolah.index',
        ],

        // 5. GTK
        [
            'title' => 'GTK',
            'slug' => 'gtk',
            'icon' => 'bx bxs-user-badge',
            'submenu' => [
                ['title' => 'Guru', 'route' => 'admin.gtk.guru.index'],
                ['title' => 'Tendik', 'route' => 'admin.gtk.tendik.index'],
            ],
        ],

        // 6. PESERTA DIDIK
        [
            'title' => 'Peserta Didik',
            'slug' => 'peserta-didik',
            'icon' => 'bx bx-user',
            'submenu' => [
                ['title' => 'Peserta Didik', 'route' => 'admin.kesiswaan.siswa.index'],
            ],
        ],

        // 7. LAYANAN GTK
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

        // 8. ADMINISTRASI SURAT
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

        // 9. WEB PROFILE
        [
            'title' => 'Web Profile',
            'slug' => 'web-profile',
            'icon' => 'bx bx-globe',
            'route' => '#',
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
            'submenu' => [
                ['title' => 'Nomor Surat', 'route' => 'admin.administrasi.pengaturan-nomor.index'],
            ],
        ],
    ],
];