<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TugasPegawai extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan oleh model.
     *
     * @var string
     */
    protected $table = 'tugas_pegawais';

    /**
     * Atribut yang tidak boleh diisi secara massal.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Relasi ke model Gtk untuk mengambil data pegawai.
     */
    public function gtk()
    {
        // Menghubungkan 'pegawai_id' (foreign key) 
        // ke 'id' (primary key) di tabel gtks
        return $this->belongsTo(Gtk::class, 'pegawai_id');
    }
}