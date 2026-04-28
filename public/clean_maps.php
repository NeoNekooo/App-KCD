<?php

/**
 * Script Pembersih Metadata (.map files)
 * Jalankan sekali untuk membersihkan sisa-sisa jejak webpack di server.
 */

function cleanMapFiles($dir) {
    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        $path = $dir . DIRECTORY_SEPARATOR . $file;
        if (is_dir($path)) {
            cleanMapFiles($path);
        } elseif (pathinfo($path, PATHINFO_EXTENSION) === 'map') {
            if (unlink($path)) {
                echo "Dihapus: $path <br>";
            } else {
                echo "Gagal menghapus: $path <br>";
            }
        }
    }
}

echo "<h2>Operasi Pembersihan Jejak Ghaib Dimulai...</h2>";
cleanMapFiles(__DIR__);
echo "<h2>Selesai! Sekarang cek F12 kamu (jangan lupa clear cache).</h2>";

// Hapus diri sendiri agar tidak disalahgunakan orang lain
// unlink(__FILE__);
