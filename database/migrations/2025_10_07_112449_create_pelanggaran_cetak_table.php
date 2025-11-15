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
        Schema::create('pelanggaran_cetak', function (Blueprint $table) {
            $table->id('ID');
            $table->dateTime('tanggal_cetak')->nullable();
            $table->string('NIS', 50)->nullable()->index();
            $table->unsignedTinyInteger('IDtapel')->nullable();
            $table->unsignedTinyInteger('IDsemester')->nullable();
            $table->integer('poin_pelanggaran')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelanggaran_cetak');
    }
};