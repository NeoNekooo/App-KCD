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
        'kabupaten_kota', 'kode_pos', 'foto', 'status', 'qr_token', 'sekolah_id'
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
     * Accessor Foto URL (SUPER CLEAN & DYNAMIC BASE URL)
     */
    public function getFotoUrlAttribute()
    {
        if (!$this->foto) {
            return asset('assets/img/avatars/1.png');
        }

        // 1. Bersihkan path murni
        $cleanPath = str_replace(['public/', 'storage/', '/public/', '/storage/'], '', $this->foto);
        $cleanPath = ltrim($cleanPath, '/');

        // 2. Cek Lokal KCD
        if (\Storage::disk('public')->exists($cleanPath)) {
            return \Storage::disk('public')->url($cleanPath);
        }

        // 3. Tentukan Base URL (Nyari dari qr_token dulu baru ke website sekolah)
        $baseUrl = null;
        
        // PINTER: Ekstrak domain dari qr_token kalau isinya link (misal: https://simak...)
        if ($this->qr_token && (str_starts_with($this->qr_token, 'http'))) {
            $parsedUrl = parse_url($this->qr_token);
            if (isset($parsedUrl['scheme']) && isset($parsedUrl['host'])) {
                $baseUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
            }
        }

        // Kalau qr_token gak bantu, pake website sekolah
        if (!$baseUrl && $this->sekolah && $this->sekolah->website) {
            $baseUrl = rtrim($this->sekolah->website, '/');
        }

        // 4. Rakit URL Remote
        if ($baseUrl) {
            return $baseUrl . '/storage/' . $cleanPath;
        }

        return asset('assets/img/avatars/1.png');
    }
}