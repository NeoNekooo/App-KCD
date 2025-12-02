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
        Schema::create('tunggakans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('siswa_id');
            $table->unsignedBigInteger('iuran_id');
            $table->unsignedBigInteger('tahun_pelajaran_id');
            $table->unsignedBigInteger('total_tunggakan_awal');
            $table->unsignedBigInteger('sisa_tunggakan');
            $table->enum('status', ['Belum Lunas', 'Lunas'])->default('Belum Lunas');
            $table->timestamps();

            $table->unique(['siswa_id', 'iuran_id', 'tahun_pelajaran_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tunggakans');
    }
};

