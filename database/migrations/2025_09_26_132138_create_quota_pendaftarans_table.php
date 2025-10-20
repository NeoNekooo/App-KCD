<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quota_pendaftarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahunPelajaran_id') // relasi ke tabel tahun_pelajarans
                  ->constrained('tahun_pelajarans')
                  ->onDelete('cascade');
            $table->string('keahlian')->nullable();         // misal jurusan/kompetensi
            $table->integer('jumlah_kelas')->nullable();    // jumlah kelas
            $table->integer('quota')->nullable();           // quota per kelas / total
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quota_pendaftarans');
    }
};
