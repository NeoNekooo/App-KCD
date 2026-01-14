<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TugasPegawaiKcd extends Model
{
    use HasFactory;

    protected $table = 'tugas_pegawai_kcds';
    
    protected $fillable = [
        'pegawai_kcd_id',
        'nama_tugas',
        'kategori_layanan', // <-- Ini kuncinya buat hak akses
        'no_sk',
        'deskripsi',
        'is_active'
    ];

    // Relasi ke data Pegawai
    public function pegawai()
    {
        return $this->belongsTo(PegawaiKcd::class, 'pegawai_kcd_id');
    }
}