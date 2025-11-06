<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EkstrakurikulerSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('ekstrakurikulers')->insert([
            [
                'nama_ekskul' => 'Pramuka',
                'pembina_id' => 1, // Zulkarnaen Iskandar Hidayat
                'prasarana' => 'Lapangan Utama',
            ],
            [
                'nama_ekskul' => 'Klub Robotik',
                'pembina_id' => 2, // ABDI KAMILUDIN LUTHFI
                'prasarana' => 'Lab Komputer 2',
            ],
        ]);
    }
}