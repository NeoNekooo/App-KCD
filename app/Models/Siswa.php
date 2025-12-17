<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;


class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswas';
    protected $primaryKey = 'id';

    // =================================================================
    // [PERBAIKAN 1] KONFIGURASI UUID
    // =================================================================
    // Wajib ada karena ID di database Anda tipe CHAR(36)/UUID.
    // Tanpa ini, Laravel akan menganggap ID adalah angka (Integer) dan Update akan Gagal.
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * Daftar kolom yang boleh diisi (Mass Assignment).
     * Sudah disesuaikan dengan siswas (2).sql
     */
    protected $fillable = [
        // ID & Kunci Utama
        'id', 'peserta_didik_id', 'registrasi_id', 'qr_token',
        
        // Identitas Pribadi
        'nama', 
        'nipd', // Database pakai nipd, bukan nis
        'nisn', 
        'nik', 
        'jenis_kelamin',
        'tempat_lahir', 
        'tanggal_lahir', 
        'agama_id', 
        'agama_id_str',
        // 'kewarganegaraan', // (Hapus komentar jika kolom ini nanti ditambahkan ke DB, saat ini di SQL tidak ada)
        'email', 
        'nomor_telepon_rumah', 
        'nomor_telepon_seluler',
        'tinggi_badan', 
        'berat_badan', 
        'kebutuhan_khusus', 
        
        // [PERBAIKAN 2] Penulisan kolom ini salah di model lama
        'anak_keberapa', // SEBELUMNYA: anak_ke_berapa (Salah)
        
        // Alamat (Lengkap sesuai SQL)
        'alamat_jalan', 
        'rt', 
        'rw', 
        'nama_dusun', // Kadang dapodik pakai ini
        'dusun',      // Kadang pakai ini, kita masukkan dua-duanya biar aman
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
        'riwayat_penyakit' => 'array', // Pastikan kolom ini ada di DB atau hapus jika tidak
        'data_ayah' => 'array',
        'data_ibu' => 'array',
        'data_wali_laki' => 'array',
        'data_wali_perempuan' => 'array',
        'kepribadian' => 'array',
        'prestasi' => 'array',
        'tanggal_lahir' => 'date',
        'tanggal_masuk_sekolah' => 'date',
    ];

    // --- RELASI ---

    public function rombel(): BelongsTo
    {
        return $this->belongsTo(Rombel::class, 'rombongan_belajar_id', 'rombongan_belajar_id');
    }

    public function pelanggaran(): HasMany
    {
        return $this->hasMany(PelanggaranNilai::class, 'nipd', 'nipd');
    }

    public function getFotoUrlAttribute()
    {
        if ($this->foto && Storage::disk('public')->exists($this->foto)) {
            return Storage::disk('public')->url($this->foto);
        }
        // Pastikan path image default benar
        return asset('assets/img/avatars/default.png'); 
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'Aktif');
    }

    // --- Relasi Keuangan ---
    public function tagihans() { return $this->hasMany(Tagihan::class); }
    public function tunggakans() { return $this->hasMany(Tunggakan::class); }
    public function pembayarans() { return $this->hasMany(Pembayaran::class); }
    public function vouchers() { return $this->hasMany(Voucher::class); }
}