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
    Schema::create('sambutan_kepala_sekolahs', function (Blueprint $table) {
        $table->id();
        $table->string('nama_kepala_sekolah')->nullable();
        $table->string('foto')->nullable();
        $table->string('judul_sambutan')->nullable();
        $table->text('isi_sambutan')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sambutan_kepala_sekolahs');
    }
};
