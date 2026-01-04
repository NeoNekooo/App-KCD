<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne; // [PENTING: Import ini ditambahkan]
use Illuminate\Support\Facades\Storage;

use App\Models\AlumniTestimoni;
use App\Models\TracerStudy;
use App\Models\Pengguna; // [Opsional: Import model Pengguna]

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswas';
    protected $primaryKey = 'id';

    // =================================================================
    // KONFIGURASI UUID
    // =================================================================
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        // ID & Kunci Utama
        'id', 'peserta_didik_id', 'registrasi_id', 'qr_token',

        // Identitas Pribadi
        'nama',
        'nipd',
        'nisn',
        'nik',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama_id',
        'agama_id_str',
        'email',
        'nomor_telepon_rumah',
        'nomor_telepon_seluler',
        'no_wa',
        'tinggi_badan',
        'berat_badan',
        'kebutuhan_khusus',
        'anak_keberapa',

        // Alamat
        'alamat_jalan',
        'rt',
        'rw',
        'nama_dusun',
        'dusun',
        'desa_kelurahan',
        'kecamatan',
        'kabupaten_kota',
        'provinsi',
        'kode_pos',
        'lintang',
        'bujur',
        'kode_wilayah',

        // Data Orang Tua & Wali
        'nama_ayah', 'pekerjaan_ayah_id', 'pekerjaan_ayah_id_str',
        'nama_ibu', 'pekerjaan_ibu_id', 'pekerjaan_ibu_id_str',
        'nama_wali', 'pekerjaan_wali_id', 'pekerjaan_wali_id_str',

        // Data Akademik & Sekolah
        'sekolah_asal',
        'tanggal_masuk_sekolah',
        'jenis_pendaftaran_id',
        'jenis_pendaftaran_id_str',
        'semester_id',
        'anggota_rombel_id',
        'rombongan_belajar_id',
        'tingkat_pendidikan_id',
        'nama_rombel',
        'kurikulum_id',
        'kurikulum_id_str',
        'status',

        // File Foto
        'foto',

        // TAMBAHAN BARU DARI FORMULIR EXCEL
        'npsn_sekolah_asal', 'no_seri_ijazah', 'no_seri_skhun', 'no_ujian_nasional', 'no_registrasi_akta_lahir',
        'no_kks', 'penerima_kps', 'no_kps', 'layak_pip', 'alasan_layak_pip', 'penerima_kip', 'no_kip', 'nama_di_kip', 'alasan_menolak_kip',
        'tahun_lahir_ayah', 'pendidikan_ayah_id_str', 'penghasilan_ayah_id_str', 'kebutuhan_khusus_ayah',
        'tahun_lahir_ibu', 'pendidikan_ibu_id_str', 'penghasilan_ibu_id_str', 'kebutuhan_khusus_ibu',
        'tahun_lahir_wali', 'pendidikan_wali_id_str', 'penghasilan_wali_id_str',
        'alat_transportasi_id_str', 'jenis_tinggal_id_str', 'jarak_rumah_ke_sekolah_km', 'waktu_tempuh_menit', 'jumlah_saudara_kandung',
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

    // =================================================================
    // RELASI DATABASE
    // =================================================================

    /**
     * Relasi ke Pengguna (PENTING: Untuk Filter Sekolah di KCD)
     */
    public function pengguna(): HasOne
    {
        return $this->hasOne(Pengguna::class, 'peserta_didik_id', 'peserta_didik_id');
    }

    public function tracer(): HasOne
    {
        return $this->hasOne(TracerStudy::class, 'siswa_id', 'id');
    }

    public function testimoni(): HasOne
    {
        return $this->hasOne(AlumniTestimoni::class, 'siswa_id', 'id');
    }

    public function rombel(): BelongsTo
    {
        return $this->belongsTo(Rombel::class, 'rombongan_belajar_id', 'rombongan_belajar_id');
    }

    public function pelanggaran(): HasMany
    {
        return $this->hasMany(PelanggaranNilai::class, 'nipd', 'nipd');
    }

    public function mutasiKeluar()
    {
        return $this->morphOne(MutasiKeluar::class, 'keluarable');
    }

    // --- Relasi Keuangan ---
    public function tagihans() { return $this->hasMany(Tagihan::class); }
    public function tunggakans() { return $this->hasMany(Tunggakan::class); }
    public function pembayarans() { return $this->hasMany(Pembayaran::class); }
    public function vouchers() { return $this->hasMany(Voucher::class); }

    /**
     * Relasi many-to-many ke Rombel melalui tabel pivot `anggota_rombel`.
     */
    public function rombels()
    {
        return $this->belongsToMany(Rombel::class, 'anggota_rombel', 'siswa_id', 'rombel_id')
                    ->withPivot(['peserta_didik_id','anggota_rombel_id','jenis_pendaftaran_id'])
                    ->withTimestamps();
    }

    // =================================================================
    // ACCESSOR & SCOPE
    // =================================================================

    public function isAlumni()
    {
        return strtolower($this->status) === 'lulus';
    }

    public function getFotoUrlAttribute()
    {
        if ($this->foto && Storage::disk('public')->exists($this->foto)) {
            return Storage::disk('public')->url($this->foto);
        }
        return asset('assets/img/avatars/default.png');
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'Aktif');
    }
}