<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratMasuk extends Model
{
    use HasFactory;

    protected $table = 'surat_masuks';

    protected $fillable = [
        'no_agenda',
        'no_surat',
        'tanggal_surat',
        'tanggal_diterima',
        'asal_surat',
        'perihal',
        'tujuan_disposisi',
        'file_surat',
        'keterangan'
    ];
}
