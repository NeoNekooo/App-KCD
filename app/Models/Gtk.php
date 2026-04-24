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
        'foto', 'status', 'sekolah_id'
    ];

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id', 'sekolah_id');
    }

    /**
     * Accessor Foto URL (SUPER CLEAN & PINTER)
     */
    public function getFotoUrlAttribute()
    {
        if (!$this->foto) {
            return asset('assets/img/avatars/1.png');
        }

        // 1. Bersihkan prefix-prefix nakal
        $cleanPath = str_replace(['public/', 'storage/', '/public/', '/storage/'], '', $this->foto);
        $cleanPath = ltrim($cleanPath, '/');

        // 2. Cek Lokal KCD
        if (\Storage::disk('public')->exists($cleanPath)) {
            return \Storage::disk('public')->url($cleanPath);
        }

        // 3. Cek Remote (Web Sekolah)
        if ($this->sekolah && $this->sekolah->website) {
            $base_url = rtrim($this->sekolah->website, '/');
            return $base_url . '/storage/' . $cleanPath;
        }

        return asset('assets/img/avatars/1.png');
    }
}