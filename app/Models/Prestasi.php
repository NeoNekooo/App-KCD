<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prestasi extends Model
{
    protected $fillable = [
        'judul',
        'nama_pemenang',
        'tingkat',
        'tanggal',
        'deskripsi',
        'foto', // Ini jadi Cover Utama
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // Relasi ke banyak foto
    public function items()
    {
        return $this->hasMany(PrestasiItem::class);
    }
}