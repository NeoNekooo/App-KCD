<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    use HasFactory;

    // Nama tabel sesuai di database 'miaw' kamu
    protected $table = 'sekolahs';

    // Semua kolom yang bisa diisi (sesuai struktur SQL sekolahs.sql)
    protected $fillable = [
        'sekolah_id',
        'nama',
        'nss',
        'npsn',
        'kode_sekolah',
        'bentuk_pendidikan_id',
        'bentuk_pendidikan_id_str',
        'status_sekolah',
        'status_sekolah_str',
        'alamat_jalan',
        'rt',
        'rw',
        'kode_wilayah',
        'kode_pos',
        'nomor_telepon',
        'nomor_fax',
        'email',
        'website',
        'is_sks',
        'lintang',
        'bujur',
        'dusun',
        'desa_kelurahan',
        'kecamatan',
        'kabupaten_kota',
        'provinsi',
        'logo',
        'background_kartu',
        'peta',
        'social_media'
    ];

    /**
     * Casting otomatis kolom JSON biar langsung jadi array di Laravel
     */
    protected $casts = [
        'social_media' => 'array',
    ];

    /**
     * RELASI KE TABEL PENGGUNA
     * Dipakai di SekolahController buat hitung total Siswa, Guru, Tendik, & Kepsek.
     */
    public function pengguna()
    {
        // Relasi ke model Pengguna berdasarkan 'sekolah_id' (UUID)
        return $this->hasMany(Pengguna::class, 'sekolah_id', 'sekolah_id');
    }
}