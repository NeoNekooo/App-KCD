<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\EncryptionService;

class EncryptExistingSiswa extends Command
{
    protected $signature = 'siswa:encrypt {--limit=100}';
    protected $description = 'Enkripsi massal data siswa yang masih plain text di database KCD';

    public function handle()
    {
        $tableName = 'siswas';
        $columns = EncryptionService::getEncryptedColumns()[$tableName] ?? [];

        if (empty($columns)) {
            $this->error("Tidak ada kolom yang terdaftar untuk enkripsi di tabel $tableName.");
            return;
        }

        $this->info("Memulai enkripsi massal untuk tabel [$tableName]...");
        
        $total = DB::table($tableName)->count();
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $processed = 0;
        $updated = 0;

        DB::table($tableName)->orderBy('id')->chunk(100, function ($rows) use ($columns, $tableName, &$processed, &$updated, $bar) {
            foreach ($rows as $row) {
                $data = (array) $row;
                $needsUpdate = false;
                $updatePayload = [];

                foreach ($columns as $col) {
                    if (isset($data[$col]) && $data[$col] !== null && $data[$col] !== '') {
                        // Jika belum terenkripsi (tidak diawali eyj...)
                        if (strpos($data[$col], 'eyJpdi') === false) {
                            $updatePayload[$col] = EncryptionService::encrypt($data[$col]);
                            $needsUpdate = true;
                        }
                    }
                }

                if ($needsUpdate) {
                    DB::table($tableName)->where('id', $data['id'])->update($updatePayload);
                    $updated++;
                }

                $processed++;
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info("Selesai! Berhasil memproses $processed data. $updated data diperbarui ke format terenkripsi.");
    }
}
