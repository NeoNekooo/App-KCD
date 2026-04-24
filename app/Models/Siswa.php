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
        'kabupaten_kota', 'kode_pos', 'foto', 'status', 'sekolah_id'
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
     * Accessor Foto URL (SUPER CLEAN & PINTER)
     */
    public function getFotoUrlAttribute()
    {
        if (!$this->foto) {
            return asset('assets/img/avatars/1.png');
        }

        // 1. Bersihkan path dari segala jenis prefix yang mengganggu
        // Kita mau dapet path murni: "siswas/namafoto.jpg"
        $cleanPath = str_replace(['public/', 'storage/', '/public/', '/storage/'], '', $this->foto);
        $cleanPath = ltrim($cleanPath, '/');

        // 2. Cek Lokal KCD (Disk 'public' otomatis lari ke storage/app/public)
        if (\Storage::disk('public')->exists($cleanPath)) {
            return \Storage::disk('public')->url($cleanPath);
        }

        // 3. Cek Remote (Web Sekolah)
        if ($this->sekolah && $this->sekolah->website) {
            $base_url = rtrim($this->sekolah->website, '/');
            
            // Rakit URL: website.sch.id/storage/siswas/namafoto.jpg
            return $base_url . '/storage/' . $cleanPath;
        }

        return asset('assets/img/avatars/1.png');
    }
}