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
        Schema::create('profil_sekolahs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama_instansi')->nullable();
            $table->string('singkatan')->nullable();
            $table->string('status')->nullable();
            $table->string('kode')->nullable();
            $table->string('nama_kepala_sekolah')->nullable();
            $table->string('nip_kepala_sekolah')->nullable();
            $table->string('bidang_studi')->nullable();
            $table->string('tahun_berdiri')->nullable();
            $table->string('nss')->nullable();
            $table->string('npsn')->nullable();
            $table->string('luas')->nullable();
            $table->text('moto')->nullable();
            $table->string('logo')->nullable();
            $table->string('icon')->nullable();
            $table->text('alamat')->nullable();
            $table->string('desa')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kabupaten')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kode_pos')->nullable();
            $table->string('telepon')->nullable();
            $table->string('faximile')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->text('embed_peta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profil_sekolahs');
    }
};
