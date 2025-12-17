<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('siswas', function (Blueprint $table) {
            // A. Dokumen & Sekolah Asal
            $table->string('npsn_sekolah_asal')->nullable();
            $table->string('no_seri_ijazah')->nullable();
            $table->string('no_seri_skhun')->nullable();
            $table->string('no_ujian_nasional')->nullable();
            $table->string('no_registrasi_akta_lahir')->nullable();

            // B. Kesejahteraan
            $table->string('no_kks')->nullable();
            $table->string('penerima_kps')->nullable(); // Ya/Tidak
            $table->string('no_kps')->nullable();
            $table->string('layak_pip')->nullable();    // Ya/Tidak
            $table->string('alasan_layak_pip')->nullable();
            $table->string('penerima_kip')->nullable(); // Ya/Tidak
            $table->string('no_kip')->nullable();
            $table->string('nama_di_kip')->nullable();
            $table->string('alasan_menolak_kip')->nullable();

            // C. Detail Ortu & Wali
            $table->string('tahun_lahir_ayah')->nullable();
            $table->string('pendidikan_ayah_id_str')->nullable();
            $table->string('penghasilan_ayah_id_str')->nullable();
            $table->string('kebutuhan_khusus_ayah')->nullable();

            $table->string('tahun_lahir_ibu')->nullable();
            $table->string('pendidikan_ibu_id_str')->nullable();
            $table->string('penghasilan_ibu_id_str')->nullable();
            $table->string('kebutuhan_khusus_ibu')->nullable();

            $table->string('tahun_lahir_wali')->nullable();
            $table->string('pendidikan_wali_id_str')->nullable();
            $table->string('penghasilan_wali_id_str')->nullable();

            // D. Data Periodik & Lingkungan
            $table->string('alat_transportasi_id_str')->nullable();
            $table->string('jenis_tinggal_id_str')->nullable();
            $table->string('jarak_rumah_ke_sekolah_km')->nullable();
            $table->string('waktu_tempuh_menit')->nullable();
            $table->integer('jumlah_saudara_kandung')->nullable();
        });
    }

    public function down()
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn([
                'npsn_sekolah_asal', 'no_seri_ijazah', 'no_seri_skhun', 'no_ujian_nasional', 'no_registrasi_akta_lahir',
                'no_kks', 'penerima_kps', 'no_kps', 'layak_pip', 'alasan_layak_pip', 'penerima_kip', 'no_kip', 'nama_di_kip', 'alasan_menolak_kip',
                'tahun_lahir_ayah', 'pendidikan_ayah_id_str', 'penghasilan_ayah_id_str', 'kebutuhan_khusus_ayah',
                'tahun_lahir_ibu', 'pendidikan_ibu_id_str', 'penghasilan_ibu_id_str', 'kebutuhan_khusus_ibu',
                'tahun_lahir_wali', 'pendidikan_wali_id_str', 'penghasilan_wali_id_str',
                'alat_transportasi_id_str', 'jenis_tinggal_id_str', 'jarak_rumah_ke_sekolah_km', 'waktu_tempuh_menit', 'jumlah_saudara_kandung'
            ]);
        });
    }
};