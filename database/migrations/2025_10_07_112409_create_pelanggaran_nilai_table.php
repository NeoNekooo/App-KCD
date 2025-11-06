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
        Schema::create('pelanggaran_nilai', function (Blueprint $table) {
            $table->id('ID');
            // Disesuaikan dengan relasi ke `nipd` di tabel `siswas`
            $table->string('NIS', 50)->index(); 
            $table->unsignedTinyInteger('IDtapel');
            $table->unsignedTinyInteger('IDsemester');
            $table->unsignedBigInteger('IDkelas'); // Merujuk ke `rombels.id`
            $table->unsignedBigInteger('IDpelanggaran_poin');
            $table->date('tanggal')->nullable();
            $table->string('jam', 10)->nullable();
            $table->integer('poin')->nullable();
            $table->unsignedTinyInteger('IDmapel')->nullable();
            
            // Relasi
            // Asumsi `NIS` merujuk ke `nipd` di tabel `siswas`
            $table->foreign('IDpelanggaran_poin')->references('ID')->on('pelanggaran_poin')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelanggaran_nilai');
    }
};