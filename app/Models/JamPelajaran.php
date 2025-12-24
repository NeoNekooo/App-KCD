<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JamPelajaran extends Model
{
    use HasFactory;

    protected $table = 'jam_pelajarans';

    protected $fillable = [
        'hari',
        'urutan',
        'nama',
        'jam_mulai',
        'jam_selesai',
        'tipe',
    ];

    // Helper untuk menampilkan format jam yang bersih (07:00 bukan 07:00:00)
    public function getRangeJamAttribute()
    {
        return \Carbon\Carbon::parse($this->jam_mulai)->format('H:i') . ' - ' .
               \Carbon\Carbon::parse($this->jam_selesai)->format('H:i');
    }
}
