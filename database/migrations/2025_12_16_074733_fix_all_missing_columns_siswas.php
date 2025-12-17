<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('siswas', function (Blueprint $table) {

            // --- 1. ALAMAT (Yang tadi error hilang) ---
            if (!Schema::hasColumn('siswas', 'alamat_jalan')) $table->string('alamat_jalan')->nullable();
            if (!Schema::hasColumn('siswas', 'rt')) $table->string('rt', 10)->nullable();
            if (!Schema::hasColumn('siswas', 'rw')) $table->string('rw', 10)->nullable();
            if (!Schema::hasColumn('siswas', 'dusun')) $table->string('dusun')->nullable();
            if (!Schema::hasColumn('siswas', 'nama_dusun')) $table->string('nama_dusun')->nullable();
            if (!Schema::hasColumn('siswas', 'desa_kelurahan')) $table->string('desa_kelurahan')->nullable();
            if (!Schema::hasColumn('siswas', 'kecamatan')) $table->string('kecamatan')->nullable();
            if (!Schema::hasColumn('siswas', 'kabupaten_kota')) $table->string('kabupaten_kota')->nullable();
            if (!Schema::hasColumn('siswas', 'provinsi')) $table->string('provinsi')->nullable();
            if (!Schema::hasColumn('siswas', 'kode_pos')) $table->string('kode_pos', 10)->nullable();
            if (!Schema::hasColumn('siswas', 'lintang')) $table->string('lintang')->nullable();
            if (!Schema::hasColumn('siswas', 'bujur')) $table->string('bujur')->nullable();
            if (!Schema::hasColumn('siswas', 'jenis_tinggal_id_str')) $table->string('jenis_tinggal_id_str')->nullable();

            // --- 2. DOKUMEN & SEKOLAH ASAL (Cek lagi jaga-jaga) ---
            if (!Schema::hasColumn('siswas', 'npsn_sekolah_asal')) $table->string('npsn_sekolah_asal')->nullable();
            if (!Schema::hasColumn('siswas', 'no_seri_ijazah')) $table->string('no_seri_ijazah')->nullable();
            if (!Schema::hasColumn('siswas', 'no_seri_skhun')) $table->string('no_seri_skhun')->nullable();
            if (!Schema::hasColumn('siswas', 'no_ujian_nasional')) $table->string('no_ujian_nasional')->nullable();
            if (!Schema::hasColumn('siswas', 'no_registrasi_akta_lahir')) $table->string('no_registrasi_akta_lahir')->nullable();

            // --- 3. KESEJAHTERAAN ---
            if (!Schema::hasColumn('siswas', 'no_kks')) $table->string('no_kks')->nullable();
            if (!Schema::hasColumn('siswas', 'penerima_kps')) $table->string('penerima_kps')->nullable();
            if (!Schema::hasColumn('siswas', 'no_kps')) $table->string('no_kps')->nullable();
            if (!Schema::hasColumn('siswas', 'layak_pip')) $table->string('layak_pip')->nullable();
            if (!Schema::hasColumn('siswas', 'alasan_layak_pip')) $table->string('alasan_layak_pip')->nullable();
            if (!Schema::hasColumn('siswas', 'penerima_kip')) $table->string('penerima_kip')->nullable();
            if (!Schema::hasColumn('siswas', 'no_kip')) $table->string('no_kip')->nullable();
            if (!Schema::hasColumn('siswas', 'nama_di_kip')) $table->string('nama_di_kip')->nullable();
            if (!Schema::hasColumn('siswas', 'alasan_menolak_kip')) $table->string('alasan_menolak_kip')->nullable();

            // --- 4. DATA ORANG TUA ---
            // Ayah
            if (!Schema::hasColumn('siswas', 'tahun_lahir_ayah')) $table->string('tahun_lahir_ayah')->nullable();
            if (!Schema::hasColumn('siswas', 'pendidikan_ayah_id_str')) $table->string('pendidikan_ayah_id_str')->nullable();
            if (!Schema::hasColumn('siswas', 'penghasilan_ayah_id_str')) $table->string('penghasilan_ayah_id_str')->nullable();
            if (!Schema::hasColumn('siswas', 'kebutuhan_khusus_ayah')) $table->string('kebutuhan_khusus_ayah')->nullable();

            // Ibu
            if (!Schema::hasColumn('siswas', 'tahun_lahir_ibu')) $table->string('tahun_lahir_ibu')->nullable();
            if (!Schema::hasColumn('siswas', 'pendidikan_ibu_id_str')) $table->string('pendidikan_ibu_id_str')->nullable();
            if (!Schema::hasColumn('siswas', 'penghasilan_ibu_id_str')) $table->string('penghasilan_ibu_id_str')->nullable();
            if (!Schema::hasColumn('siswas', 'kebutuhan_khusus_ibu')) $table->string('kebutuhan_khusus_ibu')->nullable();

            // Wali
            if (!Schema::hasColumn('siswas', 'tahun_lahir_wali')) $table->string('tahun_lahir_wali')->nullable();
            if (!Schema::hasColumn('siswas', 'pendidikan_wali_id_str')) $table->string('pendidikan_wali_id_str')->nullable();
            if (!Schema::hasColumn('siswas', 'penghasilan_wali_id_str')) $table->string('penghasilan_wali_id_str')->nullable();

            // --- 5. TRANSPORTASI & LAINNYA ---
            if (!Schema::hasColumn('siswas', 'alat_transportasi_id_str')) $table->string('alat_transportasi_id_str')->nullable();
            if (!Schema::hasColumn('siswas', 'jarak_rumah_ke_sekolah_km')) $table->string('jarak_rumah_ke_sekolah_km')->nullable();
            if (!Schema::hasColumn('siswas', 'waktu_tempuh_menit')) $table->string('waktu_tempuh_menit')->nullable();
            if (!Schema::hasColumn('siswas', 'jumlah_saudara_kandung')) $table->integer('jumlah_saudara_kandung')->nullable();
        });
    }

    public function down()
    {
        // Kosongkan agar aman
    }
};