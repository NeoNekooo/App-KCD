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
        'nama_kepala',
        'nip_kepala',
        'alamat',
        'email',
        'telepon',
        'website',
        'logo',
        'visi',
        'misi'
    ];
}