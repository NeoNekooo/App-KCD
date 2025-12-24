<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    protected $fillable = [
        'nama_jurusan',
        'singkatan',
        'kepala_jurusan',
        'deskripsi', // <--- Tambahkan ini
        'gambar',
    ];
}