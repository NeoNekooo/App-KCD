<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TugasPegawai extends Model
{
    use HasFactory;

    protected $table = 'tugas_pegawais';

    protected $guarded = [];

    /**
     * Relasi ke model Gtk untuk mengambil data pegawai.
     */
    public function gtk()
    {
        // Menghubungkan 'pegawai_id' (di tabel ini) 
        // dengan 'ptk_id' (di tabel 'gtks')
        return $this->belongsTo(Gtk::class, 'pegawai_id', 'ptk_id');
    }
}