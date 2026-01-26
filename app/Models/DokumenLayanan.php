<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokumenLayanan extends Model
{
    protected $fillable = [
        'pengajuan_sekolah_id',
        'nama_dokumen',
        'path_dokumen',
    ];

    /**
     * Get the pengajuanSekolah that owns the DokumenLayanan
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pengajuanSekolah(): BelongsTo
    {
        return $this->belongsTo(PengajuanSekolah::class);
    }
}
