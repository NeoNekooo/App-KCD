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
        // A. DATA KONFIGURASI MENU
        // =================================================================
        $sidebarMenu = [
            // --- GRUP UTAMA (MENYATU TANPA HEADER) ---
            [
                'title' => 'Dashboard', 
                'slug' => 'dashboard', 
                'icon' => 'bx bx-home-circle', 
                'route' => 'admin.dashboard',
            ],
            [
                'title' => 'Profil Saya', 
                'slug' => 'profil-saya', 
                'icon' => 'bx bx-user', 
                'route' => 'admin.kepegawaian.me',
            ],
            [
                'title' => 'Profil Instansi', 
                'slug' => 'profil-instansi', 
                'icon' => 'bx bxs-landmark', 
                'route' => 'admin.instansi.index',
            ],
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
                'route' => 'admin.sekolah.index',
            ],
            [
                'title' => 'GTK', 
                'slug' => 'gtk', 
                'icon' => 'bx bxs-user-badge',
                'submenu' => [
                    ['title' => 'Guru', 'slug' => 'gtk-guru', 'route' => 'admin.gtk.guru.index'],
                    ['title' => 'Tendik', 'slug' => 'gtk-tendik', 'route' => 'admin.gtk.tendik.index'],
                ],
            ],
            [
                'title' => 'Peserta Didik', 
                'slug' => 'peserta-didik', 
                'icon' => 'bx bx-user-pin',
                'submenu' => [
                    ['title' => 'Data Siswa', 'slug' => 'data-siswa', 'route' => 'admin.kesiswaan.siswa.index'],
                ],
            ],
            [
                'title' => 'Layanan GTK', 
                'slug' => 'layanan-gtk', 
                'icon' => 'bx bx-briefcase-alt-2', 
                'badge_key' => 'total_layanan_gtk', 
                'submenu' => [
                    ['title' => 'Kenaikan Pangkat', 'slug' => 'layanan-kp', 'route' => 'admin.verifikasi.index', 'params' => ['kategori' => 'kenaikan-pangkat'], 'badge_key' => 'notif_kp'],
                    ['title' => 'KGB', 'slug' => 'layanan-kgb', 'route' => 'admin.verifikasi.index', 'params' => ['kategori' => 'kgb'], 'badge_key' => 'notif_kgb'],
                    ['title' => 'Mutasi', 'slug' => 'layanan-mutasi', 'route' => 'admin.verifikasi.index', 'params' => ['kategori' => 'mutasi'], 'badge_key' => 'notif_mutasi'],
                    ['title' => 'Relokasi', 'slug' => 'layanan-relokasi', 'route' => 'admin.verifikasi.index', 'params' => ['kategori' => 'relokasi'], 'badge_key' => 'notif_relokasi'],
                    ['title' => 'Satya Lencana', 'slug' => 'layanan-satya', 'route' => 'admin.verifikasi.index', 'params' => ['kategori' => 'satya-lencana'], 'badge_key' => 'notif_satya'],
                    ['title' => 'Hukuman Disiplin', 'slug' => 'layanan-hukdis', 'route' => 'admin.verifikasi.index', 'params' => ['kategori' => 'hukuman-disiplin'], 'badge_key' => 'notif_hukdis'],
                    ['title' => 'Verifikasi Lainnya', 'slug' => 'verifikasi-surat', 'route' => 'admin.verifikasi.index', 'params' => []],
                ],
            ],
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
            [
                'title' => 'Web Profile', 
                'slug' => 'web-profile', 
                'icon' => 'bx bx-globe', 
                'route' => '#',
            ],

            // --- HEADER: SETTINGS (INI DIPISAH) ---
            [
                'title' => 'SETTINGS', 
                'slug' => 'header-settings', 
                'is_header' => true, 
            ],
            [
                'title' => 'Pengaturan', 
                'slug' => 'pengaturan-sistem', 
                'icon' => 'bx bx-cog',
                'submenu' => [
                    ['title' => 'Nomor Surat', 'slug' => 'pengaturan-nomor', 'route' => 'admin.administrasi.pengaturan-nomor.index'],
                    ['title' => 'Manajemen Menu', 'slug' => 'pengaturan-menu', 'route' => 'admin.settings.menus.index'],
                    ['title' => 'Hak Akses Role', 'slug' => 'role-access', 'route' => 'admin.settings.role-access.index'],
                ],
            ],
        ];

        // =================================================================
        // B. PROSES INSERT KE DATABASE
        // =================================================================
        
        $slugToIdMap = [];
        $orderCounter = 1;

        foreach ($sidebarMenu as $menu) {
            $menuId = DB::table('menus')->insertGetId([
                'title'     => $menu['title'],
                'slug'      => $menu['slug'],
                'icon'      => $menu['icon'] ?? null,
                'route'     => $menu['route'] ?? null,
                'params'    => isset($menu['params']) ? json_encode($menu['params']) : null,
                'badge_key' => $menu['badge_key'] ?? null,
                'is_header' => $menu['is_header'] ?? false,
                'urutan'    => $orderCounter++,
                'is_active' => true,
                'created_at' => now(), 
                'updated_at' => now(),
            ]);
            
            $slugToIdMap[$menu['slug']] = $menuId;

            if (isset($menu['submenu'])) {
                foreach ($menu['submenu'] as $child) {
                    $childId = DB::table('menus')->insertGetId([
                        'title'     => $child['title'],
                        'slug'      => $child['slug'],
                        'route'     => $child['route'] ?? null,
                        'params'    => isset($child['params']) ? json_encode($child['params']) : null,
                        'badge_key' => $child['badge_key'] ?? null,
                        'parent_id' => $menuId,
                        'urutan'    => $orderCounter++,
                        'is_active' => true,
                        'created_at' => now(), 
                        'updated_at' => now(),
                    ]);
                    $slugToIdMap[$child['slug']] = $childId;
                }
            }
        }

        // =================================================================
        // C. BERI AKSES KHUSUS KE "Admin"
        // =================================================================
        
        $allMenuIds = array_values($slugToIdMap);
        
        // 🔥 HANYA ROLE 'Admin' YANG DAPAT AKSES FULL
        $superAdminRole = 'Admin'; 

        foreach ($allMenuIds as $mid) {
            DB::table('menu_accesses')->insertOrIgnore([
                'role_name' => $superAdminRole,
                'menu_id' => $mid
            ]);
        }
        
        $this->command->info('✅ Menu berhasil di-reset! Akses eksklusif untuk role Admin.');
    }
}