<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterRegional;

class Siswa extends Model
{
    use HasFactory, FilterRegional;

    protected $table = 'siswas';

    protected $fillable = [
        'sekolah_id', 'nama', 'nipd', 'jenis_kelamin', 'nisn', 'tempat_lahir', 
        'tanggal_lahir', 'nik', 'agama_id', 'agama_id_str', 'alamat_jalan', 
        'rt', 'rw', 'nama_dusun', 'desa_kelurahan', 'kecamatan', 
        'kabupaten_kota', 'kode_pos', 'foto', 'status', 'qr_token', 
        'rombel_id', 'rombongan_belajar_id', 'nama_rombel'
    ];

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id', 'sekolah_id');
    }

    public function rombel()
    {
        return $this->belongsTo(Rombel::class, 'rombel_id', 'rombel_id');
    }

    /**
     * Accessor Foto URL (SUPER SMART + OPTIMIZED)
     */
    public function getFotoUrlAttribute()
    {
        // 1. Placeholder Default
        $default = asset('assets/img/avatars/1.png');

        if (!$this->foto || str_contains($this->foto, 'default')) {
            return $default;
        }

        // 2. Bersihkan path
        $cleanPath = str_replace(['public/', 'storage/', '/public/', '/storage/'], '', $this->foto);
        $cleanPath = ltrim($cleanPath, '/');

        // 3. Cek Lokal KCD (Sangat Cepat)
        if (\Storage::disk('public')->exists($cleanPath)) {
            return \Storage::disk('public')->url($cleanPath);
        }

        // 4. Remote Discovery
        $baseUrl = null;
        if ($this->qr_token && str_starts_with($this->qr_token, 'http')) {
            $parsed = parse_url($this->qr_token);
            $baseUrl = ($parsed['scheme'] ?? 'https') . '://' . ($parsed['host'] ?? '');
        }

        if (!$baseUrl && $this->sekolah && $this->sekolah->website) {
            $baseUrl = rtrim($this->sekolah->website, '/');
        }

        if ($baseUrl) {
            return $baseUrl . '/storage/' . $cleanPath;
        }

        return $default;
    }
}