<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Reset Database (Biar bersih total)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('menu_accesses')->truncate();
        DB::table('menus')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // =================================================================
        // A. DATA KONFIGURASI MENU (BERDASARKAN DATA TERBARU)
        // =================================================================
        $sidebarMenu = [
            ['title' => 'Dashboard', 'slug' => 'dashboard', 'icon' => 'bx bx-home-circle', 'route' => 'admin.dashboard'],
            ['title' => 'Profil Saya', 'slug' => 'profil-saya', 'icon' => 'bx bx-user', 'route' => 'admin.profil-saya.show'],
            ['title' => 'Profil Instansi', 'slug' => 'profil-instansi', 'icon' => 'bx bxs-landmark', 'route' => 'admin.instansi.index'],
            
            [
                'title' => 'Kepegawaian', 
                'slug' => 'kepegawaian', 
                'icon' => 'bx bxs-id-card',
                'submenu' => [
                    ['title' => 'Data Pegawai', 'slug' => 'kepegawaian-data', 'route' => 'admin.kepegawaian.index'],
                    ['title' => 'Tugas Pegawai', 'slug' => 'kepegawaian-tugas', 'route' => 'admin.kepegawaian.tugas-kcd.index'],
                ],
            ],
            
            [
                'title' => 'Satuan Pendidikan', 
                'slug' => 'satuan-pendidikan', 
                'icon' => 'bx bxs-school',
                'submenu' => [
                    ['title' => 'Data Satuan Pendidikan', 'slug' => 'data-satuan-pendidikan', 'route' => 'admin.sekolah.index'],
                    ['title' => 'Data Spasial', 'slug' => 'data-spasial', 'route' => 'admin.dataspasial.index'],
                    ['title' => 'Rekapitulasi Sekolah', 'slug' => 'rekapitulasi-sekolah', 'route' => 'admin.sekolah.rekapitulasi'],
                ],
            ],

            [
                'title' => 'GTK', 
                'slug' => 'gtk', 
                'icon' => 'bx bxs-user-badge',
                'submenu' => [
                    ['title' => 'Guru', 'slug' => 'gtk-guru', 'route' => 'admin.gtk.guru.index'],
                    ['title' => 'Tendik', 'slug' => 'gtk-tendik', 'route' => 'admin.gtk.tendik.index'],
                    ['title' => 'Rekapitulasi GTK', 'slug' => 'rekapitulasi-gtk', 'route' => 'admin.gtk.rekapitulasi'],
                ],
            ],

            [
                'title' => 'Peserta Didik', 
                'slug' => 'peserta-didik', 
                'icon' => 'bx bx-user-pin',
                'submenu' => [
                    ['title' => 'Data Siswa', 'slug' => 'data-siswa', 'route' => 'admin.kesiswaan.siswa.index'],
                    ['title' => 'Rekapitulasi Siswa', 'slug' => 'rekapitulasi-siswa', 'route' => 'admin.kesiswaan.siswa.rekapitulasi'],
                ],
            ],

            [
                'title' => 'Layanan', 
                'slug' => 'layanan-kcd', 
                'icon' => 'bx bx-folder',
                'submenu' => [
                    [
                        'title' => 'Layanan GTK', 
                        'slug' => 'layanan-gtk', 
                        'badge_key' => 'total_layanan_gtk',
                        'submenu' => [
                            ['title' => 'Kenaikan Pangkat', 'slug' => 'layanan-kp', 'route' => 'admin.verifikasi.index', 'params' => ['kategori' => 'kenaikan-pangkat'], 'badge_key' => 'notif_kp'],
                            ['title' => 'KGB', 'slug' => 'layanan-kgb', 'route' => 'admin.verifikasi.index', 'params' => ['kategori' => 'kgb'], 'badge_key' => 'notif_kgb'],
                            ['title' => 'Mutasi', 'slug' => 'layanan-mutasi', 'route' => 'admin.verifikasi.index', 'params' => ['kategori' => 'mutasi'], 'badge_key' => 'notif_mutasi'],
                            ['title' => 'Relokasi', 'slug' => 'layanan-relokasi', 'route' => 'admin.verifikasi.index', 'params' => ['kategori' => 'relokasi'], 'badge_key' => 'notif_relokasi'],
                            ['title' => 'Satya Lencana', 'slug' => 'layanan-satya', 'route' => 'admin.verifikasi.index', 'params' => ['kategori' => 'satya-lencana'], 'badge_key' => 'notif_satya'],
                            ['title' => 'Hukuman Disiplin', 'slug' => 'layanan-hukdis', 'route' => 'admin.verifikasi.index', 'params' => ['kategori' => 'hukuman-disiplin'], 'badge_key' => 'notif_hukdis'],
                            ['title' => 'Verifikasi Lainnya', 'slug' => 'verifikasi-surat', 'route' => 'admin.verifikasi.index'],
                        ]
                    ],
                    ['title' => 'Layanan Peserta Didik', 'slug' => 'layanan-peserta-didik', 'route' => 'admin.verifikasi_pd.index'],
                ],
            ],

            ['title' => 'Dokumen Layanan', 'slug' => 'dokumen-layanan', 'icon' => 'bx bx-archive', 'route' => 'admin.dokumen-layanan.index'],

            [
                'title' => 'Administrasi Surat', 
                'slug' => 'administrasi-surat', 
                'icon' => 'bx bx-envelope',
                'submenu' => [
                    ['title' => 'Surat Masuk', 'slug' => 'surat-masuk', 'route' => 'admin.administrasi.surat-masuk.index'],
                    ['title' => 'Surat Keluar', 'slug' => 'surat-keluar-siswa', 'route' => 'admin.administrasi.surat-keluar-siswa.index'],
                    ['title' => 'Arsip Surat', 'slug' => 'arsip-surat', 'route' => 'admin.administrasi.arsip-surat.index'],
                    ['title' => 'Template Surat', 'slug' => 'tipe-surat', 'route' => 'admin.administrasi.tipe-surat.index'],
                ],
            ],

            ['title' => 'Daftar Antrian', 'slug' => 'daftar-antrian', 'icon' => 'bx bx-group', 'route' => 'admin.antrian.index'],
            ['title' => 'Web Profile', 'slug' => 'web-profile', 'icon' => 'bx bx-globe', 'route' => '#'],

            ['title' => 'SETTINGS', 'slug' => 'header-settings', 'is_header' => true],
            [
                'title' => 'Pengaturan', 
                'slug' => 'pengaturan-sistem', 
                'icon' => 'bx bx-cog',
                'submenu' => [
                    ['title' => 'Nomor Surat', 'slug' => 'pengaturan-nomor', 'route' => 'admin.administrasi.pengaturan-nomor.index'],
                    ['title' => 'Manajemen Menu', 'slug' => 'pengaturan-menu', 'route' => 'admin.settings.menus.index'],
                    ['title' => 'Hak Akses Role', 'slug' => 'role-access', 'route' => 'admin.settings.role-access.index'],
                    ['title' => 'Pengaturan Jabatan', 'slug' => 'pengaturan-jabatan-', 'route' => 'admin.kepegawaian_kcd.jabatan.index'],
                ],
            ],
            // Menu khusus buat redirect (tidak tampil di sidebar tapi butuh rute)
            ['title' => 'dashboard-pegawai', 'slug' => 'dashboard-pegawai', 'icon' => 'bx bx-home-circle', 'route' => 'admin.dashboard.pegawai', 'is_active' => false],
        ];

        // =================================================================
        // B. PROSES INSERT KE DATABASE
        // =================================================================
        $slugToIdMap = [];
        $orderCounter = 1;

        $processMenu = function ($menus, $parentId = null) use (&$processMenu, &$slugToIdMap, &$orderCounter) {
            foreach ($menus as $menu) {
                $menuId = DB::table('menus')->insertGetId([
                    'title'     => $menu['title'],
                    'slug'      => $menu['slug'],
                    'icon'      => $menu['icon'] ?? null,
                    'route'     => $menu['route'] ?? null,
                    'params'    => isset($menu['params']) ? json_encode($menu['params']) : null,
                    'badge_key' => $menu['badge_key'] ?? null,
                    'is_header' => $menu['is_header'] ?? false,
                    'parent_id' => $parentId,
                    'urutan'    => $orderCounter++,
                    'is_active' => $menu['is_active'] ?? true,
                    'created_at' => now(), 
                    'updated_at' => now(),
                ]);
                
                $slugToIdMap[$menu['slug']] = $menuId;

                if (isset($menu['submenu'])) {
                    $processMenu($menu['submenu'], $menuId);
                }
            }
        };

        $processMenu($sidebarMenu);

        // =================================================================
        // C. BERI AKSES KHUSUS KE ROLE "administrator" (SUPER ADMIN)
        // =================================================================
        $allMenuIds = array_values($slugToIdMap);
        $superAdminRole = 'administrator'; // Pakai nama role tunggal sesuai jabatan Riri

        foreach ($allMenuIds as $mid) {
            DB::table('menu_accesses')->insertOrIgnore([
                'role_name' => $superAdminRole,
                'menu_id' => $mid
            ]);
        }
        
        $this->command->info('✅ Menu terbaru berhasil disinkronkan! Akses Super Admin aktif.');
    }
}