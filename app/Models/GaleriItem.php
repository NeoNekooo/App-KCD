<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GaleriItem extends Model
{
    protected $table = 'galeri_items';

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