<?php

namespace App\Traits;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

/**
 * Trait EncryptsSensitiveData v5-Ultra-Nuclear
 * 
 * Menyediakan mapping kolom sensitif dan penanganan otomatis enkripsi/dekripsi.
 * Versi ini dipersenjatai dengan pengamanan ekstra agar tidak menyebabkan 
 * Error 500 pada casting tipe data Date di Laravel.
 */
trait EncryptsSensitiveData
{
    /**
     * Mapping tabel => kolom-kolom yang harus dienkripsi.
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
     * Mengenkripsi sebuah nilai secara aman.
     */
    public static function encryptValue($value): ?string
    {
        if ($value === null || $value === '' || is_array($value) || is_object($value)) {
            return $value;
        }

        try {
            return Crypt::encryptString((string) $value);
        } catch (\Throwable $e) {
            return (string) $value;
        }
    }

    /**
     * Mendekripsi sebuah nilai (v5-Ultra-Nuclear).
     * Jika gagal atau data korup, mengembalikan NULL untuk mencegah crash pada Eloquent Casting.
     */
    public static function decryptValue($value): ?string
    {
        if ($value === null || $value === '' || !is_string($value)) {
            return $value;
        }

        // Deteksi pola enkripsi Laravel (Base64 dari JSON yang dimulai dengan {"iv":)
        // Pola umum: eyJpdiI...
        if (!str_contains($value, 'eyJpdiI')) {
            return $value;
        }

        try {
            return Crypt::decryptString(trim($value));
        } catch (\Throwable $e) {
            // Jika gagal (misal data terpotong/APP_KEY beda), balikan NULL.
            // PENTING: Jangan balikan string asli terenkripsi karena akan merusak Carbon::parse()
            return null;
        }
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
     * Overriding getAttributeValue (Jantung dari Trait v5).
     * Mencegah data terenkripsi lolos ke Eloquent Casting (seperti Date).
     */
    public function getAttributeValue($key)
    {
        // 1. Ambil nilai mentah dari model attributes
        $value = $this->getAttributeFromArray($key);

        // 2. Deteksi data terenkripsi secara agresif
        if (is_string($value) && str_contains($value, 'eyJpdiI')) {
            // Debug Marker V5-NUCLEAR
            $value = static::decryptValue($value);
        }

        // 3. Teruskan ke transformModelValue (Casting, Accessors, dll)
        // Jika $value sekarang null (karena gagal dekripsi), Carbon akan aman.
        return $this->transformModelValue($key, $value);
    }

    /**
     * Cek apakah atribut bisa dienkripsi.
     */
    protected function isEncryptable($key): bool
    {
        return static::shouldEncrypt($this->getTable(), $key);
    }
}
