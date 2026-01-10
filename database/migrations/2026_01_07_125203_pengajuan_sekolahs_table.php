<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pengajuan_sekolahs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique(); // ID Unik dari Sekolah (KTP Surat)
            
            // Data Identitas
            $table->string('npsn');
            $table->string('nama_sekolah');
            $table->string('nama_guru');
            $table->string('nip')->nullable();
            
            // Data Surat
            $table->string('kategori');
            $table->string('judul');
            $table->string('file_path'); // Lokasi File PDF
            
            // Data Teknis (Callback)
            $table->string('url_callback'); // Alamat buat lapor balik ke sekolah
            
            // Status Verifikasi
            $table->enum('status', ['Proses', 'ACC', 'Ditolak'])->default('Proses');
            $table->text('alasan_tolak')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pengajuan_sekolahs');
    }
};