<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PkksInstrumen extends Model
{
    protected $fillable = ['nama', 'jenjang', 'tahun', 'skor_min', 'skor_maks', 'is_active'];

    public function kompetensis()
    {
        return $this->hasMany(PkksKompetensi::class, 'pkks_instrumen_id')->orderBy('urutan');
    }

    public function penilaians()
    {
        return $this->hasMany(PkksPenilaian::class, 'pkks_instrumen_id');
    }
}
