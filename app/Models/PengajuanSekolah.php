<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Tambah ini buat relasi

class PengajuanSekolah extends Model
{
    use HasFactory;

    // Guarded ID aman, semua kolom baru otomatis bisa diinput
    protected $guarded = ['id'];

    // Tambahkan casting datetime agar dianggap Objek Carbon otomatis
    protected $casts = [
        'dokumen_syarat' => 'array',
        'acc_admin_at'   => 'datetime',
        'acc_kasubag_at' => 'datetime',
        'acc_kepala_at'  => 'datetime',
        
        // [TAMBAHAN PENTING]
        'tgl_selesai'    => 'date', // Biar enak format tanggal SK di view
    ];

    /**
     * [BARU] Relasi ke Template Surat
     * Fungsinya: Biar pas cetak bisa panggil $pengajuan->template->ukuran_kertas
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(TipeSurat::class, 'template_id');
    }
}