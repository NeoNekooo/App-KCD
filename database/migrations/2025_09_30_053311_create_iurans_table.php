<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('iurans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_iuran', 100);
            $table->enum('tipe_iuran', ['Bulanan', 'Bebas']);
            $table->unsignedBigInteger('besaran_default')->nullable();
            $table->unsignedBigInteger('tahun_pelajaran_id'); // Sebelumnya constrained
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('iurans'); }
};

