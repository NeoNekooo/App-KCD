<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JurusanSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('jurusan')->truncate();
        DB::table('jurusan')->insert([
            ['id' => 1, 'nama_jurusan' => 'Pengembangan Perangkat Lunak dan Gim'],
            ['id' => 2, 'nama_jurusan' => 'Teknik Jaringan Komputer dan Telekomunikasi'],
            ['id' => 3, 'nama_jurusan' => 'Teknik Otomotif'],
            ['id' => 4, 'nama_jurusan' => 'Manajemen Perkantoran dan Layanan Bisnis'],
            ['id' => 5, 'nama_jurusan' => 'Desain Komunikasi Visual'],
            ['id' => 6, 'nama_jurusan' => 'Akuntansi dan Keuangan Lembaga'],
            ['id' => 7, 'nama_jurusan' => 'Teknik Sepeda Motor'],
            ['id' => 8, 'nama_jurusan' => 'Akuntansi'],
            ['id' => 9, 'nama_jurusan' => 'Manajemen Perkantoran'],
            ['id' => 10, 'nama_jurusan' => 'Rekayasa Perangkat Lunak'],
        ]);
    }
}