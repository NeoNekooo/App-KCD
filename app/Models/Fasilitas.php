<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterRegional;

class Fasilitas extends Model
{
    use FilterRegional;

    protected $table = 'fasilitas';

    protected $fillable = [
        'instansi_id',
        'nama_fasilitas',
        'deskripsi',
        'foto',
    ];
}