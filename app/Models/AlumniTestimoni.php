<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlumniTestimoni extends Model
{
    use HasFactory;

    // Arahkan ke tabel baru
    protected $table = 'alumni_testimonis'; 

    protected $fillable = [
        'siswa_id',
        'nama',
        'pesan',
        'nama_instansi',
        'status_kegiatan',
        'status',
        'tampilkan'
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id', 'id');
    }
}