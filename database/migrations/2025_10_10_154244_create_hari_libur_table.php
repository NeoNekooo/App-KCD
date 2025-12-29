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
        Schema::create('hari_libur', function (Blueprint $table) {
    $table->id();
    $table->string('keterangan');
    $table->date('tanggal_mulai');
    $table->date('tanggal_selesai'); // Untuk rentang tanggal
    $table->enum('tipe', ['global', 'khusus'])->default('global'); // global = semua kelas, khusus = pilih kelas
    $table->timestamps();
});

// Migration: create_hari_libur_rombel_table (Tabel Pivot)
Schema::create('hari_libur_rombel', function (Blueprint $table) {
    $table->foreignId('hari_libur_id')->constrained('hari_libur')->onDelete('cascade');
    $table->foreignId('rombel_id')->constrained('rombels')->onDelete('cascade'); // Asumsi tabel rombel bernama 'rombels'
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hari_libur');
    }
};
