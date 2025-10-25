<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TugasPegawai extends Model
{
    use HasFactory;

    protected $table = 'tugas_pegawais';

    protected $guarded = [];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

   public function gtk()
    {
        return $this->belongsTo(Gtk::class, 'pegawai_id', 'ptk_id');
    }
}