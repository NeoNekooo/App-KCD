<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterRegional;

class LandingSlider extends Model
{
    use HasFactory, FilterRegional;

    // Tentukan nama tabel (opsional jika nama tabelnya jamak bahasa inggris, tapi bagus untuk kepastian)
    protected $table = 'landing_sliders';

    // WAJIB: Daftarkan semua kolom yang boleh diisi lewat formulir
    protected $fillable = [
        'instansi_id',
        'judul',
        'deskripsi',
        'gambar',
        'tombol_teks',
        'tombol_url',
        'urutan',
        'status',
    ];
}