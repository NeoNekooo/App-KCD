<?php

namespace App\Models;

use App\Traits\FilterRegional;

class AntrianTamu extends Model
{
    use FilterRegional;

    protected $table = 'antrian_tamus';

    protected $fillable = [
        'instansi_id',
        'nomor_antrian',
        'nama',
        'npsn',
        'nomor_hp',
        'asal_instansi',
        'jabatan_pengunjung',
        'keperluan',
        'tujuan_pegawai_id',
        'status',
        'print_requested',
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
