<?php

namespace App\Traits;

use Illuminate\Support\Facades\Crypt;

/**
 * Trait EncryptsSensitiveData v6-ULTIMATE
 * 
 * Sistem intersepsi triple-layer untuk menjamin data PII terdekripsi 
 * sebelum menyentuh sistem casting Laravel (terutama Date/Carbon).
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
        if ($value === null || $value === '' || !is_string($value)) {
            return $value;
        }

        try {
            return Crypt::encryptString((string) $value);
        } catch (\Throwable $e) {
            return (string) $value;
        }
    }

    /**
     * Mendekripsi sebuah nilai (v6-ULTIMATE).
     */
    public static function decryptValue($value): ?string
    {
        if ($value === null || $value === '' || !is_string($value)) {
            return $value;
        }

        // Cek pola Base64 Laravel Encryption secara longgar
        if (!str_contains($value, 'eyJpdi')) {
            return $value;
        }

        try {
            return Crypt::decryptString(trim($value));
        } catch (\Throwable $e) {
            // Jika gagal didekripsi (korup/key beda), WAJIB kembalikan NULL
            // agar tidak merusak sistem casting Date Laravel.
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
     * LAYER 1: Intersepsi pada pengambilan nilai atribut.
     */
    public function getAttributeValue($key)
    {
        $value = $this->getAttributeFromArray($key);

        if (is_string($value) && str_contains($value, 'eyJpdi')) {
            $value = static::decryptValue($value);
        }

        return $this->transformModelValue($key, $value);
    }

    /**
     * LAYER 2: Intersepsi tepat sebelum casting atribut dijalankan.
     */
    protected function castAttribute($key, $value)
    {
        if (is_string($value) && str_contains($value, 'eyJpdi')) {
            $value = static::decryptValue($value);
        }

        return parent::castAttribute($key, $value);
    }

    /**
     * LAYER 3: Intersepsi pada fungsi asDate (Benteng terakhir sebelum Carbon).
     */
    protected function asDate($value)
    {
        if (is_string($value) && str_contains($value, 'eyJpdi')) {
            $value = static::decryptValue($value);
        }

        return parent::asDate($value);
    }

    /**
     * Cek apakah atribut bisa dienkripsi (Mutators).
     */
    protected function isEncryptable($key): bool
    {
        return static::shouldEncrypt($this->getTable(), $key);
    }
}
