<?php

namespace App\Traits;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

/**
 * Trait EncryptsSensitiveData
 * 
 * Menyediakan mapping kolom sensitif per tabel dan helper method
 * untuk enkripsi/dekripsi data PII (Personal Identifiable Information).
 */
trait EncryptsSensitiveData
{
    /**
     * Mapping tabel => kolom-kolom yang harus dienkripsi.
     * Kolom 'nama' TIDAK dienkripsi demi mempertahankan fitur search & sorting.
     */
    public static function getEncryptedColumns(): array
    {
        return [
            'siswas' => [
                'nisn',
                'nik',
                'no_kk',
                'nik_ayah',
                'nik_ibu',
                'nik_wali',
                'tanggal_lahir',
                'nomor_telepon_rumah',
                'nomor_telepon_seluler',
                'no_wa',
                'no_wa_ayah',
                'no_wa_ibu',
                'no_wa_wali',
            ],
            'gtks' => [
                'nik',
                'nik_ibu_kandung',
                'no_hp',
                'no_wa',
                'no_telepon_rumah',
            ],
        ];
    }

    /**
     * Mengenkripsi sebuah nilai. Mengembalikan null jika nilai kosong.
     */
    public static function encryptValue($value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        return Crypt::encryptString((string) $value);
    }

    /**
     * Mendekripsi sebuah nilai dengan error handling.
     * Jika data SUDAH berupa plain text (belum terenkripsi / migrasi lama),
     * maka kembalikan nilai aslinya tanpa error.
     */
    public static function decryptValue($value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        try {
            return Crypt::decryptString($value);
        } catch (DecryptException $e) {
            // Data belum terenkripsi (legacy/plain text), kembalikan apa adanya
            return $value;
        }
    }

    /**
     * Cek apakah kolom tertentu pada tabel tertentu harus dienkripsi.
     */
    public static function shouldEncrypt(string $tableName, string $columnName): bool
    {
        $mapping = static::getEncryptedColumns();

        // Support singular/plural names as we often use both in sync controllers
        $normalizedTable = (str_ends_with($tableName, 's')) ? $tableName : $tableName . 's';
        
        if (!isset($mapping[$normalizedTable])) {
            return false;
        }

        return in_array(strtolower($columnName), $mapping[$normalizedTable]);
    }

    /**
     * Overriding getAttributeValue to automatically decrypt values 
     * BEFORE Laravel tries to cast them (e.g. to Date)
     */
    public function getAttributeValue($key)
    {
        $value = parent::getAttributeValue($key);

        if (is_string($value) && $this->isEncryptable($key)) {
            return static::decryptValue($value);
        }

        return $value;
    }

    /**
     * Check if an attribute is encryptable
     */
    protected function isEncryptable($key): bool
    {
        $tableName = $this->getTable();
        return static::shouldEncrypt($tableName, $key);
    }
}
