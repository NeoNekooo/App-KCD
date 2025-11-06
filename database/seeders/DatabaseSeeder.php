<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 2. Nonaktifkan pengecekan foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->call([
            UserSeeder::class,
            PtkSeeder::class,
            JurusanSeeder::class,
            KurikulumSeeder::class,
            EkstrakurikulerSeeder::class,
            TingkatSeeder::class,
        ]);

        // 3. Aktifkan kembali pengecekan foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
