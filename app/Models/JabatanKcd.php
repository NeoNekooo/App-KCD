<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\FilterRegional;

class JabatanKcd extends Model
{
    use HasFactory, FilterRegional;

    protected $table = 'jabatan_kcd';

    protected $fillable = [
        'instansi_id',
        'nama',
        'role',
    ];
}
