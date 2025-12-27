<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlumniData extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'alumni_data';

    // Kolom yang tidak boleh diisi massal (biarkan id saja yang guarded)
    protected $guarded = ['id'];

    /**
     * RELASI KE TABEL SISWA
     * Fungsi ini Wajib ada agar 'AlumniData::with('siswa')' di Controller bisa jalan.
     */
    public function siswa()
    {
        // Menghubungkan 'peserta_didik_id' di tabel alumni_data 
        // dengan 'peserta_didik_id' di tabel siswas
        return $this->belongsTo(Siswa::class, 'peserta_didik_id', 'peserta_didik_id');
    }
}