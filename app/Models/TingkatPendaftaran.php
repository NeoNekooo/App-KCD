<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TingkatPendaftaran extends Model
{
    // use HasFactory;

    protected $fillable = [
        'tingkat',
        'keterangan',
        'is_active'
    ];
}
