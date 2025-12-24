<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimoni extends Model
{
    protected $fillable = [
        'nama',
        'status', // Jabatan/Status (Wali Murid, Alumni)
        'isi',
        'foto',
        'is_published', // Kolom baru untuk status tayang
    ];
}