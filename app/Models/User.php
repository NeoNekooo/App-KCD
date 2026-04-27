<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\FilterRegional;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, FilterRegional;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'instansi_id',
        'name',
        'email',
        'password',
        'username',
        'role',
        'pegawai_kcd_id',
        'google2fa_secret',
        'google2fa_enabled',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke tabel pegawai_kcds.
     * Digunakan untuk mengambil data detail pegawai termasuk kolom 'jabatan'.
     */
    public function pegawaiKcd(): BelongsTo
    {
        // Pastikan model \App\Models\PegawaiKcd sudah ada
        return $this->belongsTo(PegawaiKcd::class, 'pegawai_kcd_id');
    }

    /**
     * 🔥 CUSTOM ROLE CHECKER (Fix Error hasRole)
     */
    public function hasRole($role)
    {
        if (strtolower($role) === 'super admin') {
            return strtolower($this->role) === 'administrator';
        }
        return strtolower($this->role) === strtolower($role);
    }
}