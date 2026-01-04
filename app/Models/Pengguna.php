<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pengguna extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'penggunas';

    protected $fillable = [
        'username',
        'password',
        'peran_id_str',
        'peserta_didik_id',
        'pengguna_id',
        'sekolah_id' // Pastikan sekolah_id juga ada di fillable jika ingin bisa diisi massal
    ];

    protected $hidden = [
        'password',
    ];

    public function gtk()
    {
        return $this->belongsTo(Gtk::class, 'ptk_id', 'ptk_id');
    }

    // --- TAMBAHAN BARU ---
    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id', 'sekolah_id');
    }
}