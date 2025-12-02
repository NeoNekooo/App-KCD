<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rombel extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung.
     *
     * @var string
     */
    protected $table = 'rombels';

    /**
     * Primary key untuk model ini.
     *
     * @var string
     */
    protected $primaryKey = 'id'; // Sesuai screenshot Anda


    protected $fillable = [
        'nama_rombel',
        'jenis_rombel',
        'kurikulum_id',
        'jurusan_id',
        'wali_id', // Ini untuk Wali Kelas (dari tabel ptk)
        'tingkat',
        'ruang',
        'is_moving_class',
        'melayani_kebutuhan_khusus',
        'tahun_ajaran',
    ];

    /**
     * Relasi ke semua Siswa di Rombel ini.
     * Menghubungkan rombels.rombongan_belajar_id (Varchar) -> siswas.rombongan_belajar_id (Varchar)
     */
    
    public function siswas()
    {
        return $this->hasMany(Siswa::class, 'rombongan_belajar_id', 'rombongan_belajar_id');
    }

    /**
     * Relasi ke semua data pelanggaran yang terjadi di rombel ini.
     * Menghubungkan rombels.id (PK) -> pelanggaran_nilai.rombongan_belajar_id (FK)
     */
    public function pelanggaran(): HasMany
    {
        return $this->hasMany(PelanggaranNilai::class, 'rombongan_belajar_id', 'id');
    }

    /**
     * Relasi ke Ptk (Wali Kelas)
     * Satu Rombel punya SATU Wali Kelas
     */
    public function wali() 
    {
        // 'wali_id' adalah foreign key di tabel 'rombels'
        return $this->belongsTo(Ptk::class, 'wali_id');
    }

    /**
     * Relasi ke Jurusan
     * Satu Rombel punya SATU Jurusan
     */
    public function jurusan() 
    {
        // 'jurusan_id' adalah foreign key
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }

    /**
     * Relasi ke Kurikulum
     * Satu Rombel punya SATU Kurikulum
     */
    public function kurikulum() 
    {
        // 'kurikulum_id' adalah foreign key
        return $this->belongsTo(Kurikulum::class, 'kurikulum_id');
    }

    
    public function waliKelas()
    {
        return $this->belongsTo(Gtk::class, 'ptk_id', 'ptk_id');
    }
}