<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('mutasi_keluar', function (Blueprint $table) {
        $table->id();
        // morphs akan menciptakan 'keluarable_id' dan 'keluarable_type'
        // Ini memungkinkan tabel ini merujuk ke model Siswa atau GTK
        $table->morphs('keluarable');
        $table->date('tanggal_keluar');
        $table->string('status'); // Contoh: Lulus, Pindah, Keluar, Pensiun
        $table->text('keterangan')->nullable();
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutasi_keluar');
    }
};
