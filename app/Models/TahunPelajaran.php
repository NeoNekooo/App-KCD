<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunPelajaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun_pelajaran',
        'keterangan',
        'is_active',
    ];

    public function jalurs()
    {
        return $this->hasMany(JalurPendaftaran::class, 'tahunPelajaran_id');
    }

    public function quotaPendaftarans()
    {
        return $this->hasMany(QuotaPendaftaran::class, 'tahunPelajaran_id');
    }

    public function syarats()
    {
        return $this->hasMany(SyaratPendaftaran::class, 'tahunPelajaran_id');
    }
    
}