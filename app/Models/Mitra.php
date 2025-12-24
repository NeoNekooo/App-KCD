<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mitra extends Model
{
    protected $fillable = [
        'nama_mitra',
        'logo',
        'bidang_kerjasama',
    ];
}