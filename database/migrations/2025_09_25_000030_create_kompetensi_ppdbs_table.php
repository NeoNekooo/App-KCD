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
        Schema::create('kompetensi_ppdbs', function (Blueprint $table) {
            $table->id();
            // Judul dan deskripsi untuk section kompetensi
            $table->string('judul_kompetensi');        // Contoh: "Pilihan Kompetensi Keahlian"
            $table->text('deskripsi_kompetensi');      // Contoh: "Temukan minat dan bakat Anda ..."

            // Data tiap item
            $table->string('icon')->nullable();
            $table->string('kode_kompetensi');         // Contoh: "TKJ"
            $table->string('nama_kompetensi');         // Contoh: "Teknik Komputer dan Jaringan"
            $table->text('deskripsi_jurusan');         // Contoh: "Jurusan ini mempersiapkan siswa ..."

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kompetensi_ppdbs');
    }
};
