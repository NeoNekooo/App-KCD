<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paket_keahlians', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama_paket');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paket_keahlians');
    }
};
