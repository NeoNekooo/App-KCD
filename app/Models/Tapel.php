<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tapel extends Model
{
    use HasFactory;

    // Nama tabel eksplisit (karena tidak menggunakan plural 'tapels')
    protected $table = 'tapel';

    // Kolom yang diizinkan untuk mass assignment
    protected $fillable = [
        'kode_tapel',
        'tahun_ajaran',
        'semester',
        'is_active',
    ];

    // Ubah is_active jadi boolean (true/false) saat diambil dari DB
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Fungsi Helper untuk mengambil Tapel yang sedang Aktif.
     * Dipanggil di Controller dengan: Tapel::getAktif();
     */
    public static function getAktif()
    {
        // Mengambil satu data yang is_active = 1 (true)
        return self::where('is_active', 1)->first();
    }
    
    /**
     * Accessor untuk menampilkan nama lengkap Tapel
     * Contoh output: "2024/2025 - Ganjil"
     * Dipanggil di blade dengan: {{ $tapel->nama_lengkap }}
     */
    public function getNamaLengkapAttribute()
    {
        return "{$this->tahun_ajaran} - {$this->semester}";
    }
}