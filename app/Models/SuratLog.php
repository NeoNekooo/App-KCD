<?php

namespace App\Models;

use App\Traits\FilterRegional;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SuratLog extends Model {
    use FilterRegional;
    protected $table = 'surat_logs';
    protected $guarded = ['id'];
    protected $casts = ['tanggal_dibuat' => 'datetime'];
}

class TugasPegawaiKcd extends Model
{
    use HasFactory, FilterRegional;

    protected $fillable = [
        'instansi_id',
        'pegawai_kcd_id',
    ];
}
