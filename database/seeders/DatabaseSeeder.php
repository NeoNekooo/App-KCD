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
            PengaturanAbsensiSeeder::class,
            TingkatSeeder::class,
        ]);

        // 3. Aktifkan kembali pengecekan foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
