<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterRegional;

class Sekolah extends Model
{
    use HasFactory, FilterRegional;

    protected $table = 'sekolahs';

    protected $fillable = [
        'instansi_id', 'cadisdik_id', 'sekolah_id', 'nama', 'nss', 'npsn', 
        'kode_sekolah', 'bentuk_pendidikan_id', 'bentuk_pendidikan_id_str', 
        'status_sekolah', 'status_sekolah_str', 'alamat_jalan', 'rt', 'rw', 
        'kode_wilayah', 'kode_pos', 'nomor_telepon', 'nomor_fax', 'email', 
        'website', 'is_sks', 'lintang', 'bujur', 'dusun', 'desa_kelurahan', 
        'kecamatan', 'kabupaten_kota', 'provinsi', 'logo', 'background_kartu', 
        'peta', 'social_media'
    ];

    protected $casts = [
        'social_media' => 'array',
    ];

    public function pengguna()
    {
        return $this->hasMany(Pengguna::class, 'sekolah_id', 'sekolah_id');
    }

    /**
     * Accessor Logo URL (SUPER CLEAN & PINTER)
     */
    public function getLogoUrlAttribute()
    {
        if (!$this->logo) {
            return asset('assets/img/avatars/default-school.png');
        }

        $cleanPath = str_replace(['public/', 'storage/', '/public/', '/storage/'], '', $this->logo);
        $cleanPath = ltrim($cleanPath, '/');

        if (\Storage::disk('public')->exists($cleanPath)) {
            return \Storage::disk('public')->url($cleanPath);
        }

        if ($this->website) {
            $base_url = rtrim($this->website, '/');
            
            // Jika di database cuma simpan 'logos/xxx.png', coba asumsikan dia ada di folder storage remote
            // Tapi kalau link ini mati, kita percayakan ke onerror di view
            return $base_url . '/storage/' . $cleanPath;
        }

        return asset('assets/img/avatars/default-school.png');
    }
}