<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsensiGtk extends Model
{
    use HasFactory;

    protected $table = 'absensi_gtk';

    // Izinkan kolom ini diisi
    protected $fillable = [
        'gtk_id',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'status',
        'keterangan',
        'status_kehadiran', // Kita akan gunakan ini untuk 'Terlambat'
    ];

    // Relasi ke model Gtk
    public function gtk()
    {
        return $this->belongsTo(Gtk::class, 'gtk_id');
    }
}