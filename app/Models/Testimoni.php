<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterRegional;

class Testimoni extends Model
{
    use FilterRegional;

    protected $fillable = [
        'instansi_id',
        'nama',
        'status', // Jabatan/Status (Wali Murid, Alumni)
        'isi',
        'foto',
        'is_published', // Kolom baru untuk status tayang
    ];
}