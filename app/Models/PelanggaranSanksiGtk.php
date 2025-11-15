<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PelanggaranSanksiGtk extends Model
{
    use HasFactory;

    protected $table = 'pelanggaran_sanksi_gtk';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'poin_min',
        'poin_max',
        'nama',
        'penindak'
    ];
}
