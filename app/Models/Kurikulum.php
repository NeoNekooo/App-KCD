<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kurikulum extends Model
{
    /**
     * Deprecated stub: Kurikulum master table is no longer used.
     * If you see this exception during runtime it means some code still expects
     * the Kurikulum model/table to exist. Update that code to read from rombels
     * (`kurikulum_id_str`) instead.
     */
    public static function booted()
    {
        throw new \RuntimeException('Kurikulum model is deprecated and its table has been removed. Update code to use rombels.kurikulum_id_str');
    }
}
