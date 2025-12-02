<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tapel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tagihan extends Model
{
    use HasFactory;

    protected $table = 'tagihans';
    protected $fillable = [
        'siswa_id', 'iuran_id', 'tahun_ajaran_id', 'periode',
        'tanggal_mulai',
        'tahun_pelajaran_id',
        'jumlah_tagihan', 'sisa_tagihan', 'status'
    ];

    /**
     * Relasi ke siswa yang memiliki tagihan ini.
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    /**
     * Relasi ke jenis iuran.
     */
    public function iuran(): BelongsTo
    {
        return $this->belongsTo(Iuran::class);
    }

    /**
     * Relasi ke semua pembayaran yang melunasi tagihan ini.
     */
    public function pembayarans(): HasMany
    {
        return $this->hasMany(Pembayaran::class);
    }
    public function tapel(): BelongsTo
    {
        return $this->belongsTo(Tapel::class, 'tahun_ajaran_id', 'id');
    }
}
