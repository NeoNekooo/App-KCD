<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StrukturOrganisasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kepala = \App\Models\StrukturOrganisasi::create([
            'jabatan' => 'Kepala Cabang Dinas',
            'nama_pejabat' => 'Dr. H. Ahmad Saefudin, M.Pd.',
            'jenis_hubungan' => 'struktural',
            'urutan' => 1
        ]);

        \App\Models\StrukturOrganisasi::create([
            'parent_id' => $kepala->id,
            'jabatan' => 'Tim Ahli / Staf Khusus',
            'nama_pejabat' => 'Budi Santoso, S.Kom.',
            'jenis_hubungan' => 'asisten',
            'urutan' => 2
        ]);

        \App\Models\StrukturOrganisasi::create([
            'parent_id' => $kepala->id,
            'jabatan' => 'Kasubag Tata Usaha',
            'nama_pejabat' => 'Dra. Hj. Siti Aminah',
            'jenis_hubungan' => 'struktural',
            'urutan' => 3
        ]);

        \App\Models\StrukturOrganisasi::create([
            'parent_id' => $kepala->id,
            'jabatan' => 'Seksi Pelayanan',
            'nama_pejabat' => 'Ir. Gunawan Wibisono',
            'jenis_hubungan' => 'struktural',
            'urutan' => 4
        ]);
        
        \App\Models\StrukturOrganisasi::create([
            'parent_id' => $kepala->id,
            'jabatan' => 'Seksi Pembinaan',
            'nama_pejabat' => 'Drs. Rahmat Hidayat',
            'jenis_hubungan' => 'struktural',
            'urutan' => 5
        ]);
    }
}
