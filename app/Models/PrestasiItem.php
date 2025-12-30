<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrestasiItem extends Model
{
    protected $fillable = ['prestasi_id', 'file', 'caption'];

    public function prestasi()
    {
        return $this->belongsTo(Prestasi::class);
    }
}