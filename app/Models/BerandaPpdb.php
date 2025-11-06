<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BerandaPpdb extends Model
{
    
    use HasFactory;

    protected $fillable = [
        'slogan_utama',
        'deskripsi_singkat',
        'point_keunggulan_1'
    ];

}
