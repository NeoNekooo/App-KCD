<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalonSiswa extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun_id', 
        'jalur_id', 
        'nis',
        'nomor_resi',
        'nama_lengkap', 
        'nisn', 
        'npun',
        'jenis_kelamin', 
        'tempat_lahir', 
        'tgl_lahir', 
        'nama_ayah', 
        'nama_ibu',
        'alamat', 
        'desa', 
        'kecamatan', 
        'kabupaten', 
        'provinsi',
        'kode_pos', 
        'kontak', 
        'asal_sekolah', 
        'kelas',
        'kelas_tujuan',
        'jurusan', 
        'ukuran_pakaian', 
        'pembayaran',
        'alamat_lengkap',
        'status'
    ];

    // Relasi ke Tahun Pelajaran
    public function tahunPpdb()
    {
        return $this->belongsTo(TahunPelajaran::class, 'tahun_id');
    }

    // Relasi ke Jalur Pendaftaran
    public function jalurPendaftaran()
    {
        return $this->belongsTo(JalurPendaftaran::class, 'jalur_id');
    }

    // Relasi ke syarat (many-to-many)
    public function syarat()
    {
        return $this->belongsToMany(SyaratPendaftaran::class, 'calon_siswa_syarat', 'calon_siswa_id', 'syarat_id')
                    ->withPivot('is_checked')
                    ->withTimestamps();
    }

}
