<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prestasi extends Model
{
    protected $fillable = [
        'judul',
        'nama_pemenang',
        'tingkat',
        'deskripsi',
        'foto',
        'tanggal',
    ];

    // Casting agar tanggal otomatis jadi objek Carbon (mudah diformat)
    protected $casts = [
        'tanggal' => 'date',
    ];
}