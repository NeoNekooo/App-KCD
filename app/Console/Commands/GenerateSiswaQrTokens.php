<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Siswa;
use Illuminate\Support\Str;

class GenerateSiswaQrTokens extends Command
{
    protected $signature = 'siswa:generate-qr-tokens';
    protected $description = 'Membuat token QR unik untuk semua siswa yang belum memilikinya';

    public function handle()
    {
        $siswasToUpdate = Siswa::whereNull('qr_token')->get();

        if ($siswasToUpdate->isEmpty()) {
            $this->info('Semua siswa sudah memiliki token QR.');
            return 0;
        }

        $bar = $this->output->createProgressBar($siswasToUpdate->count());
        $bar->start();

        foreach ($siswasToUpdate as $siswa) {
            $siswa->qr_token = Str::uuid()->toString();
            $siswa->save();
            $bar->advance();
        }

        $bar->finish();
        $this->info("\nToken QR berhasil dibuat untuk " . $siswasToUpdate->count() . " siswa.");
        return 0;
    }
}