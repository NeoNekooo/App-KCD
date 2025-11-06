<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TingkatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tingkat_pendaftarans')->insert([
            [
                'tingkat' => 01,
                'keterangan' => 'Satu',
                'is_active' => false, 
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tingkat' => 07,
                'keterangan' => 'Tujuh',
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tingkat' => 10,
                'keterangan' => 'sepuluh',
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
