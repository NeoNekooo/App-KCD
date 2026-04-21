<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\FilterRegional;

class SuratMasuk extends Model
{
    use HasFactory, FilterRegional;

    protected $table = 'surat_masuks';

    protected $fillable = [
        'instansi_id',
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
