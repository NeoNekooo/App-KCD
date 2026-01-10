<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ROLE MAPPING (PEMETAAN PERAN)
    |--------------------------------------------------------------------------
    */
    'role_map' => [
        'Admin' => ['*'],
        'Operator KCD' => [
            'dashboard',
            'profil-instansi',
            'kepegawaian-kcd',
            'satuan-pendidikan',
            'gtk',
            'peserta-didik',
            'layanan-gtk',
            'administrasi-surat',
            'web-profile',
            'pengaturan-sistem-header',
            'pengaturan-sistem',
        ],
        'Sekolah' => [
            'dashboard',
            'gtk',
            'peserta-didik',
            'administrasi-surat',
            'web-profile',
            'pengaturan-sistem-header',
            'pengaturan-sistem',
        ],
    ],

    'sub_role_map' => [],

    /*
    |--------------------------------------------------------------------------
    | MENU STRUCTURE (STRUKTUR MENU)
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

        // 3. KEPEGAWAIAN (KCD)
        [
            'title' => 'Kepegawaian (KCD)',
            'slug' => 'kepegawaian-kcd',
            'icon' => 'bx bxs-id-card',
            'route' => 'admin.kcd.pegawai.index',
            'is_active' => 'request()->routeIs("admin.kcd.pegawai.*")',
        ],

        // 4. SATUAN PENDIDIKAN
        [
            'title' => 'Satuan Pendidikan',
            'slug' => 'satuan-pendidikan',
            'icon' => 'bx bxs-school',
            'route' => 'admin.sekolah.index',
            'is_active' => 'request()->routeIs("admin.sekolah.*")',
        ],

        // 5. GTK
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
                    'is_active' => 'request()->routeIs("admin.gtk.guru.*")',
                ],
                [
                    'title' => 'Tendik',
                    'route' => 'admin.gtk.tendik.index',
                    'is_active' => 'request()->routeIs("admin.gtk.tendik.*")',
                ],
            ],
        ],

        // 6. PESERTA DIDIK
        [
            'title' => 'Peserta Didik',
            'slug' => 'peserta-didik',
            'icon' => 'bx bx-user',
            'is_toggle' => true,
            'is_open' => 'request()->is("admin/kesiswaan*")',
            'submenu' => [
                [
                    'title' => 'Peserta Didik',
                    'route' => 'admin.kesiswaan.siswa.index',
                    'is_active' => 'request()->routeIs("admin.kesiswaan.siswa.*")',
                ],
            ],
        ],

        // 7. LAYANAN GTK
        [
            'title' => 'Layanan GTK',
            'slug' => 'layanan-gtk',
            'icon' => 'bx bx-briefcase-alt-2',
            'is_toggle' => true,
            'is_open' => 'request()->is("admin/layanan*")', 
            
            // ✅ PARENT: MENAMPILKAN TOTAL (11)
            'badge_key' => 'total_layanan_gtk', 
            
            'submenu' => [
                [
                    'title' => 'Kenaikan Pangkat',
                    'slug'  => 'layanan-kp',
                    'route' => 'admin.layanan.kategori', 
                    'params' => ['kategori' => 'kenaikan-pangkat'], 
                    'is_active' => 'request()->is("admin/layanan/kenaikan-pangkat")',
                    // ✅ ANAK: MENAMPILKAN SPESIFIK (5)
                    'badge_key' => 'notif_kp', 
                ],
                [
                    'title' => 'KGB (Gaji Berkala)',
                    'slug'  => 'layanan-kgb',
                    'route' => 'admin.layanan.kategori',
                    'params' => ['kategori' => 'kgb'],
                    'is_active' => 'request()->is("admin/layanan/kgb")',
                    // ✅ ANAK: MENAMPILKAN SPESIFIK (0)
                    'badge_key' => 'notif_kgb',
                ],
                [
                    'title' => 'Mutasi',
                    'slug'  => 'layanan-mutasi',
                    'route' => 'admin.layanan.kategori',
                    'params' => ['kategori' => 'mutasi'],
                    'is_active' => 'request()->is("admin/layanan/mutasi")',
                    // ✅ ANAK: MENAMPILKAN SPESIFIK (2)
                    'badge_key' => 'notif_mutasi',
                ],
                [
                    'title' => 'Relokasi / Penempatan',
                    'slug'  => 'layanan-relokasi',
                    'route' => 'admin.layanan.kategori',
                    'params' => ['kategori' => 'relokasi'],
                    'is_active' => 'request()->is("admin/layanan/relokasi")',
                    // ✅ ANAK: MENAMPILKAN SPESIFIK (1)
                    'badge_key' => 'notif_relokasi',
                ],
                [
                    'title' => 'Satya Lencana',
                    'slug'  => 'layanan-satya',
                    'route' => 'admin.layanan.kategori',
                    'params' => ['kategori' => 'satya-lencana'],
                    'is_active' => 'request()->is("admin/layanan/satya-lencana")',
                    // ✅ ANAK: MENAMPILKAN SPESIFIK (3)
                    'badge_key' => 'notif_satya',
                ],
                [
                    'title' => 'Hukuman Disiplin',
                    'slug'  => 'layanan-hukdis',
                    'route' => 'admin.layanan.kategori',
                    'params' => ['kategori' => 'hukuman-disiplin'],
                    'is_active' => 'request()->is("admin/layanan/hukuman-disiplin")',
                    // ✅ ANAK: MENAMPILKAN SPESIFIK (0)
                    'badge_key' => 'notif_hukdis',
                ],
                [
                    'title' => 'Verifikasi Surat Lainnya',
                    'slug'  => 'verifikasi-surat',
                    'route' => 'admin.verifikasi.index',
                    'is_active' => 'request()->routeIs("admin.verifikasi.*")',
                ],
            ],
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
                    'is_active' => 'request()->routeIs("admin.administrasi.surat-masuk.*")',
                ],
                [
                    'title' => 'Surat Keluar (Siswa)',
                    'route' => 'admin.administrasi.surat-keluar-siswa.index',
                    'is_active' => 'request()->routeIs("admin.administrasi.surat-keluar-siswa.*")',
                ],
                [
                    'title' => 'Surat Keluar (Guru)',
                    'route' => 'admin.administrasi.surat-keluar-guru.index',
                    'is_active' => 'request()->routeIs("admin.administrasi.surat-keluar-guru.*")',
                ],
                [
                    'title' => 'Arsip Surat',
                    'route' => 'admin.administrasi.arsip-surat.index',
                    'is_active' => 'request()->routeIs("admin.administrasi.arsip-surat.*")',
                ],
                [
                    'title' => 'Template Surat',
                    'route' => 'admin.administrasi.tipe-surat.index',
                    'is_active' => 'request()->routeIs("admin.administrasi.tipe-surat.*")',
                ],
            ],
        ],

        // 9. WEB PROFILE
        [
            'title' => 'Web Profile',
            'slug' => 'web-profile',
            'icon' => 'bx bx-globe',
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
                    'is_active' => 'request()->routeIs("admin.administrasi.pengaturan-nomor.*")',
                ],
            ],
        ],
    ],
];