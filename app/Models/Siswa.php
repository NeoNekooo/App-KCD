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
     * PENYESUAIAN 2: Menggunakan $fillable untuk keamanan dan kejelasan.
     * Daftarkan semua kolom yang boleh diisi melalui form atau sinkronisasi.
     */
    protected $fillable = [
    'peserta_didik_id', 'registrasi_id', 'jenis_pendaftaran_id', 'jenis_pendaftaran_id_str',
        'nipd', 'tanggal_masuk_sekolah', 'sekolah_asal', 'nama', 'nisn', 'jenis_kelamin',
        'nik', 'tempat_lahir', 'tanggal_lahir', 'agama_id', 'agama_id_str',
        'nomor_telepon_rumah', 'nomor_telepon_seluler', 'nama_ayah', 'pekerjaan_ayah_id',
        'pekerjaan_ayah_id_str', 'nama_ibu', 'pekerjaan_ibu_id', 'pekerjaan_ibu_id_str',
        'nama_wali', 'pekerjaan_wali_id', 'pekerjaan_wali_id_str', 'anak_keberapa',
        'tinggi_badan', 'berat_badan', 'email', 'semester_id', 'anggota_rombel_id',
        'rombongan_belajar_id', 'tingkat_pendidikan_id', 'nama_rombel', 'kurikulum_id',
        'kurikulum_id_str', 'kebutuhan_khusus',
        'status',
        'foto',
    ];

    protected $casts = [
        'riwayat_penyakit' => 'array',
        'data_ayah' => 'array',
        'data_ibu' => 'array',
        'data_wali_laki' => 'array',
        'data_wali_perempuan' => 'array',
        'kepribadian' => 'array',
        'prestasi' => 'array',
        'tanggal_lahir' => 'date',
        'tanggal_masuk_sekolah' => 'date',
    ];

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



    public function getFotoUrlAttribute()
    {
        if ($this->foto && Storage::disk('public')->exists($this->foto)) {
            return Storage::disk('public')->url($this->foto);
        }
        return asset('images/default-avatar.png');
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'Aktif');
    }


    // --- Relasi Modul Keuangan (Sudah Benar) ---
    public function tagihans() { return $this->hasMany(Tagihan::class); }
    public function tunggakans() { return $this->hasMany(Tunggakan::class); }
    public function pembayarans() { return $this->hasMany(Pembayaran::class); }
    public function vouchers() { return $this->hasMany(Voucher::class); }
}