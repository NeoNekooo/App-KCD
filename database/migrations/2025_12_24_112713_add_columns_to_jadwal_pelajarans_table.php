<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('jadwal_pelajarans', function (Blueprint $table) {
            // Kolom penghubung ke Master Jam (Waktu)
            $table->foreignId('jam_pelajaran_id')->nullable()->constrained('jam_pelajarans')->onDelete('cascade');

            // Kolom penghubung ke Master Pembelajaran (Guru & Mapel)
            $table->foreignId('pembelajaran_id')->nullable()->constrained('pembelajarans')->onDelete('cascade');

            // Kita buat kolom lama jadi nullable dulu biar tidak error saat migrasi
            // Nanti kolom-kolom string manual ini perlahan tidak dipakai lagi
            $table->string('mata_pelajaran')->nullable()->change();
            $table->string('ptk_id')->nullable()->change(); // Jika tipe data di database Anda string/uuid
            $table->string('hari')->nullable()->change();
            $table->time('jam_mulai')->nullable()->change();
            $table->time('jam_selesai')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('jadwal_pelajarans', function (Blueprint $table) {
            $table->dropForeign(['jam_pelajaran_id']);
            $table->dropForeign(['pembelajaran_id']);
            $table->dropColumn(['jam_pelajaran_id', 'pembelajaran_id']);
        });
    }
};
