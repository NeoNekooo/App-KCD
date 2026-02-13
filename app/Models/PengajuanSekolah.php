<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PengajuanSekolah extends Model
{
    use HasFactory;

    /**
     * Guarded ID aman, semua kolom baru otomatis bisa diinput.
     * Termasuk kolom 'tipe_pengaju', 'data_siswa_json', dll.
     */
    protected $guarded = ['id'];

    /**
     * Casting attribute agar otomatis menjadi objek yang tepat saat diakses.
     */
    protected $casts = [
        'dokumen_syarat'  => 'array',
        'data_siswa_json' => 'array',    // 🔥 Simpan snapshot biodata siswa dari sekolah
        'acc_admin_at'    => 'datetime',
        'acc_kasubag_at'  => 'datetime',
        'acc_kepala_at'   => 'datetime',
        'tgl_selesai'     => 'date',       // Mudahkan format tanggal SK di view
    ];

    /**
     * Helper: Cek apakah pengajuan ini milik Peserta Didik (PD)
     */
    public function isPd(): bool
    {
        return $this->tipe_pengaju === 'PD';
    }

    /**
     * Relasi ke Template Surat
     * Digunakan saat proses cetak SK untuk mengambil margin, ukuran kertas, dll.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(TipeSurat::class, 'template_id');
    }

    /**
     * Relasi ke file fisik yang sudah di-download oleh Background Job (Arsip)
     * Memberikan akses ke tabel dokumen_layanans.
     */
    public function dokumenLayanan(): HasMany
    {
        return $this->hasMany(DokumenLayanan::class, 'pengajuan_sekolah_id');
    }
}