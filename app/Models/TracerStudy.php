<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TracerStudy extends Model
{
    use HasFactory;

    protected $table = 'tracer_studies';

    protected $fillable = [
        'siswa_id',
        'kegiatan_setelah_lulus',
        'nama_instansi',
        'jabatan_posisi',
        'tahun_lulus',
        'kesan_pesan'
    ];

    /**
     * Relasi balik ke Siswa
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id', 'id');
    }
}