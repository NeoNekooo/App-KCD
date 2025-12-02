<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayarans';
    protected $fillable = [
        'siswa_id', 'iuran_id', 'tagihan_id', 'tunggakan_id', 'tanggal_bayar', 'jumlah_bayar',
        'master_kas_id', 'acc_jurnal_id', 'petugas_id', 'keterangan'
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
    ];

    /**
     * Relasi ke siswa yang melakukan pembayaran.
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    /**
     * Relasi ke master iuran.
     */
    public function iuran(): BelongsTo
    {
        return $this->belongsTo(Iuran::class);
    }

    /**
     * Relasi ke tagihan (jika ini pembayaran bulanan).
     */
    public function tagihan(): BelongsTo
    {
        return $this->belongsTo(Tagihan::class);
    }

    /**
     * Relasi ke tunggakan (jika ini pembayaran bebas).
     */
    public function tunggakan(): BelongsTo
    {
        return $this->belongsTo(Tunggakan::class);
    }

    /**
     * Relasi ke petugas (GTK) yang mencatat pembayaran.
     */
    public function petugas(): BelongsTo
    {
        return $this->belongsTo(Gtk::class, 'petugas_id');
    }

    /**
     * Relasi ke master kas tujuan dana.
     */
    public function masterKas(): BelongsTo
    {
        return $this->belongsTo(MasterKas::class);
    }
}
