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
        Schema::create('antrian_tamus', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_antrian')->unique(); // Ex: A-001, B-002
            $table->string('nama');
            $table->string('nik', 20)->nullable();
            $table->string('asal_instansi')->nullable(); // Asal sekolah / institusi
            $table->text('keperluan');
            $table->foreignId('tujuan_pegawai_id')->nullable()->constrained('pegawai_kcds')->nullOnDelete();
            
            // Status Enum: menunggu -> dipanggil -> selesai / batal
            $table->enum('status', ['menunggu', 'dipanggil', 'selesai', 'batal'])->default('menunggu');
            $table->integer('jumlah_panggilan')->default(0); // Counter brapa kali resepsionis mencet "Panggil"
            
            $table->timestamp('waktu_panggilan')->nullable(); // Waktu terkahir dipanggil
            $table->timestamp('waktu_selesai')->nullable(); // Waktu saat diselesaikan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('antrian_tamus');
    }
};
