<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cadisdik extends Model
{
    use HasFactory;

    protected $table = 'cadisdiks';

    // Karena menggunakan UUID dari SIAKAD sebagai primary key
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'nama',
        'kode',
        'keterangan',
    ];

    /**
     * Relasi ke Sekolah (Satu Wilayah memiliki banyak Sekolah)
     */
    public function sekolahs()
    {
        return $this->hasMany(Sekolah::class);
    }

    /**
     * Relasi ke Pegawai (Satu Wilayah memiliki banyak Pegawai)
     */
    public function pegawais()
    {
        return $this->hasMany(PegawaiKcd::class);
    }

    /**
     * ID Cantik buat URL (Contoh: VI-a1b2)
     * a1b2 diambil dari hash UUID biar gak gampang ditebak.
     */
    public function getShortSlugAttribute()
    {
        $hash = substr(md5($this->id), 0, 4);
        return ($this->kode ?? 'KCD') . '-' . $hash;
    }
}
