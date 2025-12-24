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
    Schema::create('testimonis', function (Blueprint $table) {
        $table->id();
        $table->string('nama');
        $table->string('status'); // Ini Jabatan (Misal: Alumni 2020)
        $table->text('isi');
        $table->string('foto')->nullable();
        
        // KOLOM BARU: Status Tayang (Default False/0 artinya Pending)
        $table->boolean('is_published')->default(false); 
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testimonis');
    }
};
