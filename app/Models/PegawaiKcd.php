<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PegawaiKcd extends Model
{
    use HasFactory;

    protected $guarded = []; // Biar bisa create massal

    // Relasi ke User Login
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}