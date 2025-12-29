<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MutasiKeluar extends Model
{
    protected $table = 'mutasi_keluar';
    protected $fillable = ['tanggal_keluar', 'status', 'keterangan'];

    public function keluarable()
    {
        return $this->morphTo();
    }
}
