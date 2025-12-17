<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     * (Opsional jika nama tabelnya jamak dari nama model, misal: settings)
     */
    protected $table = 'settings';

    /**
     * Kolom yang boleh diisi secara massal (Mass Assignment).
     * Sangat penting agar fungsi updateOrCreate() di controller bisa jalan.
     */
    protected $fillable = [
        'key',
        'value',
    ];
}
