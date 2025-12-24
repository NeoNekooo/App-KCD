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
    Schema::create('mitras', function (Blueprint $table) {
        $table->id();
        $table->string('nama_mitra'); // Nama Perusahaan/Instansi
        $table->string('logo');       // Logo Perusahaan
        $table->string('bidang_kerjasama')->nullable(); // Contoh: Tempat PKL / Rekrutmen
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mitras');
    }
};
