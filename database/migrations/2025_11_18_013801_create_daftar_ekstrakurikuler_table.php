<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daftar_ekstrakurikuler', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->string('alias')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daftar_ekstrakurikuler');
    }
};
