<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gtk extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Relasi ke banyak tugas pegawai (riwayat tugas).
     */
   protected $fillable = ['status'];

    public function riwayatTugas()
    {
       return $this->hasMany(TugasPegawai::class, 'pegawai_id', 'ptk_id');
    }

    /**
     * Relasi ke rombel jika GTK ini adalah wali kelas.
     */
    public function rombelWali()
    {
        return $this->hasOne(Rombel::class, 'ptk_id', 'ptk_id');
    }

    /**
     * Relasi ke pengguna (akun) berdasarkan ptk_id.
     */
    public function pengguna()
    {
        return $this->hasOne(Pengguna::class, 'ptk_id', 'ptk_id');
    }

    public function mutasiKeluar()
    {
        return $this->morphOne(MutasiKeluar::class, 'keluarable');
    }

    /**
     * Ambil email dari tabel pengguna bila tersedia, fallback ke attribute lokal.
     */
    public function getEmailAttribute($value)
    {
        // Jika hubungan telah eager-loaded gunakan itu (hindari N+1), kalau belum ambil langsung dari pengguna
        return $this->relationLoaded('pengguna') ? ($this->pengguna?->email ?? $value) : ($this->pengguna()->value('email') ?? $value);
    }

    /**
     * Ambil no_hp dari tabel pengguna bila tersedia, fallback ke attribute lokal.
     */
    public function getNoHpAttribute($value)
    {
        return $this->relationLoaded('pengguna') ? ($this->pengguna?->no_hp ?? $value) : ($this->pengguna()->value('no_hp') ?? $value);
    }
}
