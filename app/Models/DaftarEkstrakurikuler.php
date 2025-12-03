<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaftarEkstrakurikuler extends Model
{
    use HasFactory;

    protected $table = 'daftar_ekstrakurikuler';

    protected $fillable = [
        'nama',
        'alias',
        'keterangan',
    ];
}
