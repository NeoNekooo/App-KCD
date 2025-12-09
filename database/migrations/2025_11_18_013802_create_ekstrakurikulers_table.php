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
        // Pastikan tabel tidak ada sebelum dibuat (opsional, untuk safety)
        Schema::dropIfExists('ekstrakurikulers');

        Schema::create('ekstrakurikulers', function (Blueprint $table) {
            $table->id();
            
            // Kolom Relasi ke Tabel Master (daftar_ekstrakurikuler)
            // Pastikan nama tabel 'daftar_ekstrakurikuler' sudah benar ada
            $table->foreignId('daftar_ekstrakurikuler_id')
                  ->constrained('daftar_ekstrakurikuler')
                  ->onDelete('cascade');

            // Kolom Relasi ke Guru/Pembina (gtks)
            // Pastikan nama tabel 'gtks' sudah benar ada
            $table->foreignId('pembina_id')
                  ->nullable()
                  ->constrained('gtks')
                  ->onDelete('set null'); // Jika guru dihapus, data ekskul tetap ada tapi pembinanya null

            $table->string('prasarana')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ekstrakurikulers');
    }
};