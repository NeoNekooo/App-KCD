<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rombel extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rombels';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'rombongan_belajar_id',
        'nama',
        'tingkat_pendidikan_id',
        'ptk_id', // Wali Kelas
        'jurusan_id',
        // Tambahkan kolom lain yang relevan
    ];

    /**
     * Mendapatkan semua siswa yang ada di dalam rombel ini.
     */
    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'rombongan_belajar_id', 'rombongan_belajar_id');
    }

    public function wali() {
        return $this->belongsTo(Ptk::class, 'wali_id');
    }
    public function jurusan() {
        return $this->belongsTo(Jurusan::class);
    }
    public function kurikulum() {
        return $this->belongsTo(Kurikulum::class);
    }

    public function waliKelas()
    {
        return $this->belongsTo(Gtk::class, 'ptk_id_str', 'nama');
    }
}
