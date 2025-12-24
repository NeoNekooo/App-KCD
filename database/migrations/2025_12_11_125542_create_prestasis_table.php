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
    Schema::create('prestasis', function (Blueprint $table) {
        $table->id();
        $table->string('judul');          // Contoh: Juara 1 Lomba Web Design
        $table->string('nama_pemenang');  // Contoh: Ahmad Dani
        $table->enum('tingkat', ['Sekolah', 'Kecamatan', 'Kabupaten', 'Provinsi', 'Nasional', 'Internasional']);
        $table->text('deskripsi')->nullable();
        $table->string('foto');           // Foto penyerahan piala/sertifikat
        $table->date('tanggal')->nullable(); // Tanggal kejadian
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestasis');
    }
};
