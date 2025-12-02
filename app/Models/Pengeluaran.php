<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Pengeluaran extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pengeluarans';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tanggal',
        'uraian',
        'nominal',
        'master_kas_id',
        'petugas_id',
        'kode_akun',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal' => 'date',
    ];

    /**
     * Relasi ke MasterKas untuk mengetahui sumber dana pengeluaran.
     */
    public function masterKas(): BelongsTo
    {
        return $this->belongsTo(MasterKas::class, 'master_kas_id');
    }

    /**
     * Relasi ke Gtk untuk mengetahui siapa petugas yang mencatat.
     */
    public function petugas(): BelongsTo
    {
        return $this->belongsTo(Gtk::class, 'petugas_id');
    }

    /**
     * Relasi polimorfik ke catatan mutasi kas.
     * Setiap pengeluaran memiliki satu catatan mutasi kas.
     */
    public function kasMutasi(): MorphOne
    {
        return $this->morphOne(KasMutasi::class, 'sumber', 'sumber_transaksi', 'transaksi_id');
    }
}
