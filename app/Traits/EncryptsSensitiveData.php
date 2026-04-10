<?php

namespace App\Traits;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

/**
 * Trait EncryptsSensitiveData v6.3-GLOBAL-INTERCEPTOR
 * 
 * Melakukan intersepsi pada level getAttributeValue untuk menjamin 
 * dekripsi dilakukan sebelum sistem casting Laravel berjalan.
 */
trait EncryptsSensitiveData
{
    /**
     * MAPPING KOLOM (Tetap dipertahankan untuk referensi)
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
     * CORE DECRYPTER: Selalu NULL jika gagal dekripsi pada data terenkripsi.
     */
    public static function decryptValue($value): ?string
    {
        if ($value === null || $value === '' || !is_string($value)) {
            return $value;
        }

        // Cek pola Base64 Laravel Encryption
        if (strpos($value, 'eyJpdi') === false) {
            return $value;
        }

        try {
            $decrypted = Crypt::decryptString(trim($value));
            return (string) $decrypted;
        } catch (\Throwable $e) {
            return null; // Force null agar tidak tampil kode ghaib
        }
    }

    /**
     * MASTER OVERRIDE: getAttributeValue
     * Inilah gerbang utama Laravel mengambil data dari database.
     */
    public function getAttributeValue($key)
    {
        // Ambil nilai mentah dari model
        $value = $this->getAttributeFromArray($key);

        // Langsung dekripsi JIKA terindikasi enkripsi
        if (is_string($value) && strpos($value, 'eyJpdi') !== false) {
            $value = static::decryptValue($value);
        }

        // Jalankan casting bawaan Laravel (Date, Array, dll) pada nilai yang SUDAH didekripsi
        return $this->transformModelValue($key, $value);
    }

    /**
     * BENTENG TERAKHIR: asDate
     */
    protected function asDate($value)
    {
        if (is_string($value) && strpos($value, 'eyJpdi') !== false) {
            $value = static::decryptValue($value);
        }

        return parent::asDate($value);
    }

    /**
     * BENTENG TERAKHIR: asDateTime
     */
    protected function asDateTime($value)
    {
        if (is_string($value) && strpos($value, 'eyJpdi') !== false) {
            $value = static::decryptValue($value);
        }

        return parent::asDateTime($value);
    }

    /**
     * Logic enkripsi (Mutator)
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
}
