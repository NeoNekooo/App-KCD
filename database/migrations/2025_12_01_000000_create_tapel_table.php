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
        Schema::create('tapel', function (Blueprint $table) {
            $table->id();
            $table->string('kode_tapel', 20)->unique();
            $table->string('tahun_ajaran', 20);
            $table->string('semester', 20); // Ganjil / Genap
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tapel');
    }
};
