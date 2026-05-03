<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PkksKompetensi extends Model
{
    protected $fillable = ['pkks_instrumen_id', 'nama', 'urutan'];

    public function instrumen()
    {
        return $this->belongsTo(PkksInstrumen::class, 'pkks_instrumen_id');
    }

    public function indikators()
    {
        return $this->hasMany(PkksIndikator::class, 'pkks_kompetensi_id');
    }
}
