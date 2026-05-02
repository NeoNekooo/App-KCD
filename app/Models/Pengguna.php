<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\FilterRegional;

class Pengguna extends Authenticatable
{
    use HasFactory, Notifiable, FilterRegional;

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
        'remember_token',
        'google2fa_secret',
    ];

    /**
     * Decrypt the 2FA secret when retrieved.
     */
    public function getGoogle2faSecretAttribute($value)
    {
        if (empty($value)) return null;
        try {
            return decrypt($value);
        } catch (\Exception $e) {
            return $value; // Return as-is if not encrypted
        }
    }

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