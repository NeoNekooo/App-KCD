<?php

/**
 * Script Pembersihan OPcache v1.0
 * Jalankan via browser: https://domain.com/reset.php
 */

header('Content-Type: text/plain');

echo "--- PROSES PEMBERSIHAN MEMORI SERVER ---\n\n";

if (function_exists('opcache_reset')) {
    if (opcache_reset()) {
        echo "[SUKSES] OPcache berhasil di-reset!\n";
    } else {
        echo "[GAGAL] Gagal me-reset OPcache.\n";
    }
} else {
    echo "[PERINGATAN] Fungsi opcache_reset() tidak diaktifkan di server ini.\n";
}

if (function_exists('apcu_clear_cache')) {
    apcu_clear_cache();
    echo "[SUKSES] APCu cache dibersihkan!\n";
}

echo "\n--- PEMBERSIHAN CACHE LARAVEL ---\n";
// Karena tidak bisa panggil Artisan langsung dari sini dengan mudah, 
// kita hanya fokus ke OPcache yang paling sering bikin nyangkut.

echo "\nSELESAI. Silakan coba buka halaman Data Siswa lagi.";
