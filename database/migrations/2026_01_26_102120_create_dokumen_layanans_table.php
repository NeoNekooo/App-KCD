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
        Schema::create('dokumen_layanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_sekolah_id')->constrained('pengajuan_sekolahs')->onDelete('cascade');
            $table->string('nama_dokumen');
            $table->string('path_dokumen');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_layanans');
    }
};
