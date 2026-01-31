<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PegawaiKcd extends Model
{
    use HasFactory;

    protected $table = 'pegawai_kcds';

    // Kita definisikan $fillable biar aman dan jelas kolom apa aja yang boleh diisi
    protected $fillable = [
        'user_id', 
        'nama', 
        'nik', 
        'nip', 
        'jabatan', 
        'jabatan_kcd_id',
        'tempat_lahir', 
        'tanggal_lahir', 
        'jenis_kelamin', 
        'no_hp', 
        'email_pribadi', 
        'alamat', 
        'foto'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    // Relasi ke User Login
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Jabatan KCD
    public function jabatanKcd()
    {
        return $this->belongsTo(JabatanKcd::class);
    }
}