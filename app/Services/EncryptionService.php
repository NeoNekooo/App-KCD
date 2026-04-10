<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;

/**
 * Service EncryptionService v1.0
 * 
 * Pusat logika enkripsi/dekripsi untuk App-KCD.
 * Digunakan oleh Trait (untuk Model) dan Controller (untuk Sinkronisasi).
 */
class EncryptionService
{
    /**
     * Mapping kolom yang harus dienkripsi.
     */
    public static function getEncryptedColumns(): array
    {
        return [
            'siswas' => [
                'nisn', 'nik', 'no_kk', 'nik_ayah', 'nik_ibu', 'nik_wali',
                'tanggal_lahir', 'nomor_telepon_rumah', 'nomor_telepon_seluler',
                'no_wa', 'no_wa_ayah', 'no_wa_ibu', 'no_wa_wali',
            ],
            'gtks' => [
                'nik', 'nik_ibu_kandung', 'no_hp', 'no_wa', 'no_telepon_rumah',
            ],
        ];
    }

    /**
     * Cek apakah kolom tertentu harus dienkripsi.
     */
    public static function shouldEncrypt(string $tableName, string $columnName): bool
    {
        $mapping = static::getEncryptedColumns();
        $tableName = strtolower($tableName);
        $columnName = strtolower($columnName);

        foreach ($mapping as $mappedTable => $columns) {
            $mappedTable = strtolower($mappedTable);
            if ($tableName === $mappedTable || 
                str_ends_with($tableName, $mappedTable) ||
                str_ends_with($tableName, rtrim($mappedTable, 's'))
            ) {
                return in_array($columnName, $columns);
            }
        }
        return false;
    }

    /**
     * Mengenkripsi nilai.
     */
    public static function encrypt($value): ?string
    {
        if ($value === null || $value === '' || !is_string($value)) {
            return $value;
        }

        // 🔥 CEGAH ENKRIPSI GANDA 🔥
        // Jika data sudah diawali eyJpdi, artinya sudah terenkripsi. JANGAN dihajar lagi!
        if (strpos($value, 'eyJpdi') !== false) {
            return $value;
        }

        try {
            return Crypt::encryptString((string) $value);
        } catch (\Throwable $e) {
            return (string) $value;
        }
    }

    /**
     * Mendekripsi nilai.
     */
    public static function decrypt($value): ?string
    {
        if ($value === null || $value === '' || !is_string($value)) {
            return $value;
        }

        if (strpos($value, 'eyJpdi') === false) {
            return $value;
        }

        try {
            return Crypt::decryptString(trim($value));
        } catch (\Throwable $e) {
            // 🔥 CATAT ERROR KE LOG LARAVEL 🔥
            \Illuminate\Support\Facades\Log::critical("DECRYPT_FAIL: " . $e->getMessage() . " | APP_KEY: " . substr(env('APP_KEY'), 0, 15) . "...");
            return null;
        }
    }
}
