<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PelanggaranPoinGtk extends Model
{
    use HasFactory;

    protected $table = 'pelanggaran_poin_gtk';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'IDpelanggaran_kategori',
        'nama',
        'poin',
        'tindakan',
        'status'
    ];

    /**
     * Relasi ke kategori pelanggaran
     */
    public function kategoriGtk()
    {
        return $this->belongsTo(PelanggaranKategoriGtk::class, 'IDpelanggaran_kategori', 'ID');
    }

    /**
     * Relasi ke data nilai pelanggaran GTK
     */
    public function nilaiGtk()
    {
        return $this->hasMany(PelanggaranNilaiGtk::class, 'IDpelanggaran_poin', 'ID');
    }
}
