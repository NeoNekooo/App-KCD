<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JamPelajaran;
use Illuminate\Support\Facades\DB;

class JamPelajaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Kosongkan tabel dulu agar tidak duplikat saat di-seed ulang
        DB::table('jam_pelajarans')->truncate();

        // ==========================================
        // SKENARIO 1: HARI SENIN (Ada Upacara)
        // ==========================================
        $jadwalSenin = [
            ['urutan' => 1, 'nama' => 'Upacara Bendera', 'mulai' => '07:00', 'selesai' => '07:45', 'tipe' => 'upacara'],
            ['urutan' => 2, 'nama' => 'Jam Ke-1', 'mulai' => '07:45', 'selesai' => '08:25', 'tipe' => 'kbm'],
            ['urutan' => 3, 'nama' => 'Jam Ke-2', 'mulai' => '08:25', 'selesai' => '09:05', 'tipe' => 'kbm'],
            ['urutan' => 4, 'nama' => 'Jam Ke-3', 'mulai' => '09:05', 'selesai' => '09:45', 'tipe' => 'kbm'],
            ['urutan' => 5, 'nama' => 'Istirahat Pertama', 'mulai' => '09:45', 'selesai' => '10:00', 'tipe' => 'istirahat'],
            ['urutan' => 6, 'nama' => 'Jam Ke-4', 'mulai' => '10:00', 'selesai' => '10:40', 'tipe' => 'kbm'],
            ['urutan' => 7, 'nama' => 'Jam Ke-5', 'mulai' => '10:40', 'selesai' => '11:20', 'tipe' => 'kbm'],
            ['urutan' => 8, 'nama' => 'Jam Ke-6', 'mulai' => '11:20', 'selesai' => '12:00', 'tipe' => 'kbm'],
            ['urutan' => 9, 'nama' => 'Ishoma (Dzuhur)', 'mulai' => '12:00', 'selesai' => '12:30', 'tipe' => 'istirahat'],
            ['urutan' => 10, 'nama' => 'Jam Ke-7', 'mulai' => '12:30', 'selesai' => '13:10', 'tipe' => 'kbm'],
            ['urutan' => 11, 'nama' => 'Jam Ke-8', 'mulai' => '13:10', 'selesai' => '13:50', 'tipe' => 'kbm'],
        ];

        $this->insertJadwal('Senin', $jadwalSenin);

        // ==========================================
        // SKENARIO 2: SELASA, RABU, KAMIS (Normal)
        // ==========================================
        $jadwalNormal = [
            ['urutan' => 1, 'nama' => 'Literasi Pagi', 'mulai' => '07:00', 'selesai' => '07:15', 'tipe' => 'lainnya'],
            ['urutan' => 2, 'nama' => 'Jam Ke-1', 'mulai' => '07:15', 'selesai' => '08:00', 'tipe' => 'kbm'],
            ['urutan' => 3, 'nama' => 'Jam Ke-2', 'mulai' => '08:00', 'selesai' => '08:45', 'tipe' => 'kbm'],
            ['urutan' => 4, 'nama' => 'Jam Ke-3', 'mulai' => '08:45', 'selesai' => '09:30', 'tipe' => 'kbm'],
            ['urutan' => 5, 'nama' => 'Istirahat Pertama', 'mulai' => '09:30', 'selesai' => '09:45', 'tipe' => 'istirahat'],
            ['urutan' => 6, 'nama' => 'Jam Ke-4', 'mulai' => '09:45', 'selesai' => '10:30', 'tipe' => 'kbm'],
            ['urutan' => 7, 'nama' => 'Jam Ke-5', 'mulai' => '10:30', 'selesai' => '11:15', 'tipe' => 'kbm'],
            ['urutan' => 8, 'nama' => 'Jam Ke-6', 'mulai' => '11:15', 'selesai' => '12:00', 'tipe' => 'kbm'],
            ['urutan' => 9, 'nama' => 'Ishoma (Dzuhur)', 'mulai' => '12:00', 'selesai' => '12:30', 'tipe' => 'istirahat'],
            ['urutan' => 10, 'nama' => 'Jam Ke-7', 'mulai' => '12:30', 'selesai' => '13:15', 'tipe' => 'kbm'],
            ['urutan' => 11, 'nama' => 'Jam Ke-8', 'mulai' => '13:15', 'selesai' => '14:00', 'tipe' => 'kbm'],
        ];

        foreach (['Selasa', 'Rabu', 'Kamis'] as $hari) {
            $this->insertJadwal($hari, $jadwalNormal);
        }

        // ==========================================
        // SKENARIO 3: JUMAT (Pendek)
        // ==========================================
        $jadwalJumat = [
            ['urutan' => 1, 'nama' => 'Yasinan / Rohis', 'mulai' => '07:00', 'selesai' => '07:30', 'tipe' => 'lainnya'],
            ['urutan' => 2, 'nama' => 'Jam Ke-1', 'mulai' => '07:30', 'selesai' => '08:10', 'tipe' => 'kbm'],
            ['urutan' => 3, 'nama' => 'Jam Ke-2', 'mulai' => '08:10', 'selesai' => '08:50', 'tipe' => 'kbm'],
            ['urutan' => 4, 'nama' => 'Istirahat', 'mulai' => '08:50', 'selesai' => '09:05', 'tipe' => 'istirahat'],
            ['urutan' => 5, 'nama' => 'Jam Ke-3', 'mulai' => '09:05', 'selesai' => '09:45', 'tipe' => 'kbm'],
            ['urutan' => 6, 'nama' => 'Jam Ke-4', 'mulai' => '09:45', 'selesai' => '10:25', 'tipe' => 'kbm'],
            ['urutan' => 7, 'nama' => 'Jam Ke-5', 'mulai' => '10:25', 'selesai' => '11:05', 'tipe' => 'kbm'],
            ['urutan' => 8, 'nama' => 'Persiapan Jumatan', 'mulai' => '11:05', 'selesai' => '11:30', 'tipe' => 'lainnya'],
        ];

        $this->insertJadwal('Jumat', $jadwalJumat);
    }

    // Helper function biar kodenya rapi
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
