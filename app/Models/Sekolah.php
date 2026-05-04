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

    public function pengawasPembina()
    {
        return $this->hasOne(PengawasPembina::class, 'sekolah_id', 'sekolah_id');
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

        // 1. Cek Lokal
        if (\Storage::disk('public')->exists($cleanPath)) {
            return \Storage::disk('public')->url($cleanPath);
        }

        // 2. Cek Remote
        if ($this->website) {
            $urlParts = parse_url($this->website);
            $host = $urlParts['host'] ?? $this->website;
            $scheme = $urlParts['scheme'] ?? 'https';
            
            // Coba gunakan website apa adanya
            $baseUrl = $scheme . '://' . rtrim($host, '/');
            
            // Jika host tidak mengandung 'simak.' atau 'siakad.', kita coba tambahkan 'simak.' 
            // sebagai probabilitas tertinggi berdasarkan arsitektur aplikasi ini
            if (!str_contains($host, 'simak.') && !str_contains($host, 'siakad.')) {
                $baseUrl = $scheme . '://simak.' . ltrim($host, 'www.');
            }

            return $baseUrl . '/storage/' . $cleanPath;
        }

        return asset('assets/img/avatars/default-school.png');
    }
}