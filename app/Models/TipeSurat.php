<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipeSurat extends Model
{
    use HasFactory;

    protected $table = 'tipe_surats';

    // $guarded = [] sudah aman, tapel_id otomatis bisa diinput.
    protected $guarded = [];

    /**
     * Relasi ke Tapel (Tahun Pelajaran)
     * Menghubungkan kolom 'tapel_id' di tabel ini ke tabel 'tapel'
     */
    public function tapel()
    {
        // belongsTo artinya "Tipe Surat ini MILIK satu Tapel"
        return $this->belongsTo(Tapel::class, 'tapel_id');
    }

    /**
     * Relasi: Satu tipe surat bisa digunakan di banyak surat keluar
     */
    public function suratKeluarSiswas()
    {
        return $this->hasMany(SuratKeluarSiswa::class, 'tipe_surat_id');
    }
}