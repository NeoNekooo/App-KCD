<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeunggulanPpdb extends Model
{
    
    use HasFactory;

    protected $fillable = [
        'judul_keunggulan',
        'deskripsi_keunggulan',
        'icon',
        'judul_item',
        'deskripsi_item'
    ];


}
