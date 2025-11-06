<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// PENTING: Kita extend Authenticatable agar model ini bisa digunakan untuk login
class Pengguna extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Nama tabel yang terhubung dengan model ini.
     *
     * @var string
     */
    protected $table = 'penggunas';

    /**
     * Atribut yang bisa diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'password',
        // tambahkan kolom lain jika perlu
    ];

    /**
     * Atribut yang harus disembunyikan.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Mendefinisikan relasi bahwa satu Pengguna "milik" satu GTK.
     */
    public function gtk()
    {
        // Menghubungkan kolom 'ptk_id' di tabel 'penggunas' 
        // dengan kolom 'ptk_id' di tabel 'gtks'
        return $this->belongsTo(Gtk::class, 'ptk_id', 'ptk_id');
    }
}
