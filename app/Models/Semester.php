<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'keterangan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Logic untuk memastikan hanya satu Semester yang aktif saat diaktifkan
    protected static function booted()
    {
        static::saving(function ($semester) {
            if ($semester->is_active) {
                // Non-aktifkan semua semester lain
                self::where('id', '!=', $semester->id)->update(['is_active' => false]);
            }
        });
    }
}