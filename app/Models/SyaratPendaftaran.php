<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyaratPendaftaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahunPelajaran_id',
        'jalurPendaftaran_id',
        'syarat',
        'is_active'
    ];

    public function tahunPpdb()
    {
        return $this->belongsTo(TahunPelajaran::class, 'tahunPelajaran_id');
    }

    public function jalurPendaftaran()
    {
        return $this->belongsTo(JalurPendaftaran::class, 'jalurPendaftaran_id');
    }

    public function formulir()
    {
        return $this->belongsToMany(FormulirPendaftaran::class, 'formulir_syarat', 'syarat_id', 'formulir_id')
                    ->withPivot('is_checked')
                    ->withTimestamps();
    }
}
