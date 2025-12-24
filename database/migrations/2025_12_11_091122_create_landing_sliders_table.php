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
    Schema::create('landing_sliders', function (Blueprint $table) {
        $table->id();
        $table->string('judul')->nullable();        // Judul besar di slider
        $table->text('deskripsi')->nullable();      // Teks kecil di bawah judul
        $table->string('gambar');                   // Menyimpan nama file gambar
        $table->string('tombol_teks')->nullable();  // Contoh: "Daftar Sekarang"
        $table->string('tombol_url')->nullable();   // Link tujuan tombol
        $table->integer('urutan')->default(0);      // Untuk mengatur urutan tampil
        $table->boolean('status')->default(true);   // Aktif/Tidak Aktif
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_sliders');
    }
};
