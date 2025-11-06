<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PengaturanAbsensiSeeder extends Seeder
{
    public function run(): void
    {
        // Kosongkan tabel sebelum mengisi
        DB::table('pengaturan_absensi')->delete();

        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        foreach ($hari as $namaHari) {
            DB::table('pengaturan_absensi')->insert([
                'hari' => $namaHari,
                'jam_masuk_sekolah' => '07:00:00',
                'jam_pulang_sekolah' => ($namaHari == 'Jumat') ? '11:30:00' : '15:00:00', // Contoh jadwal Jumat lebih pendek
                'batas_toleransi_terlambat' => 15,
                'is_active' => !in_array($namaHari, ['Sabtu', 'Minggu']), // Sabtu & Minggu non-aktif
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}