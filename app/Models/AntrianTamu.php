<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AntrianTamu extends Model
{
    protected $table = 'antrian_tamus';

    protected $fillable = [
        'nomor_antrian',
        'nama',
        'nik',
        'nomor_hp',
        'asal_instansi',
        'keperluan',
        'tujuan_pegawai_id',
        'status',
        'jumlah_panggilan',
        'waktu_panggilan',
        'waktu_selesai'
    ];

    protected $casts = [
        'waktu_panggilan' => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

    /**
     * Relasi ke Pegawai (Tujuan)
     */
    public function tujuanPegawai()
    {
        return $this->belongsTo(PegawaiKcd::class, 'tujuan_pegawai_id');
    }
}
