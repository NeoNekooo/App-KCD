<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterRegional;

class GaleriItem extends Model
{
    use FilterRegional;

    protected $fillable = [
        'instansi_id',
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