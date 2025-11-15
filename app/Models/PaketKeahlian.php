<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaketKeahlian extends Model
{
    use HasFactory;

    protected $table = 'paket_keahlians';

    protected $fillable = [
        'kode',
        'nama_paket',
    ];
}
