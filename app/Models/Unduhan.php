<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unduhan extends Model
{
    protected $table = 'unduhans';

    protected $fillable = [
        'judul', 'deskripsi', 'file', 'kategori', 'jumlah_unduhan',
    ];
}
