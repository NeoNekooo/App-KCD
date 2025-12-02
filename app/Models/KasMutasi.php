<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KasMutasi extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model ini.
     *
     * @var string
     */
    protected $table = 'kas_mutasis';

    /**
     * Mass assignment protection.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Tipe data native untuk atribut.
     *
     * @var array
     */
    protected $casts = [
        'tanggal' => 'date',
    ];

    /**
     * Relasi ke master kas yang memiliki mutasi ini.
     */
    public function masterKas(): BelongsTo
    {
        return $this->belongsTo(MasterKas::class);
    }
}
