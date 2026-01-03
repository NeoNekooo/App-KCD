<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    protected $table = 'majors';
    protected $fillable = [
        'nama_jurusan',
        'singkatan',
        'kepala_jurusan',
        'deskripsi', // <--- Tambahkan ini
        'gambar',
    ];
}