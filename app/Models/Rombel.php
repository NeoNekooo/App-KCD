<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rombel extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung.
     *
     * @var string
     */
    protected $table = 'rombels';

    /**
     * Primary key untuk model ini.
     *
     * @var string
     */
    protected $primaryKey = 'id'; // Sesuai screenshot Anda

    /**
     * Relasi ke semua Siswa di Rombel ini.
     * Menghubungkan rombels.rombongan_belajar_id (Varchar) -> siswas.rombongan_belajar_id (Varchar)
     */
    public function siswa(): HasMany
    {
        return $this->hasMany(Siswa::class, 'rombongan_belajar_id', 'rombongan_belajar_id');
    }

    /**
     * Relasi ke semua data pelanggaran yang terjadi di rombel ini.
     * Menghubungkan rombels.id (PK) -> pelanggaran_nilai.rombongan_belajar_id (FK)
     */
    public function pelanggaran(): HasMany
    {
        return $this->hasMany(PelanggaranNilai::class, 'rombongan_belajar_id', 'id');
    }
}