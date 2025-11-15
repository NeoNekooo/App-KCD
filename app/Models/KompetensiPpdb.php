<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KompetensiPpdb extends Model
{
    
    use HasFactory;

    protected $fillable = [
        'judul_kompetensi',
        'deskripsi_kompetensi',
        'icon',
        'code_kompetensi',
        'nama_kompetensi',
        'deskripsi_jurusan'
    ];


}
