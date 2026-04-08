<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WebManagementSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Get last order
        $lastOrder = DB::table('menus')->max('urutan') ?? 0;

        // 2. Insert Parent Menu
        $parentId = DB::table('menus')->insertGetId([
            'title'     => 'Manajemen Website',
            'slug'      => 'manajemen-website',
            'icon'      => 'bx bx-globe',
            'route'     => null,
            'is_header' => false,
            'parent_id' => null,
            'urutan'    => $lastOrder + 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Insert Submenus
        $submenus = [
            ['title' => 'Slider Beranda', 'slug' => 'web-slider', 'route' => 'admin.website.sliders.index'],
            ['title' => 'Sambutan Pimpinan', 'slug' => 'web-welcome', 'route' => 'admin.website.welcome.index'],
            ['title' => 'Pengaturan Website', 'slug' => 'web-settings', 'route' => 'admin.website.settings.index'],
        ];

        $order = $lastOrder + 2;
        foreach ($submenus as $menu) {
            $menuId = DB::table('menus')->insertGetId([
                'title'     => $menu['title'],
                'slug'      => $menu['slug'],
                'icon'      => null,
                'route'     => $menu['route'],
                'is_header' => false,
                'parent_id' => $parentId,
                'urutan'    => $order++,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Give access to administrator
            DB::table('menu_accesses')->insertOrIgnore([
                'role_name' => 'administrator',
                'menu_id' => $menuId
            ]);
            
            // Give access to admin (if exists)
            DB::table('menu_accesses')->insertOrIgnore([
                'role_name' => 'admin',
                'menu_id' => $menuId
            ]);
        }

        // Give parent access
        DB::table('menu_accesses')->insertOrIgnore([
            'role_name' => 'administrator',
            'menu_id' => $parentId
        ]);
        DB::table('menu_accesses')->insertOrIgnore([
            'role_name' => 'admin',
            'menu_id' => $parentId
        ]);

        $this->command->info('✅ Menu Manajemen Website berhasil ditambahkan!');
    }
}
