<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingSlider extends Model
{
    use HasFactory;

    // Tentukan nama tabel (opsional jika nama tabelnya jamak bahasa inggris, tapi bagus untuk kepastian)
    protected $table = 'landing_sliders';

    // WAJIB: Daftarkan semua kolom yang boleh diisi lewat formulir
    protected $fillable = [
        'judul',
        'deskripsi',
        'gambar',
        'tombol_teks',
        'tombol_url',
        'urutan',
        'status',
    ];
}