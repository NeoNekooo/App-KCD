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
        Schema::create('absensi_mapel', function (Blueprint $table) {
    $table->id();
    
    // Kunci utama dari absensi ini
    $table->foreignId('jadwal_pelajaran_id')->constrained('jadwal_pelajaran')->onDelete('cascade');
    $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
    $table->date('tanggal');
    
    // Status absensi di kelas
    $table->enum('status', ['Hadir', 'Sakit', 'Izin', 'Alfa']);
    $table->text('keterangan')->nullable();
    
    // Siapa yang mencatat (guru)
    $table->foreignId('dicatat_oleh_gtk_id')->nullable()->constrained('gtks')->onDelete('set null');
    
    $table->timestamps();

    // Menjamin tidak ada data duplikat untuk siswa, jadwal, dan tanggal yang sama
    $table->unique(['jadwal_pelajaran_id', 'siswa_id', 'tanggal'], 'absensi_mapel_unique');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_mapel');
    }
};
