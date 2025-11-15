<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Siswa extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung.
     *
     * @var string
     */
    protected $table = 'siswas';

    /**
     * Primary key untuk model ini.
     *
     * @var string
     */
    protected $primaryKey = 'id'; // Sesuai screenshot Anda

    /**
     * Relasi ke Rombel (Dapodik).
     * Menghubungkan siswas.rombongan_belajar_id (Varchar) -> rombels.rombongan_belajar_id (Varchar)
     */
    public function rombel(): BelongsTo
    {
        return $this->belongsTo(Rombel::class, 'rombongan_belajar_id', 'rombongan_belajar_id');
    }

    /**
     * Relasi ke semua data pelanggaran milik siswa ini.
     * Menghubungkan siswas.nipd -> pelanggaran_nilai.nipd
     */
    public function pelanggaran(): HasMany
    {
        return $this->hasMany(PelanggaranNilai::class, 'nipd', 'nipd');
    }
}