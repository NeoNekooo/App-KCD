<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterRegional;

class Gtk extends Model
{
    use HasFactory, FilterRegional;

    protected $table = 'gtks';

    protected $fillable = [
        'sekolah_id', 'nama', 'nuptk', 'jenis_kelamin', 'tempat_lahir', 
        'tanggal_lahir', 'nip', 'status_kepegawaian_id_str', 'jenis_ptk_id_str',
        'foto', 'status', 'qr_token', 'sekolah_id'
    ];

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id', 'sekolah_id');
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

        // 2. Bersihkan path murni
        $cleanPath = str_replace(['public/', 'storage/', '/public/', '/storage/'], '', $this->foto);
        $cleanPath = ltrim($cleanPath, '/');

        // 3. Cek Lokal KCD
        if (\Storage::disk('public')->exists($cleanPath)) {
            return \Storage::disk('public')->url($cleanPath);
        }

        // 4. Remote Discovery
        $baseUrl = null;
        if ($this->qr_token && str_starts_with($this->qr_token, 'http')) {
            $parsed = parse_url($this->qr_token);
            if (isset($parsed['scheme']) && isset($parsed['host'])) {
                $baseUrl = $parsed['scheme'] . '://' . $parsed['host'];
            }
        }

        if (!$baseUrl && $this->sekolah && $this->sekolah->website) {
            $baseUrl = rtrim($this->sekolah->website, '/');
        }

        // 5. Rakit URL Remote
        if ($baseUrl) {
            return $baseUrl . '/storage/' . $cleanPath;
        }

        return $default;
    }
}