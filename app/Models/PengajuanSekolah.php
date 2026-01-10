<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanSekolah extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
    'dokumen_syarat' => 'array',
];
}