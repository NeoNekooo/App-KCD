<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SambutanKepalaSekolah extends Model
{
    protected $table = 'sambutan_kepala_sekolahs';

    protected $fillable = [
        'nama_kepala_sekolah',
        'foto',             // Foto Kepala Sekolah
        'foto_gedung',      // Foto Gedung / Sejarah (BARU)
        'judul_sambutan',
        'isi_sambutan',
        'sejarah',          // Sejarah (BARU)
        'visi',
        'misi',
        'program_kerja',
    ];
}