<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SambutanKepalaSekolah extends Model
{
    protected $table = 'sambutan_kepala_sekolahs';

    protected $fillable = [
        'nama_kepala_sekolah',
        'foto',
        'judul_sambutan',
        'isi_sambutan',
        'visi',           // Baru
        'misi',           // Baru
        'program_kerja',  // Baru
    ];
}