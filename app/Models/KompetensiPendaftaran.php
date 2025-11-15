<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KompetensiPendaftaran extends Model
{
    
    use HasFactory;

    protected $fillable = [
        'tahunPelajaran_id',
        'kode',
        'kompetensi',
    ];

    public function tahunPpdb()
    {
        return $this->belongsTo(TahunPelajaran::class, 'tahunPelajaran_id');
    }

    public function kelass()
    {
        return $this->hasMany(KelasPendaftaran::class, 'kompetensiPendaftaran_id');
    }
}
