<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PtkSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('ptk')->truncate();
        DB::table('ptk')->insert([
            ['id' => 1, 'nama' => 'Zulkarnaen Iskandar Hidayat'],
            ['id' => 2, 'nama' => 'ABDI KAMILUDIN LUTHFI'],
            ['id' => 3, 'nama' => 'Abdul Gani'],
            ['id' => 5, 'nama' => 'Agi Herawan'],
            ['id' => 7, 'nama' => 'Ahmad Sirojudin'],
            ['id' => 12, 'nama' => 'Ati Rohayati'],
            ['id' => 14, 'nama' => 'Bambang Muhamad'],
            ['id' => 15, 'nama' => 'Bella Saadatul Mardiyah'],
            ['id' => 16, 'nama' => 'Cecep Supriatna'],
            ['id' => 18, 'nama' => 'D. AGUS MUHARAM'],
            ['id' => 21, 'nama' => 'Deni Supriadi'],
            ['id' => 22, 'nama' => 'DEVI SILVIA OKTAVIANI'],
            ['id' => 23, 'nama' => 'DINA IKA AGUSTIANI'],
            ['id' => 28, 'nama' => 'Encep Dedi Suhendar'],
            ['id' => 35, 'nama' => 'Herher Abdul Kohar'],
            ['id' => 39, 'nama' => 'IRMA ALFIANI'],
            ['id' => 40, 'nama' => 'Irma Nur Rohmah'],
            ['id' => 42, 'nama' => 'Lastri Aisyati Radiah'],
            ['id' => 44, 'nama' => 'Mahmudin'],
            ['id' => 50, 'nama' => 'N AGIS MULYA PURNAMA SARI'],
            ['id' => 51, 'nama' => 'Neneng Anggriani'],
            ['id' => 52, 'nama' => 'NOVI ILHAM ADELLA'],
            ['id' => 53, 'nama' => 'Nu\'man'],
            ['id' => 55, 'nama' => 'Nuri Purnamasari'],
            ['id' => 58, 'nama' => 'Piat Rikmawansyah'],
            ['id' => 59, 'nama' => 'Ranny Ramdhayani'],
            ['id' => 63, 'nama' => 'Rudi Permadi'],
            ['id' => 64, 'nama' => 'Siska Rismalinda'],
            ['id' => 65, 'nama' => 'SITI KARIMA'],
            ['id' => 71, 'nama' => 'Utep Sutiana'],
            ['id' => 73, 'nama' => 'ZEDI SUMANJAYA'],
        ]);
    }
}