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
        Schema::create('tracer_studies', function (Blueprint $table) {
            $table->id();

            // 1. Ganti jadi string dan tambahkan index agar cepat
            $table->string('siswa_id')->index();

            $table->string('kegiatan_setelah_lulus'); 
            $table->string('nama_instansi')->nullable(); 
            $table->string('jabatan_posisi')->nullable(); 
            $table->year('tahun_lulus')->nullable();
            
            $table->timestamps();

            // 2. HAPUS atau KOMENTARI bagian ini agar tidak error Foreign Key
            // $table->foreign('siswa_id')->references('id')->on('siswas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracer_studies');
    }
};