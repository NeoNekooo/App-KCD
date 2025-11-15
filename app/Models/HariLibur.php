<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HariLibur extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model ini.
     * Ini akan memberitahu Eloquent untuk tidak mencari 'hari_liburs'.
     *
     * @var string
     */
    protected $table = 'hari_libur';

    /**
     * Atribut yang bisa diisi secara massal (untuk method store).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tanggal',
        'keterangan',
    ];
}
