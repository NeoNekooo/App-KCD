<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penggajian extends Model
{
    use HasFactory;

    protected $table = 'penggajians';
    protected $fillable = [
        'gtk_id', 'tahun_pelajaran_id', 'periode_bulan', 'jumlah_gaji_bersih',
        'status_pembayaran', 'tanggal_dibayar', 'master_kas_id', 'acc_jurnal_id',
        'keterangan'
    ];

    /**
     * Relasi ke GTK (guru/pegawai) yang menerima gaji.
     */
    public function gtk(): BelongsTo
    {
        return $this->belongsTo(Gtk::class, 'gtk_id');
    }

    /**
     * Relasi ke tahun pelajaran saat penggajian terjadi.
     */
    public function tahunPelajaran(): BelongsTo
    {
        return $this->belongsTo(TahunPelajaran::class, 'tahun_pelajaran_id');
    }

    /**
     * Relasi ke master kas sumber dana.
     */
    public function masterKas(): BelongsTo
    {
        return $this->belongsTo(MasterKas::class);
    }
}
