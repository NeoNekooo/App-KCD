<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterRegional;

class TugasPegawaiKcd extends Model
{
    use HasFactory, FilterRegional;

    protected $table = 'tugas_pegawai_kcds';
    
    protected $fillable = [
        'instansi_id',
        'pegawai_kcd_id',
        'nama_tugas',
        'kategori_layanan', // <-- Ini kuncinya buat hak akses
        'no_sk',
        'deskripsi',
        'is_active'
    ];

    protected $casts = [
        'kategori_layanan' => 'array',
    ];

    // Relasi ke data Pegawai
    public function pegawai()
    {
        return $this->belongsTo(PegawaiKcd::class, 'pegawai_kcd_id');
    }
}