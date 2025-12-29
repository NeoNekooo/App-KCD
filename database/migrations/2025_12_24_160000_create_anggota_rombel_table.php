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
        Schema::create('anggota_rombel', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rombel_id')->index();
            $table->unsignedBigInteger('siswa_id')->nullable()->index();
            $table->string('peserta_didik_id')->nullable()->index();
            $table->string('anggota_rombel_id')->nullable()->index();
            $table->string('jenis_pendaftaran_id')->nullable();
            $table->timestamps();

            $table->foreign('rombel_id')->references('id')->on('rombels')->onDelete('cascade');
            $table->foreign('siswa_id')->references('id')->on('siswas')->onDelete('cascade');

            $table->unique(['rombel_id', 'siswa_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anggota_rombel');
    }
};
