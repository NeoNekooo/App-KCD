<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tunggakan extends Model
{
    use HasFactory;

    protected $table = 'tunggakans';
    protected $fillable = [
        'siswa_id', 'iuran_id', 'tahun_pelajaran_id', 'total_tunggakan_awal',
        'sisa_tunggakan', 'status'
    ];

    /**
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    /**
     * Relasi ke jenis iuran yang ditunggak.
     */
    public function iuran(): BelongsTo
    {
        return $this->belongsTo(Iuran::class);
    }

    /**
     * Relasi ke tahun pelajaran saat tunggakan terjadi.
     */
    public function tahunPelajaran(): BelongsTo
    {
        return $this->belongsTo(TahunPelajaran::class, 'tahun_pelajaran_id');
    }
}
