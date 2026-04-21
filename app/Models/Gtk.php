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
    //protected $fillable = ['status'];

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

    // 🔥 TAMBAHKAN KODE INI DI SINI 🔥
    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id', 'sekolah_id');
    }
}