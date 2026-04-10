<?php

namespace App\Traits;

use App\Services\EncryptionService;

/**
 * Trait EncryptsSensitiveData v7.0
 * 
 * Versi yang sudah terintegrasi dengan EncryptionService.
 * Sangat stabil dan mencegah error redundansi.
 */
trait EncryptsSensitiveData
{
    public function getAttributeValue($key)
    {
        $value = $this->getAttributeFromArray($key);

        if (is_string($value) && strpos($value, 'eyJpdi') !== false) {
            $value = EncryptionService::decrypt($value);
        }

        return $this->transformModelValue($key, $value);
    }

    protected function castAttribute($key, $value)
    {
        if (is_string($value) && strpos($value, 'eyJpdi') !== false) {
            $value = EncryptionService::decrypt($value);
        }

        return parent::castAttribute($key, $value);
    }

    protected function asDate($value)
    {
        if (is_string($value) && strpos($value, 'eyJpdi') !== false) {
            $value = EncryptionService::decrypt($value);
        }

        return parent::asDate($value);
    }

    protected function asDateTime($value)
    {
        if (is_string($value) && strpos($value, 'eyJpdi') !== false) {
            $value = EncryptionService::decrypt($value);
        }

        return parent::asDateTime($value);
    }

    public static function encryptValue($value)
    {
        return EncryptionService::encrypt($value);
    }

    public static function decryptValue($value)
    {
        return EncryptionService::decrypt($value);
    }
}
