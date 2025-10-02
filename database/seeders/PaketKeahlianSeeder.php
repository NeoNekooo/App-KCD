<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaketKeahlian;

class PaketKeahlianSeeder extends Seeder
{
    public function run(): void
    {
        PaketKeahlian::insert([
            ['kode' => 'RPL', 'nama_paket' => 'Rekayasa Perangkat Lunak'],
            ['kode' => 'TKJ', 'nama_paket' => 'Teknik Komputer dan Jaringan'],
            ['kode' => 'DKV', 'nama_paket' => 'Desain Komunikasi Visual'],
        ]);
    }
}
