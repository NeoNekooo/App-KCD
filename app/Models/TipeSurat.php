<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipeSurat extends Model
{
    use HasFactory;

    // Menentukan nama tabel secara eksplisit
    protected $table = 'tipe_surats';

    // Mengizinkan semua kolom diisi (mass assignment)
    // Kolom: judul_surat, kategori, template_isi
    protected $guarded = [];

    /**
     * Relasi: Satu tipe surat bisa digunakan di banyak surat keluar
     */
    public function suratKeluarSiswas()
    {
        return $this->hasMany(SuratKeluarSiswa::class, 'tipe_surat_id');
    }
}