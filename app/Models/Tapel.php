<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tapel extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database
     */
    protected $table = 'tapel';

    /**
     * Kolom yang boleh diisi
     */
    protected $fillable = [
        'kode_tapel',
        'tahun_ajaran',
        'semester',
        'is_active',
    ];
}