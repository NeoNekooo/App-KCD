<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TugasPegawaiDetail extends Model
{
    protected $fillable = [
        'tugas_pegawai_id',
        'tugas_pokok',
        'kelas',
        'jumlah_jam',
        'jenis' // 'pembelajaran' atau 'struktural'
    ];

    public function parent()
    {
        return $this->belongsTo(TugasPegawai::class, 'tugas_pegawai_id');
    }
}
