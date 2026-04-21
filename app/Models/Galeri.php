<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterRegional;

class Galeri extends Model
{
    use FilterRegional;
    protected $fillable = [
        'instansi_id',
        'judul',
        'tanggal',
        'deskripsi',
        'foto', // Ini berfungsi sebagai Cover Album
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // Relasi: Satu album punya banyak item
    public function items()
    {
        return $this->hasMany(GaleriItem::class);
    }
}