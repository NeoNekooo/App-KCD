<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_create_agendas_table.php

public function up()
{
    Schema::create('agendas', function (Blueprint $table) {
        $table->id();
        $table->string('judul');
        
        // PERBAIKAN 1: Samakan nama kolom dengan Controller (tanggal_mulai)
        $table->date('tanggal_mulai'); 
        
        // PERBAIKAN 2: Samakan nama kolom dengan Controller (tanggal_selesai)
        $table->date('tanggal_selesai')->nullable(); 
        
        // PERBAIKAN 3: Tambahkan kolom kategori (karena di form & controller ada input kategori)
        $table->string('kategori'); 
        
        $table->text('deskripsi')->nullable();
        
        // Kolom tambahan (Opsional, biarkan saja jika ingin dikembangkan nanti)
        $table->string('slug')->nullable();
        $table->string('lokasi')->nullable();
        $table->time('jam_mulai')->nullable();
        $table->time('jam_selesai')->nullable();
        $table->enum('status', ['upcoming', 'finished'])->default('upcoming');
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agendas');
    }
};
