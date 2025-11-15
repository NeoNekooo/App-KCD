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
        Schema::create('pelanggaran_nilai', function (Blueprint $table) {
            $table->id('ID');
            $table->string('nipd', 50)->index(); // Sesuai asli (links ke siswas.nipd)
            
            // --- PERUBAHAN UTAMA DISINI ---
            
            // HAPUS: $table->unsignedTinyInteger('tapel'); 
            // HAPUS: $table->unsignedTinyInteger('semester');
            
            // UBAH: Menyimpan ID semester dari Dapodik (cth: "20251")
            $table->string('semester_id', 10)->index(); 

            // --- SELESAI PERUBAHAN ---
            
            $table->unsignedBigInteger('rombongan_belajar_id'); // Merujuk ke rombels.id (PK)
            $table->unsignedBigInteger('IDpelanggaran_poin');
            $table->date('tanggal')->nullable();
            $table->string('jam', 10)->nullable();
            $table->integer('poin')->nullable();
            
            // UBAH: Menyimpan ID Mapel (string/uuid) dari JSON, bukan angka kecil
            $table->string('pembelajaran', 191)->nullable();
            
            // Relasi
            $table->foreign('IDpelanggaran_poin')->references('ID')->on('pelanggaran_poin')->onDelete('cascade');
            
            // Tambahkan foreign key (opsional tapi disarankan)
            // $table->foreign('rombongan_belajar_id')->references('id')->on('rombels');
            // $table->foreign('nipd')->references('nipd')->on('siswas'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelanggaran_nilai');
    }
};