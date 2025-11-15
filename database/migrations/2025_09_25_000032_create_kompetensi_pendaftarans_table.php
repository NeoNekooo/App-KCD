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
        Schema::create('kompetensi_pendaftarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahunPelajaran_id')->constrained('tahun_pelajarans')->onDelete('cascade');
            $table->string('kode')->nullable();
            $table->string('kompetensi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kompetensi_pendaftarans');
    }
};
