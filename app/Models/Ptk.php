<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ptk extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model ini.
     * (Opsional jika nama tabel sudah sesuai konvensi: 'ptk')
     *
     * @var string
     */
    protected $table = 'ptk';
}