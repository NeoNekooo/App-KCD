<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsensiMapel extends Model
{
    use HasFactory;

    protected $table = 'absensi_mapel';

    protected $fillable = [
        'jadwal_pelajaran_id',
        'siswa_id',
        'tanggal',
        'status',
        'keterangan',
        'dicatat_oleh_gtk_id',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function jadwal()
    {
        return $this->belongsTo(JadwalPelajaran::class, 'jadwal_pelajaran_id');
    }

    public function guru()
    {
        return $this->belongsTo(Gtk::class, 'dicatat_oleh_gtk_id');
    }
}