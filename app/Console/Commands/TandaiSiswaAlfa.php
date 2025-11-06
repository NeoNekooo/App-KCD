<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Siswa;
use App\Models\AbsensiSiswa;
use App\Models\HariLibur; // Pastikan model ini ada
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TandaiSiswaAlfa extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'absensi:alfa';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menandai siswa yang tidak memiliki catatan kehadiran sebagai Alfa pada hari ini.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::now();
        $todayString = $today->toDateString();
        $namaHariIni = $today->isoFormat('dddd');

        $this->info("Memulai proses pengecekan absensi Alfa untuk tanggal: {$todayString}...");

        // 1. Cek apakah hari ini adalah hari libur atau tidak aktif
        $isLibur = HariLibur::where('tanggal', $todayString)->exists();
        if ($isLibur) {
            $this->info("Proses dibatalkan: Hari ini ({$todayString}) adalah hari libur.");
            return;
        }

        $pengaturan = DB::table('pengaturan_absensi')->where('hari', $namaHariIni)->first();
        if (!$pengaturan || !$pengaturan->is_active) {
            $this->info("Proses dibatalkan: Tidak ada jadwal absensi aktif untuk hari {$namaHariIni}.");
            return;
        }

        // 2. Ambil semua ID siswa yang aktif
        // Asumsi: semua siswa di tabel 'siswas' adalah aktif.
        // Jika ada kolom status, tambahkan ->where('status', 'Aktif')
        $semuaSiswaIds = Siswa::pluck('id');

        // 3. Ambil semua ID siswa yang sudah memiliki catatan absensi hari ini
        $siswaSudahAbsenIds = AbsensiSiswa::where('tanggal', $todayString)
                                          ->pluck('siswa_id');

        // 4. Tentukan siswa yang belum punya catatan absensi (calon Alfa)
        $siswaAlfaIds = $semuaSiswaIds->diff($siswaSudahAbsenIds);

        if ($siswaAlfaIds->isEmpty()) {
            $this->info('Semua siswa sudah memiliki catatan kehadiran. Tidak ada yang ditandai Alfa.');
            return;
        }

        $this->info("Ditemukan {$siswaAlfaIds->count()} siswa tanpa catatan kehadiran. Menandai sebagai Alfa...");

        // 5. Buat data absensi Alfa untuk siswa-siswa tersebut
        $dataToInsert = [];
        foreach ($siswaAlfaIds as $siswaId) {
            $dataToInsert[] = [
                'siswa_id' => $siswaId,
                'tanggal' => $todayString,
                'status' => 'Alfa',
                'status_kehadiran' => 'Tidak Hadir',
                'keterangan' => 'Dibuat otomatis oleh sistem',
                'dicatat_oleh' => 1, // ID user sistem/admin default
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Masukkan semua data sekaligus untuk efisiensi
        AbsensiSiswa::insert($dataToInsert);

        $this->info("Proses selesai. {$siswaAlfaIds->count()} siswa berhasil ditandai sebagai Alfa.");
        Log::info("Scheduler absensi:alfa berhasil dijalankan. {$siswaAlfaIds->count()} siswa ditandai Alfa.");
    }
}