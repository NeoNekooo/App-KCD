<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Siswa;
use App\Models\Rombel;

class FixAllOrphanedStudents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'students:fix-all-orphans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Memperbaiki SEMUA siswa (kelas X, XI, XII) yang data kelasnya tidak valid.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai proses perbaikan data untuk semua siswa tanpa kelas valid...');

        // Ambil semua siswa yang relasi rombelnya gagal
        $orphanedStudents = Siswa::whereDoesntHave('rombel')->get();

        if ($orphanedStudents->isEmpty()) {
            $this->info("Tidak ditemukan siswa yang perlu diperbaiki. Data Anda sudah bersih!");
            return 0;
        }

        $this->warn("Ditemukan {$orphanedStudents->count()} siswa yang akan diperbaiki.");
        $progressBar = $this->output->createProgressBar($orphanedStudents->count());
        $progressBar->start();

        $fixedCount = 0;
        $failedCount = 0;

        foreach ($orphanedStudents as $siswa) {
            $tingkat = $siswa->tingkat_pendidikan_id; // misal: '11' atau '12'
            $kurikulum = $siswa->kurikulum_id_str;   // misal: 'SMK Merdeka Akuntansi (K)'

            // Cari singkatan jurusan dari nama kurikulum
            $singkatan = $this->getJurusanSingkatan($kurikulum);

            if ($singkatan) {
                // Cari rombel yang valid berdasarkan Tingkat DAN Jurusan
                $namaKelasRomawi = $this->getTingkatRomawi($tingkat); // misal: 'XI' atau 'XII'
                
                $validRombel = Rombel::where('nama', 'LIKE', "{$namaKelasRomawi} {$singkatan}%")->first();

                if ($validRombel) {
                    // Jika ditemukan, update data siswa
                    $siswa->rombongan_belajar_id = $validRombel->rombongan_belajar_id;
                    $siswa->save();
                    $fixedCount++;
                } else {
                    $failedCount++;
                }
            } else {
                $failedCount++;
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);
        $this->info("Proses selesai.");
        $this->info("Berhasil memperbaiki: {$fixedCount} siswa.");
        if ($failedCount > 0) {
            $this->warn("Gagal memperbaiki: {$failedCount} siswa (kemungkinan tidak ditemukan kelas pengganti yang cocok).");
            $this->line("Jalankan 'php artisan students:find-orphans' lagi untuk melihat sisa siswa yang bermasalah.");
        }

        return 0;
    }

    /**
     * Helper untuk mendapatkan singkatan jurusan dari nama kurikulum.
     */
    private function getJurusanSingkatan($kurikulum)
    {
        $mapping = [
            'Akuntansi' => 'AKL',
            'Desain Komunikasi Visual' => 'DKV',
            'Rekayasa Perangkat Lunak' => 'PPLG', // Disesuaikan dengan data Anda
            'Teknik Komputer dan Jaringan' => 'TJKT', // Disesuaikan dengan data Anda
            'Teknik Sepeda Motor' => 'TO', // Disesuaikan dengan data Anda
            'Manajemen Perkantoran' => 'MPLB',
        ];

        foreach ($mapping as $namaLengkap => $singkatan) {
            if (str_contains($kurikulum, $namaLengkap)) {
                return $singkatan;
            }
        }
        return null;
    }

    /**
     * Helper untuk mengubah angka tingkat menjadi Romawi.
     */
    private function getTingkatRomawi($tingkat)
    {
        switch ($tingkat) {
            case '10': return 'X';
            case '11': return 'XI';
            case '12': return 'XII';
            default: return 'UNKNOWN';
        }
    }
}