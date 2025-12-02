<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tagihans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('siswa_id'); // Sebelumnya constrained
            $table->unsignedBigInteger('iuran_id'); // Sebelumnya constrained
            $table->unsignedBigInteger('tahun_pelajaran_id'); // Sebelumnya constrained
            $table->string('periode', 10)->nullable();
            $table->unsignedBigInteger('jumlah_tagihan');
            $table->unsignedBigInteger('sisa_tagihan');
            $table->enum('status', ['Belum Bayar', 'Cicilan', 'Lunas'])->default('Belum Bayar');
            $table->timestamps();

            $table->unique(['siswa_id', 'iuran_id', 'periode']);
        });
    }
    public function down(): void { Schema::dropIfExists('tagihans'); }
};

