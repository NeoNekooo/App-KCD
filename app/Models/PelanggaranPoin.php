<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PelanggaranPoin extends Model
{
    use HasFactory;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pelanggaran_poin';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'ID';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'IDpelanggaran_kategori',
        'nama',
        'poin',
        'tindakan',
        'status',
    ];

    /**
     * Get the category that owns the violation point.
     */
    public function kategori()
    {
        return $this->belongsTo(PelanggaranKategori::class, 'IDpelanggaran_kategori', 'ID');
    }
}

