<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelasPendaftaran extends Model
{
    
    use HasFactory;

    protected $fillable = [
        'tahunPelajaran_id',
        'kompetensiPendaftaran_id',
        'tingkat',
        'rombel',
    ];

    public function tahunPpdb()
    {
        return $this->belongsTo(TahunPelajaran::class, 'tahunPelajaran_id');
    }

    public function kompetensiPpdb()
    {
        return $this->belongsTo(KompetensiPendaftaran::class, 'kompetensiPendaftaran_id');
    }
}
