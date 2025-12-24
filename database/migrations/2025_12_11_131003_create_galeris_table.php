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
    Schema::create('galeris', function (Blueprint $table) {
        $table->id();
        $table->string('judul');      // Nama Kegiatan / Album
        $table->date('tanggal')->nullable(); // Tanggal Kegiatan
        $table->text('deskripsi')->nullable();
        $table->string('foto');       // Foto Cover Album
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('galeris');
    }
};
