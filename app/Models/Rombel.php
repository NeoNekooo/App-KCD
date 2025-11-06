<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rombel extends Model
{
    use HasFactory;

    /**
     * Table name.
     */
    protected $table = 'rombels';

    /**
     * Primary key.
     */
    protected $primaryKey = 'id';

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'rombongan_belajar_id',
        'nama',
        'tingkat_pendidikan_id',
        'ptk_id', // Wali Kelas
        'jurusan_id',
        // Tambahkan kolom lain jika ada
    ];

    /**
     * Cast JSON columns.
     */
    protected $casts = [
        'anggota_rombel' => 'array',
        'pembelajaran'   => 'array',
    ];

    /**
     * Relasi: semua siswa di rombel ini.
     */
    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'rombongan_belajar_id', 'rombongan_belajar_id');
    }

    /**
     * Relasi ke PTK (wali kelas) jika memang ada kolom wali_id.
     */
    public function wali()
    {
        return $this->belongsTo(Ptk::class, 'wali_id');
    }

    /**
     * Relasi ke jurusan.
     */
    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    /**
     * Relasi ke kurikulum.
     */
    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class);
    }

    /**
     * Relasi wali kelas versi modul kepegawaian (lebih realistis).
     */
    public function waliKelas()
    {
        return $this->belongsTo(Gtk::class, 'ptk_id', 'ptk_id');
    }
}
