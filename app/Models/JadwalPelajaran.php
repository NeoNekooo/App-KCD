<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPelajaran extends Model
{
    use HasFactory;
    protected $table = 'jadwal_pelajaran';

    // Definisikan relasi ke model lain
    public function rombel()
    {
        return $this->belongsTo(Rombel::class);
    }

    public function guru()
    {
        // Asumsi relasi ke Gtk melalui kolom ptk_id
        return $this->belongsTo(Gtk::class, 'ptk_id', 'ptk_id');
    }

    public function absensiSiswa()
    {
        return $this->hasMany(AbsensiSiswa::class, 'jadwal_id');
    }
}