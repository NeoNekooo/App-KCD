<?php

/**
 * RADAR DIAGNOSTIK DATABASE v2.0
 */

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

header('Content-Type: text/plain');

echo "=== RADAR DIAGNOSTIK DATABASE KCD ===\n\n";

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$tables = ['siswas', 'gtks'];

foreach ($tables as $table) {
    if (Schema::hasTable($table)) {
        echo "TABEL: $table\n";
        echo "---------------------------------\n";
        
        $columns = DB::select("SHOW COLUMNS FROM `$table` WHERE Field IN ('nik', 'nisn', 'tanggal_lahir', 'no_wa')");
        
        foreach ($columns as $col) {
            echo "Kolom: {$col->Field} | Tipe: {$col->Type} | Null: {$col->Null}\n";
        }
        
        // Cek contoh data mentah (Raw)
        $sample = DB::table($table)->whereNotNull('nik')->first();
        if ($sample) {
            $val = $sample->nik;
            echo "Contoh Raw NIK: " . substr($val, 0, 50) . "...\n";
            echo "Panjang Raw NIK: " . strlen($val) . " karakter\n";
        } else {
            echo "Tabel kosong atau NIK null.\n";
        }
        echo "\n";
    }
}

echo "INFO: Enkripsi Laravel butuh minimal tipe TEXT atau VARCHAR(255+).\n";
echo "Jika tipe masih VARCHAR(20) atau sejenisnya, DATA PASTI TERPOTONG!\n";
