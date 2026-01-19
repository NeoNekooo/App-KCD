<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    // Izinkan semua kolom diisi
    protected $guarded = ['id'];

    // PENTING: Cast kolom jadi tipe data yang sesuai
    protected $casts = [
        'params' => 'array',       // JSON -> Array
        'is_active' => 'boolean',  // 1/0 -> true/false
        'is_header' => 'boolean',  // 1/0 -> true/false
    ];

    // Relasi ke Submenu (Anak)
    public function children()
    {
        // 🔥 PERBAIKAN UTAMA: Ganti 'order' jadi 'urutan'
        return $this->hasMany(Menu::class, 'parent_id')
                    ->where('is_active', true) // Sekalian filter yang aktif aja
                    ->orderBy('urutan', 'asc');
    }

    // Relasi ke Parent (Induk)
    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }
}