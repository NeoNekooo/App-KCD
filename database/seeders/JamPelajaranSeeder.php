<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JamPelajaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; // Tambahkan ini

class JamPelajaranSeeder extends Seeder
{
    public function run()
    {
        // 1. Matikan Foreign Key Check (Cara Laravel yang lebih aman)
        Schema::disableForeignKeyConstraints();

        // 2. Gunakan delete() bukan truncate() agar tidak error constraint
        DB::table('jam_pelajarans')->delete();

        // 3. Reset Auto Increment ke 1 (Opsional, biar ID mulai dari 1 lagi)
        // Cek dulu driver database, kalau MySQL pakai ini:
        DB::statement('ALTER TABLE jam_pelajarans AUTO_INCREMENT = 1;');

        // 4. Nyalakan lagi Foreign Key Check
        Schema::enableForeignKeyConstraints();

        // --- MULAI INPUT DATA ---

        // Data Senin (Ada Upacara)
        $jadwalSenin = [
            ['urutan' => 1, 'nama' => 'Upacara Bendera', 'mulai' => '07:00', 'selesai' => '07:45', 'tipe' => 'upacara'],
            ['urutan' => 2, 'nama' => 'Jam Ke-1', 'mulai' => '07:45', 'selesai' => '08:25', 'tipe' => 'kbm'],
            ['urutan' => 3, 'nama' => 'Jam Ke-2', 'mulai' => '08:25', 'selesai' => '09:05', 'tipe' => 'kbm'],
            ['urutan' => 4, 'nama' => 'Jam Ke-3', 'mulai' => '09:05', 'selesai' => '09:45', 'tipe' => 'kbm'],
            ['urutan' => 5, 'nama' => 'Istirahat', 'mulai' => '09:45', 'selesai' => '10:00', 'tipe' => 'istirahat'],
            ['urutan' => 6, 'nama' => 'Jam Ke-4', 'mulai' => '10:00', 'selesai' => '10:40', 'tipe' => 'kbm'],
            ['urutan' => 7, 'nama' => 'Jam Ke-5', 'mulai' => '10:40', 'selesai' => '11:20', 'tipe' => 'kbm'],
            ['urutan' => 8, 'nama' => 'Jam Ke-6', 'mulai' => '11:20', 'selesai' => '12:00', 'tipe' => 'kbm'],
            ['urutan' => 9, 'nama' => 'Ishoma', 'mulai' => '12:00', 'selesai' => '12:30', 'tipe' => 'istirahat'],
            ['urutan' => 10, 'nama' => 'Jam Ke-7', 'mulai' => '12:30', 'selesai' => '13:10', 'tipe' => 'kbm'],
            ['urutan' => 11, 'nama' => 'Jam Ke-8', 'mulai' => '13:10', 'selesai' => '13:50', 'tipe' => 'kbm'],
        ];
        $this->insertJadwal('Senin', $jadwalSenin);

        // Data Selasa-Kamis (Normal)
        $jadwalNormal = [
            ['urutan' => 1, 'nama' => 'Literasi', 'mulai' => '07:00', 'selesai' => '07:15', 'tipe' => 'lainnya'],
            ['urutan' => 2, 'nama' => 'Jam Ke-1', 'mulai' => '07:15', 'selesai' => '08:00', 'tipe' => 'kbm'],
            ['urutan' => 3, 'nama' => 'Jam Ke-2', 'mulai' => '08:00', 'selesai' => '08:45', 'tipe' => 'kbm'],
            ['urutan' => 4, 'nama' => 'Jam Ke-3', 'mulai' => '08:45', 'selesai' => '09:30', 'tipe' => 'kbm'],
            ['urutan' => 5, 'nama' => 'Istirahat', 'mulai' => '09:30', 'selesai' => '09:45', 'tipe' => 'istirahat'],
            ['urutan' => 6, 'nama' => 'Jam Ke-4', 'mulai' => '09:45', 'selesai' => '10:30', 'tipe' => 'kbm'],
            ['urutan' => 7, 'nama' => 'Jam Ke-5', 'mulai' => '10:30', 'selesai' => '11:15', 'tipe' => 'kbm'],
            ['urutan' => 8, 'nama' => 'Jam Ke-6', 'mulai' => '11:15', 'selesai' => '12:00', 'tipe' => 'kbm'],
            ['urutan' => 9, 'nama' => 'Ishoma', 'mulai' => '12:00', 'selesai' => '12:30', 'tipe' => 'istirahat'],
            ['urutan' => 10, 'nama' => 'Jam Ke-7', 'mulai' => '12:30', 'selesai' => '13:15', 'tipe' => 'kbm'],
            ['urutan' => 11, 'nama' => 'Jam Ke-8', 'mulai' => '13:15', 'selesai' => '14:00', 'tipe' => 'kbm'],
        ];
        foreach (['Selasa', 'Rabu', 'Kamis'] as $hari) {
            $this->insertJadwal($hari, $jadwalNormal);
        }

        // Data Jumat
        $jadwalJumat = [
            ['urutan' => 1, 'nama' => 'Rohis/Yasinan', 'mulai' => '07:00', 'selesai' => '07:30', 'tipe' => 'lainnya'],
            ['urutan' => 2, 'nama' => 'Jam Ke-1', 'mulai' => '07:30', 'selesai' => '08:10', 'tipe' => 'kbm'],
            ['urutan' => 3, 'nama' => 'Jam Ke-2', 'mulai' => '08:10', 'selesai' => '08:50', 'tipe' => 'kbm'],
            ['urutan' => 4, 'nama' => 'Istirahat', 'mulai' => '08:50', 'selesai' => '09:05', 'tipe' => 'istirahat'],
            ['urutan' => 5, 'nama' => 'Jam Ke-3', 'mulai' => '09:05', 'selesai' => '09:45', 'tipe' => 'kbm'],
            ['urutan' => 6, 'nama' => 'Jam Ke-4', 'mulai' => '09:45', 'selesai' => '10:25', 'tipe' => 'kbm'],
            ['urutan' => 7, 'nama' => 'Jam Ke-5', 'mulai' => '10:25', 'selesai' => '11:05', 'tipe' => 'kbm'],
        ];
        $this->insertJadwal('Jumat', $jadwalJumat);
    }

    private function insertJadwal($hari, $dataJadwal)
    {
        foreach ($dataJadwal as $data) {
            JamPelajaran::create([
                'hari' => $hari,
                'urutan' => $data['urutan'],
                'nama' => $data['nama'],
                'jam_mulai' => $data['mulai'],
                'jam_selesai' => $data['selesai'],
                'tipe' => $data['tipe'],
            ]);
        }
    }
}
