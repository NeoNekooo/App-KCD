<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rombel extends Model
{
    use HasFactory;

    protected $table = 'rombels';

    protected $casts = [
        'anggota_rombel' => 'array',
        'pembelajaran'   => 'array',
    ];

    public function waliKelas()
{
    return $this->belongsTo(Gtk::class, 'ptk_id', 'ptk_id');
}
}