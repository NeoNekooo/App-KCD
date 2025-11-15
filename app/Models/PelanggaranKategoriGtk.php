<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PelanggaranKategoriGtk extends Model
{
    use HasFactory;

    protected $table = 'pelanggaran_kategori_gtk';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = ['nama', 'status'];

    /**
     * Relasi ke jenis pelanggaran (poin)
     */
    public function pelanggaranPoinGtk()
    {
        return $this->hasMany(PelanggaranPoinGtk::class, 'IDpelanggaran_kategori', 'ID');
    }
}
