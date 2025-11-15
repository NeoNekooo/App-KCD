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
        Schema::create('pelanggaran_poin', function (Blueprint $table) {
            $table->id('ID');
            $table->unsignedBigInteger('IDpelanggaran_kategori');
            $table->text('nama');
            $table->integer('poin');
            $table->text('tindakan');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();

            $table->foreign('IDpelanggaran_kategori')->references('ID')->on('pelanggaran_kategori')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelanggaran_poin');
    }
};