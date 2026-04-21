<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\FilterRegional;

class Unduhan extends Model
{
    use FilterRegional;

    protected $fillable = [
        'instansi_id',
        'judul', 'deskripsi', 'file', 'kategori', 'jumlah_unduhan',
    ];
}
