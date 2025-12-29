<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('alumni_data', function (Blueprint $table) {
        $table->id();
        // Kunci penghubung ke tabel siswa (UUID string sesuai file sql anda)
        $table->string('peserta_didik_id')->unique(); 
        
        // --- DATA TESTIMONI ---
        $table->text('testimoni')->nullable();
        
        // --- DATA TRACER STUDY ---
        $table->string('status_kegiatan')->nullable(); // Bekerja, Kuliah, dll
        $table->string('nama_instansi')->nullable();   // Nama PT/Kampus
        $table->string('jabatan_jurusan')->nullable(); // Jabatan atau Jurusan
        $table->year('tahun_mulai')->nullable();
        $table->string('pendapatan')->nullable();
        $table->string('linieritas')->nullable(); // Ya/Tidak
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumni_data');
    }
};
