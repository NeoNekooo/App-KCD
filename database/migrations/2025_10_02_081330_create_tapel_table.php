<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tapel', function (Blueprint $table) {
            $table->id();
            $table->string('kode_tapel')->unique();       // Contoh: 20251
            $table->string('tahun_ajaran');               // Contoh: 2025/2026
            $table->enum('semester', ['Ganjil', 'Genap']);
            $table->boolean('is_active')->default(false); // Tapel aktif atau tidakeb
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tapel');
    }
};
