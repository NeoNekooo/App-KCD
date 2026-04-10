<?php

/**
 * Mesin Pembersih Data Korup (The Great Wipe) v1.0
 * Jalankan via: https://kcd6.hexanusa.com/wipe.php
 */

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

header('Content-Type: text/plain');

echo "=== OPERASI PEMBERSIHAN DATA KORUP (WIPE) ===\n\n";

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$tables = ['siswas', 'gtks']; // Tabel utama yang bermasalah enkripsinya

foreach ($tables as $table) {
    if (Schema::hasTable($table)) {
        echo "Mengosongkan tabel $table... ";
        DB::table($table)->truncate();
        echo "BERSIH ✅\n";
    }
}

echo "\n--- PEMBERSIHAN SELESAI ---\n";
echo "Langkah selanjutnya:\n";
echo "1. Pastikan APP_KEY di KCD dan Sekolah SUDAH SAMA.\n";
echo "2. Suruh sekolah klik tombol 'SINKRON' kembali.\n";
echo "3. Cek halaman Data Siswa. Pasti muncul normal! ✨";
