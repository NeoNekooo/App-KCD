<?php

namespace Database\Seeders;

use App\Models\Cadisdik;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CadisdikSeeder extends Seeder
{
    /**
     * Seeder untuk menyamakan UUID Cadisdik dengan SIAKAD.
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Cadisdik::truncate();
        Schema::enableForeignKeyConstraints();

        $wilayahs = [
            'I'    => 'e1b0c74a-5f9a-4c2d-8b3e-7a1d1e4c5b6a',
            'II'   => 'f4d2e8b1-3c6a-4a7b-9d5c-8e2f1a6b0c3d',
            'III'  => 'a2c1b3d4-e5f6-4a7b-8c9d-0e1f2a3b4c5d',
            'IV'   => 'b9d8c7a6-1e2f-4b0d-9c8a-7b6a5e4d3c2b',
            'V'    => 'c5a4b3d2-e1f0-4a9b-8c7d-6e5a4b3c2d1e',
            'VI'   => 'd8e9a0c1-b2d3-4e5f-9a0b-1c2d3e4f5a6b',
            'VII'  => '9a8b7c6d-5e4f-4a3b-2c1d-0e9f8a7b6c5d',
            'VIII' => '1a2b3c4d-5e6f-4a7b-8c9d-0e1f2a3b4c5d',
            'IX'   => 'f1e2d3c4-b5a6-4a7b-8c9d-0e1f2a3b4c5d',
            'X'    => 'c9b8a7f6-e5d4-4c3b-2a1e-0f9e8d7c6b5a',
            'XI'   => 'd1c2b3a4-e5f6-4a7b-8c9d-0e1f2a3b4c5d',
            'XII'  => 'b2a3c4d5-e6f7-4a8b-9c0d-1e2f3a4b5c6d',
            'XIII' => '8a7b6c5d-4e3f-4f2a-1b0c-9d8e7f6a5b4c',
        ];

        foreach ($wilayahs as $nama => $uuid) {
            Cadisdik::create([
                'id' => $uuid,
                'nama' => "Wilayah " . $nama
            ]);
        }
    }
}
