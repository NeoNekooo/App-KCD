<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tugas_pegawai_kcds', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke Tabel Pegawai KCD
            // (Pastikan tabel 'pegawai_kcds' sudah ada ya)
            $table->foreignId('pegawai_kcd_id')
                  ->constrained('pegawai_kcds')
                  ->onDelete('cascade');
            
            $table->string('nama_tugas'); // Contoh: Verifikator Mutasi
            
            // KOLOM PENTING: Menentukan Pegawai ini megang layanan apa
            // Nullable: Kalau dia cuma staf biasa/tugas umum, kosongin aja.
            $table->string('kategori_layanan')->nullable(); 

            $table->string('no_sk')->nullable();
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tugas_pegawai_kcds');
    }
};