<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Galeri extends Model
{
    protected $fillable = [
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