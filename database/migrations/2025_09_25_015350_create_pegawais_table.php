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
        Schema::create('pegawais', function (Blueprint $table) {
            $table->id();

            // Data Utama
            $table->string('nama_lengkap');
            $table->string('gelar_depan')->nullable();
            $table->string('gelar_belakang')->nullable();
            $table->string('nip')->unique()->nullable();
            $table->string('nik')->unique()->nullable();
            $table->string('niy_nigk')->unique()->nullable();
            $table->string('nuptk')->unique()->nullable();
            $table->string('npwp')->nullable();

            // Data Pribadi
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->string('agama')->nullable();
            $table->string('kewarganegaraan')->nullable();
            $table->string('nama_ibu_kandung')->nullable();
            $table->string('status_pernikahan')->nullable();
            $table->string('nama_pasangan')->nullable();
            $table->integer('jumlah_anak')->nullable();
            
            // Alamat & Kontak
            $table->text('alamat')->nullable();
            $table->string('desa')->nullable(); // Ditambahkan
            $table->string('kecamatan')->nullable(); // Ditambahkan
            $table->string('kabupaten')->nullable(); // Ditambahkan
            $table->string('provinsi')->nullable(); // Ditambahkan
            $table->string('kode_pos')->nullable(); // Ditambahkan
            $table->string('kontak')->nullable();

            // Kepegawaian
            $table->string('tipe_pegawai')->nullable();
            $table->string('status')->default('Aktif');

            // File
            $table->string('foto')->nullable();
            $table->string('tanda_tangan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawais');
    }
};

