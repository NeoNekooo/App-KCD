<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JabatanKcd extends Model
{
    use HasFactory;

    protected $table = 'jabatan_kcd';

    protected $fillable = [
        'nama',
        'role',
    ];
}
