<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterKas extends Model
{
    use HasFactory;

    protected $table = 'master_kas';
    
    protected $fillable = [
        'nama_kas',
        'saldo_awal',
        // Tambahkan field lain yang aman di sini (misalnya, 'deskripsi')
    ];
}
