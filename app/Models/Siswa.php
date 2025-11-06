<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

        protected $guarded = ['id'];

        protected $casts = [
            'riwayat_penyakit' => 'array',
            'data_ayah' => 'array',
            'data_ibu' => 'array',
            'data_wali_laki' => 'array',
            'data_wali_perempuan' => 'array',
            'kepribadian' => 'array',
            'prestasi' => 'array',
        ];

        public function rombel()
    {
        // Menghubungkan kolom 'rombongan_belajar_id' di tabel ini
        // dengan kolom 'rombongan_belajar_id' (bukan 'id') di tabel 'rombels'
        return $this->belongsTo(Rombel::class, 'rombongan_belajar_id', 'rombongan_belajar_id');
    }
    }