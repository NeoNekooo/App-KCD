<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalPelajaran extends Model
{
    protected $table = 'jadwal_pelajarans';
    protected $guarded = [];

    // Relasi ke Master Waktu
    public function jamPelajaran()
    {
        return $this->belongsTo(JamPelajaran::class, 'jam_pelajaran_id');
    }

    // Relasi ke Pembelajaran (Guru + Mapel)
    public function pembelajaran()
    {
        return $this->belongsTo(Pembelajaran::class, 'pembelajaran_id');
    }

    // Relasi ke Rombel
    public function rombel()
    {
        return $this->belongsTo(Rombel::class);
    }
}
