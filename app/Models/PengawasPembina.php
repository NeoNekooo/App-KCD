<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengawasPembina extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Relasi ke User (Pengawas)
     */
    public function pengawas()
    {
        return $this->belongsTo(User::class, 'pengawas_id');
    }

    /**
     * Relasi ke Sekolah
     */
    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id', 'sekolah_id');
    }
}
