<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PkksPenilaian extends Model
{
    use HasFactory, \App\Traits\FilterRegional;

    protected $fillable = [
        'instansi_id',
        'pkks_instrumen_id',
        'sekolah_id',
        'kepala_sekolah_id',
        'penilai_id',
        'penilai_type',
        'skor_total',
        'catatan'
    ];

    public function instrumen()
    {
        return $this->belongsTo(PkksInstrumen::class, 'pkks_instrumen_id');
    }

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id', 'sekolah_id');
    }

    public function penilai()
    {
        return $this->morphTo();
    }
}
