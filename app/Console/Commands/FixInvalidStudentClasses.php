<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Siswa;
use App\Models\Rombel;

class FixInvalidStudentClasses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'students:fix-classes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Memperbaiki data siswa kelas XII yang tidak memiliki rombongan belajar (kelas) yang valid.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai proses perbaikan data kelas siswa...');

        $jurusanMapping = [
            'Akuntansi dan Keuangan Lembaga' => 'AKL',
            'Desain Komunikasi Visual' => 'DKV',
            'Pengembangan Perangkat Lunak dan Gim' => 'PPLG',
            'Teknik Jaringan Komputer dan Telekomunikasi' => 'TJKT',
            'Teknik Otomotif' => 'TO',
            'Manajemen Perkantoran dan Layanan Bisnis' => 'MPLB',
        ];

        foreach ($jurusanMapping as $namaLengkapJurusan => $singkatan) {
            $this->line("Memproses jurusan: {$namaLengkapJurusan} ({$singkatan})...");

            $validRombelId = Rombel::where('nama', 'LIKE', "XII {$singkatan}%")->value('rombongan_belajar_id');

            if (!$validRombelId) {
                $this->warn("-> Tidak ditemukan rombel kelas XII yang valid untuk {$singkatan}. Melanjutkan ke jurusan berikutnya.");
                continue;
            }

            $this->info("-> Ditemukan rombel valid: {$validRombelId}");

            // --- PERBAIKAN DI SINI ---
            // Mengganti 'jurusan_id_str' menjadi 'kurikulum_id_str' dengan pencarian LIKE
            $siswaTidakValid = Siswa::where('tingkat_pendidikan_id', '12')
                                    ->where('kurikulum_id_str', 'LIKE', "%{$namaLengkapJurusan}%")
                                    ->whereDoesntHave('rombel');

            $jumlahSiswa = $siswaTidakValid->count();

            if ($jumlahSiswa > 0) {
                $this->info("-> Ditemukan {$jumlahSiswa} siswa dengan kelas tidak valid. Memperbarui...");
                $siswaTidakValid->update(['rombongan_belajar_id' => $validRombelId]);
                $this->info("-> Selesai.");
            } else {
                $this->line("-> Tidak ada siswa yang perlu diperbaiki untuk jurusan ini.");
            }
        }

        $this->info('=============================================');
        $this->info('Proses perbaikan data siswa telah selesai.');
        return 0;
    }
}

