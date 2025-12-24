<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TugasPegawai extends Model
{
    use HasFactory;

    protected $fillable = [
        'pegawai_id',
        'tahun_pelajaran',
        'semester',
        'nomor_sk',
        'tmt',
        'keterangan'
    ];

    // Relasi ke data GTK/Guru
    public function gtk()
    {
        return $this->belongsTo(Gtk::class, 'pegawai_id');
    }

    // Relasi ke rincian mengajar (1 SK punya banyak rincian)
    public function details()
    {
        return $this->hasMany(TugasPegawaiDetail::class, 'tugas_pegawai_id');
    }

    // Helper untuk hitung total jam otomatis dari detail
    public function getTotalJamAttribute()
    {
        return $this->details->sum('jumlah_jam');
    }
}
