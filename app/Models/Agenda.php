<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    protected $fillable = [
        'judul',
        'tanggal_mulai',
        'tanggal_selesai',
        'kategori',
        'deskripsi',
    ];

    // Agar otomatis jadi format tanggal (Carbon)
    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];
}