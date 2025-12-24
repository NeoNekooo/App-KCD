<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('jam_pelajarans', function (Blueprint $table) {
            $table->id();
            // Hari: Senin, Selasa, dst. Penting karena jam ke-1 Jumat beda dengan Senin.
            $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']);

            // Urutan jam ke berapa (1, 2, 3, dst).
            $table->integer('urutan');

            // Nama slot: "Jam Ke-1", "Istirahat", "Upacara", "Sholat Jumat"
            $table->string('nama');

            // Waktu mulai dan selesai
            $table->time('jam_mulai');
            $table->time('jam_selesai');

            // Tipe slot. Penting untuk drag-and-drop nanti (Istirahat tidak bisa di-drop mapel)
            $table->enum('tipe', ['kbm', 'istirahat', 'upacara', 'lainnya'])->default('kbm');

            $table->timestamps();

            // Mencegah duplikasi urutan di hari yang sama
            // (Tidak boleh ada dua "urutan 1" di hari "Senin")
            $table->unique(['hari', 'urutan']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('jam_pelajarans');
    }
};
