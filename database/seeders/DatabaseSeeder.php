<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // <--- ini harus ditambahkan

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 2. Nonaktifkan pengecekan foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->call([
            AdminUserSeeder::class,
            // PengaturanAbsensiSeeder::class,
            // TingkatSeeder::class,
            CadisdikSeeder::class, // <-- Tambahan: Daftar UUID Wilayah
            InstansiSeeder::class, // <-- Tambahan: Jembatan UUID ke ID Integer
            RegionalAdminSeeder::class, // <-- Tambahan: Akun Contoh Wilayah VI
            // AdminSeeder::class,
            KasubagUserSeeder::class


        ]);

        // 3. Aktifkan kembali pengecekan foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
