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

    public function calonSiswas()
    {
        return $this->belongsToMany(CalonSiswa::class, 'calon_siswa_syarat', 'syarat_id', 'calon_siswa_id')
                    ->withPivot('is_checked')
                    ->withTimestamps();
    }


}
