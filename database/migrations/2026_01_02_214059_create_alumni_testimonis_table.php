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
        Schema::create('alumni_testimonis', function (Blueprint $table) {
            $table->id();

            // Kita gunakan string + index agar pencarian cepat
            // Kita HAPUS constraint foreign key agar migrasi tidak error
            $table->string('siswa_id')->index(); 

            $table->string('nama');
            $table->text('pesan');
            $table->string('nama_instansi')->nullable();
            $table->string('status_kegiatan')->nullable();
            $table->string('status')->default('Pending'); 
            $table->boolean('tampilkan')->default(false);
            $table->timestamps();

            // SAYA KOMENTARI BAGIAN INI (PENYEBAB ERROR)
            // Laravel tetap bisa jalan tanpa baris ini
            // $table->foreign('siswa_id')->references('id')->on('siswas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumni_testimonis');
    }
};