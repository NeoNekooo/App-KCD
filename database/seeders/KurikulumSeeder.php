<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // <-- Jangan lupa import DB

class KurikulumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Mengosongkan tabel sebelum diisi untuk menghindari data duplikat saat seeding ulang
        DB::table('kurikulum')->truncate();

        DB::table('kurikulum')->insert([
            [
                'id' => 1,
                'nama_kurikulum' => 'SMK Merdeka'
            ],
            [
                'id' => 2,
                'nama_kurikulum' => 'K-13 Revisi'
            ],
        ]);
    }
}