<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PelanggaranNilai extends Model
{
    use HasFactory;

    protected $table = 'pelanggaran_nilai';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'NIS',
        'IDtapel',
        'IDsemester',
        'IDkelas',
        'IDpelanggaran_poin',
        'tanggal',
        'jam',
        'poin',
        'IDmapel',
    ];

    /**
     * Mendapatkan detail poin dari catatan pelanggaran.
     */
    public function detailPoin()
    {
        return $this->belongsTo(PelanggaranPoin::class, 'IDpelanggaran_poin', 'ID');
    }

    /**
     * Mendapatkan data siswa yang terkait dengan pelanggaran.
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'NIS', 'nipd');
    }
    
    /**
     * Mendapatkan data rombongan belajar (kelas) yang terkait dengan pelanggaran.
     */
    public function rombel()
    {
        return $this->belongsTo(Rombel::class, 'IDkelas', 'id');
    }

    /**
     * (NONAKTIF) Mendapatkan data mata pelajaran yang terkait dengan pelanggaran.
     * Relasi ini akan diaktifkan kembali setelah model Mapel dibuat.
     */
    /*
    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'IDmapel', 'id');
    }
    */
}

