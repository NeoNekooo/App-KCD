<?php

/**
 * Mesin Pengupas Enkripsi Ganda (The Layer Peeler) v1.0
 * Jalankan via: https://kcd6.hexanusa.com/cleanup.php
 */

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

header('Content-Type: text/plain');

echo "=== OPERASI PENGUPASAN ENKRIPSI GANDA ===\n\n";

use App\Services\EncryptionService;
use Illuminate\Support\Facades\DB;

$tables = EncryptionService::getEncryptedColumns();

foreach ($tables as $table => $columns) {
    echo "Memproses Tabel: $table...\n";
    
    $records = DB::table($table)->get();
    $updatedCount = 0;

    foreach ($records as $record) {
        $updates = [];
        foreach ($columns as $column) {
            $value = $record->$column;
            
            if ($value && strpos($value, 'eyJpdi') !== false) {
                // KUPAS LAPISAN PERTAMA
                $decryptedOnce = EncryptionService::decrypt($value);
                
                // Cek apakah hasil kupasannya MASIH terenkripsi (Inilah si Enkripsi Ganda!)
                if ($decryptedOnce && strpos($decryptedOnce, 'eyJpdi') !== false) {
                    $updates[$column] = $decryptedOnce; // Simpan hasil kupasannya
                }
            }
        }

        if (!empty($updates)) {
            DB::table($table)->where('id', $record->id)->update($updates);
            $updatedCount++;
        }
    }
    echo "-> Selesai. $updatedCount data berhasil dikupas.\n\n";
}

echo "OPERASI SELESAI. Silakan cek tabel Siswa Anda.";
