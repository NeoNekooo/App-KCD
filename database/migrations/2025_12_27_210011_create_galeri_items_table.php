<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('galeri_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('galeri_id')->constrained('galeris')->onDelete('cascade'); // Terhubung ke album
            $table->string('file'); // Nama file
            $table->enum('jenis', ['foto', 'video']); // Penanda tipe file
            $table->string('caption')->nullable(); // Opsional
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('galeri_items');
    }
};