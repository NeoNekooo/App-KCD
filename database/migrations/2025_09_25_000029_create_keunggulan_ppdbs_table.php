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
        Schema::create('keunggulan_ppdbs', function (Blueprint $table) {
            $table->id();
            // Judul dan deskripsi untuk section keunggulan
            $table->string('judul_keunggulan');        // Contoh: "Keunggulan SMAKNIS Sebagai SMK Pilihan"
            $table->text('deskripsi_keunggulan');      // Contoh: "Kami berkomitmen ..."
                
            // Data tiap item
            $table->string('icon')->nullable();
            $table->string('judul_item');              // Contoh: "Fasilitas Lengkap"
            $table->text('deskripsi_item');            // Contoh: "Laboratorium dan ruang praktik modern"
                
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keunggulan_ppdbs');
    }
};
