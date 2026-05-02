<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;

/**
 * Service EncryptionService v2.0 - FINAL STABLE
 * 
 * Pusat logika enkripsi/dekripsi.
 * Menggunakan daftar kolom eksklusif dari Riri.
 */
class EncryptionService
{
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
            'penggunas' => [
                'google2fa_secret',
            ],
        ];
    }

    public static function shouldEncrypt(string $tableName, string $columnName): bool
    {
        $mapping = static::getEncryptedColumns();
        $tableName = strtolower($tableName);
        $columnName = strtolower($columnName);

        foreach ($mapping as $mappedTable => $columns) {
            $mappedTable = strtolower($mappedTable);
            $isSiswa = ($mappedTable === 'siswas' && (str_contains($tableName, 'peserta_didik') || str_contains($tableName, 'siswa')));
            $isGtk = ($mappedTable === 'gtks' && (str_contains($tableName, 'gtk') || str_contains($tableName, 'guru') || str_contains($tableName, 'tendik')));

            if ($tableName === $mappedTable || 
                str_ends_with($tableName, $mappedTable) ||
                str_ends_with($tableName, rtrim($mappedTable, 's')) ||
                $isSiswa || $isGtk
            ) {
                return in_array($columnName, $columns);
            }
        }

        return false;
    }

    public static function encrypt($value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        $value = (string) $value; // Paksa jadi string dulu biar bisa dienkrip

        // Jangan enkripsi jika sudah terenkripsi
        if (strpos($value, 'eyJpdi') !== false) {
            return $value;
        }

        try {
            return Crypt::encryptString($value);
        } catch (\Throwable $e) {
            return $value;
        }
    }

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
            \Illuminate\Support\Facades\Log::critical("DECRYPT_FAIL: " . $e->getMessage() . " | APP_KEY_CONFIG: " . substr(config('app.key'), 0, 15) . "...");
            return null;
        }
    }
}
