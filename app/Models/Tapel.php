<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tapel extends Model
{
    protected $table = 'tapel';

    protected $fillable = [
        'kode_tapel',
        'tahun_ajaran',
        'semester',
        'is_active',
    ];
}
