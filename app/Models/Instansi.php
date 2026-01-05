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
        'nama_brand',   // <-- KCD ENAM
        'nama_kepala',
        'nip_kepala',
        'alamat',       // <-- Alamat Lengkap (termasuk Kab/Kota)
        'peta',         // <-- Embed Maps
        'email',
        'telepon',
        'website',
        'social_media', // <-- JSON Sosmed
        'logo',
        'visi',
        'misi'
    ];

    protected $casts = [
        'social_media' => 'array',
    ];
}