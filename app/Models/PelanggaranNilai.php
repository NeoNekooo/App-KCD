<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PelanggaranNilai extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model.
     *
     * @var string
     */
    protected $table = 'pelanggaran_nilai';

    /**
     * Primary key untuk model ini.
     *
     * @var string
     */
    protected $primaryKey = 'ID';

    /**
     * Menunjukkan apakah model harus memiliki timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Atribut yang dapat diisi secara massal.
     * (INI ADALAH VERSI YANG SUDAH DIPERBAIKI)
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nipd',
        'semester_id', // String (cth: "20251")
        'rombongan_belajar_id',
        'IDpelanggaran_poin',
        'tanggal',
        'jam',
        'poin',
        'pembelajaran', // String (ID Mapel)
    ];

    /**
     * Relasi ke model Siswa (Dapodik).
     * Menghubungkan pelanggaran_nilai.nipd -> siswas.nipd
     */
    public function siswa(): BelongsTo
    {
        // Ganti 'App\Models\Siswa' jika path model Siswa Anda berbeda
        return $this->belongsTo(Siswa::class, 'nipd', 'nipd');
    }

    /**
     * Relasi ke model Rombel (Dapodik).
     * Menghubungkan pelanggaran_nilai.rombongan_belajar_id -> rombels.id
     */
    public function rombel(): BelongsTo
    {
        // Ganti 'App\Models\Rombel' jika path model Rombel Anda berbeda
        return $this->belongsTo(Rombel::class, 'rombongan_belajar_id', 'id');
    }

    /**
     * Relasi ke model PelanggaranPoin (dari menu Pengaturan).
     * Menghubungkan pelanggaran_nilai.IDpelanggaran_poin -> pelanggaran_poin.ID
     */
    public function detailPoinSiswa(): BelongsTo
    {
        // Asumsi nama modelnya adalah PelanggaranPoin, sesuaikan jika beda
        return $this->belongsTo(PelanggaranPoin::class, 'IDpelanggaran_poin', 'ID');
    }
}