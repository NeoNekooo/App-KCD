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
     * Jika data SUDAH berupa plain text (belum terenkripsi), kembalikan aslinya.
     * Jika data terenkripsi tapi RUSAK, kembalikan null agar tidak crash.
     */
    public static function decryptValue($value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        // Cek apakah format string terlihat seperti JSON Laravel Encrypter (dimulai dengan eyJpdi...)
        if (!is_string($value) || !str_starts_with($value, 'eyJpdiI')) {
            return $value;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Throwable $e) {
            // Data terenkripsi tapi gagal didekripsi (misal: APP_KEY beda atau data terpotong)
            // Balikan null saja daripada bikin crash sistem (ParseError pada Date)
            return null;
        }
    }

    /**
     * Cek apakah kolom tertentu pada tabel tertentu harus dienkripsi.
     * Dibuat robust untuk menangani table prefix dan perbedaan pluralisasi.
     */
    public static function shouldEncrypt(string $tableName, string $columnName): bool
    {
        $mapping = static::getEncryptedColumns();
        $tableName = strtolower($tableName);
        $columnName = strtolower($columnName);

        foreach ($mapping as $mappedTable => $columns) {
            $mappedTable = strtolower($mappedTable);
            
            // Cek apakah nama tabel cocok:
            // 1. Cocok persis (siswas === siswas)
            // 2. Berakhiran (prefix_siswas berakhiran siswas)
            // 3. Singular (siswas berakhiran siswa)
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
     * Overriding getAttributeValue to automatically decrypt values 
     * BEFORE Laravel tries to cast them (e.g. to Date)
     */
    public function getAttributeValue($key)
    {
        // Ambil nilai mentah dari internal attributes array (sebelum casting)
        $value = $this->getAttributeFromArray($key);

        // Jika kolom ini harus dienkripsi, dekripsi sekarang (sebelum Casting/Accessor)
        if (is_string($value) && $this->isEncryptable($key)) {
            $value = static::decryptValue($value);
        }

        // Lanjutkan ke logika standar Laravel (Casting & Accessors)
        return $this->transformModelValue($key, $value);
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
