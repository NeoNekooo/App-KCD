<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pelanggaran_poin_gtk', function (Blueprint $table) {
            $table->id('ID');
            $table->unsignedBigInteger('IDpelanggaran_kategori');
            $table->string('nama');
            $table->integer('poin')->default(0);
            $table->text('tindakan')->nullable();
            $table->boolean('status')->default(1);

            $table->foreign('IDpelanggaran_kategori')
                  ->references('ID')
                  ->on('pelanggaran_kategori_gtk')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelanggaran_poin_gtk');
    }
};
