<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Siswa;

class FindOrphanedStudents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'students:find-orphans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menemukan dan menampilkan semua siswa yang rombongan belajarnya (kelas) tidak valid atau tidak ada di tabel rombels.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Mencari siswa dengan data kelas yang tidak valid...");

        // Menggunakan whereDoesntHave untuk mencari semua siswa yang relasi 'rombel'-nya gagal.
        // Ini adalah cara paling andal untuk menemukan siswa "yatim piatu" tanpa kelas.
        $orphanedStudents = Siswa::whereDoesntHave('rombel')->get([
            'id', 'nama', 'tingkat_pendidikan_id', 'rombongan_belajar_id', 'kurikulum_id_str'
        ]);

        if ($orphanedStudents->isEmpty()) {
            $this->info("Selamat! Tidak ditemukan siswa dengan data kelas yang tidak valid.");
            return 0;
        }

        $this->warn("Ditemukan {$orphanedStudents->count()} siswa dengan data kelas tidak valid:");

        // Menyiapkan data untuk ditampilkan dalam format tabel
        $tableData = $orphanedStudents->map(function ($siswa) {
            return [
                'ID Siswa' => $siswa->id,
                'Nama' => $siswa->nama,
                'Tingkat' => $siswa->tingkat_pendidikan_id,
                'ID Rombel (Tidak Valid)' => $siswa->rombongan_belajar_id,
                'Kurikulum' => $siswa->kurikulum_id_str,
            ];
        });

        // Menampilkan data dalam tabel yang rapi di terminal
        $this->table(
            ['ID Siswa', 'Nama', 'Tingkat', 'ID Rombel (Tidak Valid)', 'Kurikulum'],
            $tableData
        );

        $this->line("Silakan periksa data siswa di atas untuk diperbaiki secara manual atau dengan skrip lebih lanjut.");

        return 0;
    }
}