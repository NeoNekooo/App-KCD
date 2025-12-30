<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // <-- TAMBAHAN
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rombel extends Model
{
    use HasFactory; // <-- TAMBAHAN

    /**
     * Atribut yang boleh diisi secara massal (mass assignable).
     * Sesuaikan dengan semua kolom di migrasi Anda.
     *
     * @var array<int, string>
     */
    protected $table = 'rombels';
    protected $fillable = [
        'nama_rombel',
        'jenis_rombel',
        'kurikulum_id',
        'kurikulum_id_str', // prefer storing kurikulum as imported string
        'kurikulum', // legacy / explicit field if present
        'jurusan_id',
        'jurusan_id_str', // prefer storing jurusan as imported string
        'jurusan', // legacy / explicit field if present
        'wali_id', // Ini untuk Wali Kelas (dari tabel ptk)
        'tingkat',
        'ruang',
        'is_moving_class',
        'melayani_kebutuhan_khusus',
        'tahun_ajaran',
    ];

    // Aksesori untuk kompatibilitas tabel impor yang menggunakan kolom `nama`
    // Memungkinkan penggunaan `$rombel->nama_rombel` di seluruh kode tanpa mengubah views.
    public function getNamaRombelAttribute()
    {
        return $this->attributes['nama'] ?? null;
    }

    public function setNamaRombelAttribute($value)
    {
        $this->attributes['nama'] = $value;
    }

    /**
     * Relasi many-to-many ke `Siswa` melalui tabel `anggota_rombel` (pivot)
     * Keeps pivot metadata for reference and future features.
     */
    public function siswa()
    {
        return $this->belongsToMany(Siswa::class, 'anggota_rombel', 'rombel_id', 'siswa_id')
                    ->withPivot(['peserta_didik_id','anggota_rombel_id','jenis_pendaftaran_id'])
                    ->withTimestamps();
    }

    /**
     * Relasi ke Ptk (Wali Kelas)
     * Satu Rombel punya SATU Wali Kelas
     */
    public function wali()
    {
        // 'wali_id' adalah foreign key di tabel 'rombels'
        return $this->belongsTo(Ptk::class, 'wali_id');
    }

    // NOTE: We intentionally no longer define a `jurusan()` relation to a separate `jurusan` table.
    // The preferred source of truth for jurusan in a Rombel is the imported string field
    // `jurusan_id_str` (or legacy `jurusan`/`jurusan_id`). This simplifies schema and avoids
    // requiring a separate `jurusan` master table for basic display and filtering.

    // NOTE: We intentionally no longer define a `kurikulum()` relation to a separate `kurikulum` table.
    // Kurikulum is taken directly from Rombel fields (e.g. `kurikulum_id_str`), so we avoid
    // depending on a master `kurikulum` table for basic display/filtering.

    // Compatibility accessors for imported fields and relations
    public function getJurusanNameAttribute()
    {
        // Prefer the imported string column `jurusan_id_str`, then fallback to other available
        // fields. We no longer rely on a `jurusan` relation.
        return $this->attributes['jurusan_id_str']
               ?? $this->attributes['jurusan']
               ?? $this->attributes['jurusan_id']
               ?? null;
    }

    public function getKurikulumNameAttribute()
    {
        // Prefer the imported string column `kurikulum_id_str`, then fallback to other available
        // fields. We no longer rely on a `kurikulum` relation.
        return $this->attributes['kurikulum_id_str']
               ?? $this->attributes['kurikulum']
               ?? $this->attributes['kurikulum_id']
               ?? null;
    }

    public function getWaliNameAttribute()
    {
        if ($this->relationLoaded('wali') && $this->wali) {
            return $this->wali->nama;
        }
        return $this->attributes['ptk_id_str'] ?? $this->attributes['wali_id'] ?? null;
    }

    public function getRuangNameAttribute()
    {
        return $this->attributes['ruang'] ?? $this->attributes['id_ruang_str'] ?? null;
    }

    public function getTingkatAttribute($value)
    {
        // Prefer explicit tingkat column, otherwise use imported string
        return $this->attributes['tingkat'] ?? $this->attributes['tingkat_pendidikan_id_str'] ?? null;
    }

    public function getTahunAjaranAttribute($value)
    {
        if (!empty($this->attributes['tahun_ajaran'])) {
            return $this->attributes['tahun_ajaran'];
        }
        if (!empty($this->attributes['semester_id'])) {
            // try extract year prefix
            return substr($this->attributes['semester_id'], 0, 4);
        }
        return null;
    }

    // Kompatibilitas dengan impor: kolom `anggota_rombel` menyimpan JSON berisi anggota.
    // Implementasikan aksesori `siswa` sehingga `$rombel->siswa` menghasilkan koleksi model `Siswa`.
    public function getSiswaAttribute()
    {
        $json = $this->attributes['anggota_rombel'] ?? null;
        if (! $json) {
            return collect();
        }

        $items = json_decode($json, true);
        if (! is_array($items)) {
            return collect();
        }

        $ids = collect($items)->pluck('peserta_didik_id')->filter()->values()->all();
        if (empty($ids)) {
            return collect();
        }

        return Siswa::whereIn('peserta_didik_id', $ids)->get();
    }
}
// Other code in the file
