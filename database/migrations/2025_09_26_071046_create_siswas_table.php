<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        public function up(): void
        {
            Schema::create('siswas', function (Blueprint $table) {
                $table->id();
                
                // TAB: Pribadi
                $table->string('foto')->nullable();
                $table->string('nis')->unique()->nullable();
                $table->string('nisn')->unique()->nullable();
                $table->string('nik')->unique()->nullable();
                $table->string('no_akta_lahir')->nullable(); // Ditambahkan
                $table->string('no_seri_pip')->nullable(); // Ditambahkan
                $table->string('pkh_kks')->nullable(); // Ditambahkan
                $table->string('kps')->nullable(); // Ditambahkan
                $table->string('nama_lengkap');
                $table->string('nama_panggilan')->nullable();
                $table->string('jenis_kelamin')->nullable();
                $table->string('tempat_lahir')->nullable();
                $table->date('tanggal_lahir')->nullable();
                $table->string('agama')->nullable();
                $table->string('kewarganegaraan')->nullable();
                $table->string('golongan_darah')->nullable();
                $table->string('bahasa_sehari_hari')->nullable();
                $table->integer('anak_ke')->nullable();
                $table->integer('jumlah_saudara_kandung')->nullable();
                $table->integer('jumlah_saudara_tiri')->nullable();
                $table->integer('jumlah_saudara_angkat')->nullable();
                $table->string('status_dalam_keluarga')->nullable();
                $table->string('kategori_anak')->nullable();

                // TAB: Tempat Tinggal
                $table->text('alamat')->nullable();
                $table->string('rt')->nullable();
                $table->string('rw')->nullable();
                $table->string('kecamatan')->nullable();
                $table->string('desa')->nullable();
                $table->string('kode_pos')->nullable();
                $table->string('kabupaten')->nullable();
                $table->string('provinsi')->nullable();
                $table->string('telepon')->nullable();
                $table->string('tinggal_dengan')->nullable();
                $table->string('jarak_ke_sekolah')->nullable();
                $table->string('transportasi_ke_sekolah')->nullable();

                // TAB: Jasmani & Riwayat Penyakit
                $table->string('berat_badan')->nullable();
                $table->string('tinggi_badan')->nullable();
                $table->text('kelainan_jasmani')->nullable();
                $table->json('riwayat_penyakit')->nullable(); // Simpan sebagai JSON

                // TAB: Pendidikan
                $table->string('kelas_aktif_id')->nullable(); // Relasi ke tabel kelas
                $table->string('kelas_diterima')->nullable();
                $table->date('tanggal_diterima')->nullable();
                $table->string('no_peserta_un')->nullable();
                $table->string('nama_smp_asal')->nullable();
                $table->string('tahun_ijazah_asal')->nullable();
                $table->string('no_seri_ijazah_asal')->nullable();
                $table->string('tahun_skhun_asal')->nullable();
                $table->string('nama_sd_asal')->nullable();

                // TAB: Ortu/Wali (Kita buat dalam format JSON untuk fleksibilitas)
                $table->json('data_ayah')->nullable();
                $table->json('data_ibu')->nullable();
                $table->json('data_wali_laki')->nullable();
                $table->json('data_wali_perempuan')->nullable();

                // TAB: Intelegensi
                $table->string('iq')->nullable();
                $table->date('tanggal_tes_iq')->nullable();
                $table->json('kepribadian')->nullable(); // Simpan aspek kepribadian sebagai JSON

                // TAB: Prestasi
                $table->json('prestasi')->nullable(); // Simpan prestasi sebagai JSON

                $table->timestamps();
            });
        }

        public function down(): void
        {
            Schema::dropIfExists('siswas');
        }
    };