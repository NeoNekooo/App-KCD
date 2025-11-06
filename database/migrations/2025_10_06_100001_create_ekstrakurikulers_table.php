<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('ekstrakurikulers', function (Blueprint $table) {
        $table->id();
        $table->string('nama_ekskul');

        // PASTIKAN BARIS INI ADA DAN SUDAH BENAR
        $table->unsignedBigInteger('pembina_id')->nullable();

        $table->string('prasarana')->nullable();
        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('ekstrakurikulers');
    }
};