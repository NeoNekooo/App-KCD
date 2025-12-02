<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Iuran extends Model
{
    use HasFactory;

    protected $table = 'iurans';
    protected $fillable = ['nama_iuran', 'tipe_iuran', 'besaran_default', 'tahun_pelajaran_id'];

    /**
     * Relasi ke tahun ajaran.
     */
public function tapel()
{
    return $this->belongsTo(Tapel::class, 'tahun_pelajaran_id');
}

    /**
     * Relasi ke tagihan (Bulanan) yang dibuat dari iuran ini.
     */
    public function tagihans(): HasMany
    {
        return $this->hasMany(Tagihan::class);
    }

    /**
     * Relasi ke tunggakan (Bebas) yang dibuat dari iuran ini.
     */
    public function tunggakans(): HasMany
    {
        return $this->hasMany(Tunggakan::class);
    }
}
