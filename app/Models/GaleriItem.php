<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GaleriItem extends Model
{
    protected $fillable = [
        'galeri_id',
        'file',
        'jenis', // 'foto' atau 'video'
        'caption'
    ];

    public function galeri()
    {
        return $this->belongsTo(Galeri::class);
    }
}