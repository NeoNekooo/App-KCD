<?php

/**
 * RADAR DIAGNOSTIK KUNCI v3.1
 */

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

header('Content-Type: text/plain');

echo "=== RADAR DIAGNOSTIK KUNCI KCD v3.1 ===\n\n";

use Illuminate\Support\Facades\DB;
use App\Services\EncryptionService;

// 1. CEK KUNCI DARI CONFIG (PASTI STABIL)
$currentKey = config('app.key');
$cipher = config('app.cipher');

echo "APP_KEY AKTIF (Config): " . substr($currentKey, 0, 15) . "...\n";
echo "CIPHER: $cipher\n";
echo "HASH KUNCI: " . md5($currentKey) . "\n\n";

// 2. TES SIKLUS ENKRIPSI
$testPlain = "TEST_RIRI_123";
$testEnc = EncryptionService::encrypt($testPlain);
$testDec = EncryptionService::decrypt($testEnc);

echo "TES SIKLUS INTERNAL:\n";
echo "Plain: $testPlain\n";
echo "Hasil Buka Gembok: " . ($testDec === $testPlain ? "✅ SUKSES (Kunci Sinkron)" : "❌ GAGAL (Sistem Enkripsi Error)") . "\n\n";

// 3. TES BUKA DATA DATABASE
$tables = ['siswas', 'gtks'];

foreach ($tables as $table) {
    echo "MEMERIKSA DATA TABEL: $table\n";
    $sample = DB::table($table)->whereNotNull('nik')->first();
    
    if ($sample) {
        $rawNik = $sample->nik;
        echo "Raw NIK di DB: " . substr($rawNik, 0, 40) . "...\n";
        
        $decrypted = EncryptionService::decrypt($rawNik);
        
        if ($decrypted) {
            echo "HASIL DEKRIPSI: $decrypted ✅ BERHASIL!\n";
        } else {
            echo "HASIL DEKRIPSI: ❌ GAGAL (MAC Invalid/Kunci Beda)\n";
            echo "KESIMPULAN: Data di DB ini dikunci pake APP_KEY lama Riri.\n";
            echo "Wajib jalankan wipe.php terus SINKRON ULANG!\n";
        }
    } else {
        echo "Tidak ada data untuk dites.\n";
    }
    echo "---------------------------------\n";
}

echo "\nSARAN ARCHER:\n";
echo "Jangan lupa jalankan: php artisan config:clear di server Riri!\n";
