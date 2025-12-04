<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ekstrakurikuler extends Model
{
    use HasFactory;

    protected $table = 'ekstrakurikulers';

    // UPDATE: Sesuaikan fillable dengan kolom baru di database
    // 'nama_ekskul' diganti dengan 'daftar_ekstrakurikuler_id'
    protected $fillable = [
        'daftar_ekstrakurikuler_id', 
        'pembina_id', 
        'prasarana'
    ];

    /**
     * Relasi ke Master Data Ekskul (Tabel daftar_ekstrakurikuler)
     * Digunakan untuk mengambil nama ekskul: $ekskul->daftar->nama
     */
    public function daftar()
    {
        // Pastikan model DaftarEkstrakurikuler sudah ada
        return $this->belongsTo(DaftarEkstrakurikuler::class, 'daftar_ekstrakurikuler_id');
    }

    /**
     * Relasi ke Guru/PTK Pembina
     */
    public function pembina()
    {
        return $this->belongsTo(Gtk::class, 'pembina_id');
    }
}