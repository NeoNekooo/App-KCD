<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gtk extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Relasi ke banyak tugas pegawai (riwayat tugas).
     */
    public function riwayatTugas()
    {
       return $this->hasMany(TugasPegawai::class, 'pegawai_id', 'ptk_id');
    }

    /**
     * Relasi ke rombel jika GTK ini adalah wali kelas.
     */
    public function rombelWali()
    {
        return $this->hasOne(Rombel::class, 'ptk_id', 'ptk_id');
    }
}