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
        if (!Schema::hasTable('cadisdiks')) {
            Schema::create('cadisdiks', function (Blueprint $table) {
                $table->uuid('id')->primary(); // Menggunakan UUID agar sinkron dengan SIAKAD
                $table->string('nama');
                $table->string('kode')->nullable();
                $table->text('keterangan')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cadisdiks');
    }
};
