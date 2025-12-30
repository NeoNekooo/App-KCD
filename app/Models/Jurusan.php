<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    /**
     * Deprecated stub: Jurusan master table is no longer used.
     * If you see this exception during runtime it means some code still expects
     * the Jurusan model/table to exist. Update that code to read from rombels
     * (`jurusan_id_str`) instead.
     */
    public static function booted()
    {
        throw new \RuntimeException('Jurusan model is deprecated and its table has been removed. Update code to use rombels.jurusan_id_str');
    }
}
