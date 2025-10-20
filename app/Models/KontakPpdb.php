<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KontakPpdb extends Model
{
    use HasFactory;

    // Kolom yang bisa diisi secara mass-assignment
    protected $fillable = [
        'nomer_ppdb',
        'jam_pelayanan',
        'email',
        'facebook',
        'instagram',
        'youtube',
        'alamat',
    ];
}
