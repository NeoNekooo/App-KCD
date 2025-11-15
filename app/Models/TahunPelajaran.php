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
        'active',
    ];

    /**
     * Method untuk memastikan hanya satu Tahun Pelajaran yang aktif.
     * @param int $id ID Tahun Pelajaran yang akan diaktifkan
     */
    public static function setActive($id)
    {
        // 1. Non-aktifkan semua Tahun Pelajaran
        self::where('is_active', true)->update(['is_active' => false]);

        // 2. Aktifkan Tahun Pelajaran yang dipilih
        self::where('id', $id)->update(['is_active' => true]);
    }

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