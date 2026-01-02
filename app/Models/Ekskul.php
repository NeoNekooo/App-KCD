<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ekskul extends Model
{
    protected $table = 'ekskul';

    protected $fillable = [
        'nama_ekskul',
        'pembina',
        'jadwal',
        'tempat', // BARU
        'status', // BARU
        'foto',
    ];
}