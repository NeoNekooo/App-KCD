<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('siswas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('qr_token')->nullable()->unique();
            $table->string('registrasi_id', 191)->nullable()->index();
            $table->string('jenis_pendaftaran_id', 191)->nullable()->index();
            $table->text('jenis_pendaftaran_id_str')->nullable();
            $table->text('nipd')->nullable();
            $table->date('tanggal_masuk_sekolah')->nullable();
            $table->text('sekolah_asal')->nullable();
            $table->string('peserta_didik_id', 191)->nullable()->index();
            $table->text('nama')->nullable();
            $table->string('foto')->nullable();
            $table->text('nisn')->nullable();
            $table->text('jenis_kelamin')->nullable();
            $table->text('nik')->nullable();
            $table->text('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('agama_id', 191)->nullable()->index();
            $table->text('agama_id_str')->nullable();
            $table->text('nomor_telepon_rumah')->nullable();
            $table->text(column: 'nomor_telepon_seluler')->nullable();
            $table->text('nama_ayah')->nullable();
            $table->string('pekerjaan_ayah_id', 191)->nullable()->index();
            $table->text('pekerjaan_ayah_id_str')->nullable();
            $table->text('nama_ibu')->nullable();
            $table->string('pekerjaan_ibu_id', 191)->nullable()->index();
            $table->text('pekerjaan_ibu_id_str')->nullable();
            $table->text('nama_wali')->nullable();
            $table->string('pekerjaan_wali_id', 191)->nullable()->index();
            $table->text('pekerjaan_wali_id_str')->nullable();
            $table->text('anak_keberapa')->nullable();
            $table->text('tinggi_badan')->nullable();
            $table->text('berat_badan')->nullable();
            $table->text('email')->nullable();
            $table->string('semester_id', 191)->nullable()->index();
            $table->string('anggota_rombel_id', 191)->nullable()->index();
            $table->string('rombongan_belajar_id', 191)->nullable()->index();
            $table->string('tingkat_pendidikan_id', 191)->nullable()->index();
            $table->text('nama_rombel')->nullable();
            $table->string('kurikulum_id', 191)->nullable()->index();
            $table->text('kurikulum_id_str')->nullable();
            $table->text('kebutuhan_khusus')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};
