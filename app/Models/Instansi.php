<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instansi extends Model
{
    use HasFactory;

    protected $table = 'instansis';
    
    protected $fillable = [
        'nama_instansi',
        'nama_brand',
        'nama_kepala',
        'nip_kepala',
        'alamat',
        'peta',
        'email',
        'telepon',
        'website',
        'social_media',
        'logo',
        'visi',
        'misi'
    ];

    // INI KUNCINYA: Otomatis convert Array PHP <-> JSON Database
    protected $casts = [
        'social_media' => 'array',
    ];
}