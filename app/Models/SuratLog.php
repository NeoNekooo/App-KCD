<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SuratLog extends Model {
    protected $table = 'surat_logs';
    protected $guarded = ['id'];
    protected $casts = ['tanggal_dibuat' => 'datetime'];
}
