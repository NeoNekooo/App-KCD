<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PkksIndikator extends Model
{
    protected $fillable = ['pkks_kompetensi_id', 'nomor', 'kriteria', 'bukti_identifikasi'];

    public function kompetensi()
    {
        return $this->belongsTo(PkksKompetensi::class, 'pkks_kompetensi_id');
    }
}
