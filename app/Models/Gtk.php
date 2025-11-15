<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gtk extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'gtks';


    public function jadwalPelajaran()
    {
        // GANTI 'gtk_id' jika nama kolomnya beda (misal: 'ptk_id')
        return $this->hasMany(JadwalPelajaran::class, 'gtk_id'); 
    }

    // Relasi ke absensi GTK
    public function absensi()
    {
        return $this->hasMany(AbsensiGtk::class, 'gtk_id');
    }
}