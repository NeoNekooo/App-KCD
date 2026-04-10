<?php

/**
 * Radar Diagnostik Enkripsi v2.0
 * Buka via: https://kcd6.hexanusa.com/diag.php
 */

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

header('Content-Type: text/plain');

echo "=== RADAR DIAGNOSTIK APPKCD ===\n\n";

// 1. Cek Linkage
echo "EncryptionService: " . (class_exists(\App\Services\EncryptionService::class) ? "LINKED ✅" : "MISSING ❌") . "\n";
echo "APP_KEY: " . (env('APP_KEY') ? "LOADED ✅" : "MISSING ❌") . "\n\n";

// 2. Cek Sampel Data
$siswa = \App\Models\Siswa::whereNotNull('nisn')->first();

if ($siswa) {
    $rawNisn = $siswa->getRawOriginal('nisn');
    echo "SAMPEL SISWA: " . $siswa->nama . "\n";
    echo "RAW NISN (Database): " . $rawNisn . "\n";
    
    // Cek Panjang
    echo "Panjang Data: " . strlen($rawNisn) . " karakter\n";
    if (strlen($rawNisn) == 191) {
        echo "[PERINGATAN] Data kemungkinan TERPOTONG (191 char). Data ini korup dan tidak bisa didekripsi selamanya.\n";
    }

    // Tes Dekripsi Manual
    try {
        $decrypted = \Illuminate\Support\Facades\Crypt::decryptString(trim($rawNisn));
        echo "TES DEKRIPSI: " . $decrypted . " ✅ SUCCESS!\n";
    } catch (\Throwable $e) {
        echo "TES DEKRIPSI: FAILED ❌ (Penyebab: Kunci APP_KEY salah atau data korup)\n";
        echo "Error: " . $e->getMessage() . "\n";
    }

} else {
    echo "Peringatan: Tidak ada data siswa di database.\n";
}

echo "\nSOLUSI TERBAIK:\n";
echo "1. Jika Tes Dekripsi FAILED, hapus data siswa ini dan suruh sekolah sinkron ulang.\n";
echo "2. Pastikan APP_KEY sekolah dan KCD sama (Opsional, tapi disarankan).\n";

echo "\n--- DIAGNOSTIK SELESAI ---";
