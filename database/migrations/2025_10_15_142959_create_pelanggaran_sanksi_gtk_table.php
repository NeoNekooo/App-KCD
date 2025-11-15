<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pelanggaran_sanksi_gtk', function (Blueprint $table) {
            $table->id('ID');
            $table->integer('poin_min')->default(0);
            $table->integer('poin_max')->default(0);
            $table->string('nama');
            $table->string('penindak')->nullable(); // contoh: Kepala Sekolah|Waka Kesiswaan
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelanggaran_sanksi_gtk');
    }
};
