<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\FilterRegional;

class DokumenLayanan extends Model
{
    use FilterRegional;
    protected $fillable = [
        'instansi_id',
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
