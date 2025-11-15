<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class IzinSiswa extends Model
    {
        use HasFactory;

        protected $table = 'izin_siswa';

        // Kolom yang boleh diisi secara massal untuk keamanan
        protected $fillable = [
            'siswa_id',
            'tanggal_izin',
            'tipe_izin',
            'jam_izin_mulai',
            'jam_izin_selesai',
            'alasan',
            'status',
            'dicatat_oleh',
            'token_sementara',
            'waktu_keluar',
            'waktu_kembali',
        ];

        /**
         * Mendefinisikan relasi ke model Siswa.
         * Setiap izin dimiliki oleh satu siswa.
         */
        public function siswa()
        {
            return $this->belongsTo(Siswa::class);
        }

        /**
         * Mendefinisikan relasi ke model User (guru/admin yang mencatat).
         * Setiap izin dicatat oleh satu user.
         */
        public function pencatat()
        {
            return $this->belongsTo(User::class, 'dicatat_oleh');
        }
    }
