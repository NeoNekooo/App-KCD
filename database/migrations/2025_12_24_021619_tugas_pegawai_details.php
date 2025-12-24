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
        Schema::create('tugas_pegawai_details', function (Blueprint $table) {
    $table->id();
    $table->foreignId('tugas_pegawai_id')->constrained('tugas_pegawais')->onDelete('cascade');
    $table->string('tugas_pokok'); // Nama Mapel atau Jabatan
    $table->string('kelas')->nullable(); // Nama Rombel
    $table->integer('jumlah_jam')->default(0);
    $table->string('jenis'); // 'pembelajaran' atau 'struktural'
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
