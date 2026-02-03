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
        Schema::table('tugas_pegawai_kcds', function (Blueprint $table) {
            $table->json('kategori_layanan')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tugas_pegawai_kcds', function (Blueprint $table) {
            $table->string('kategori_layanan')->nullable()->change();
        });
    }
};
