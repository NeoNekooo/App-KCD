<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PelanggaranNilaiGtk extends Model
{
    use HasFactory;

    protected $table = 'pelanggaran_nilai_gtk';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'nama_guru',
        'IDpelanggaran_poin',
        'tanggal',
        'jam',
        'poin',
        'tapel',
        'semester',
        'semester_id',
    ];

    public function detailPoinGtk()
    {
        return $this->belongsTo(PelanggaranPoinGtk::class, 'IDpelanggaran_poin', 'ID');
    }
}
