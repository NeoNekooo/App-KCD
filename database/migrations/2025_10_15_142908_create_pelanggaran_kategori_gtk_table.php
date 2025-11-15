<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pelanggaran_kategori_gtk', function (Blueprint $table) {
            $table->id('ID');
            $table->string('nama');
            $table->boolean('status')->default(1); // aktif/nonaktif
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelanggaran_kategori_gtk');
    }
};
