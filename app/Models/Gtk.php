<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\EncryptsSensitiveData;
use App\Traits\FilterRegional;

class Gtk extends Model
{
    use HasFactory, EncryptsSensitiveData, FilterRegional;

    protected $guarded = [];

    public $incrementing = false;
    protected $keyType = 'string';

    public function riwayatTugas()
    {
       return $this->hasMany(TugasPegawai::class, 'pegawai_id', 'ptk_id');
    }

    public function rombelWali()
    {
        return $this->hasOne(Rombel::class, 'ptk_id', 'ptk_id');
    }

    public function pengguna()
    {
        return $this->hasOne(Pengguna::class, 'ptk_id', 'ptk_id');
    }

    public function mutasiKeluar()
    {
        return $this->morphOne(MutasiKeluar::class, 'keluarable');
    }

    public function getEmailAttribute($value)
    {
        return $this->relationLoaded('pengguna') ? ($this->pengguna?->email ?? $value) : ($this->pengguna()->value('email') ?? $value);
    }

    public function getNoHpAttribute($value)
    {
        return $this->relationLoaded('pengguna') ? ($this->pengguna?->no_hp ?? $value) : ($this->pengguna()->value('no_hp') ?? $value);
    }

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id', 'sekolah_id');
    }

    // Accessor Foto URL (PENTING: Biar bisa nampilin foto dari website sekolah asal)
    public function getFotoUrlAttribute()
    {
        // 1. Cek lokal KCD
        if ($this->foto && \Storage::disk('public')->exists($this->foto)) {
            return \Storage::disk('public')->url($this->foto);
        }

        // 2. Cek Website Sekolah (Kalau file fisik belum disinkronisasi ke KCD)
        if ($this->foto && $this->sekolah?->website) {
            $base_url = rtrim($this->sekolah->website, '/');
            return $base_url . '/storage/' . $this->foto;
        }

        return asset('assets/img/avatars/default.png');
    }
}