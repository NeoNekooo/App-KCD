<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Relasi ke semua tugas
    public function semuaTugas()
    {
        return $this->hasMany(TugasPegawai::class);
    }

    // Relasi khusus untuk mengambil hanya satu tugas terbaru
    public function tugasTerbaru()
    {
        return $this->hasOne(TugasPegawai::class)->latestOfMany();
    }
}

