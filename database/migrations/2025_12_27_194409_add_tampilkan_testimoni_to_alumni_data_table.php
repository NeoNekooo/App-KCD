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
        Schema::table('alumni_data', function (Blueprint $table) {
            // Tambahkan kolom boolean dengan default 0 (Draft)
            // Letakkan setelah kolom testimoni agar rapi (opsional)
            $table->boolean('tampilkan_testimoni')->default(0)->after('testimoni');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alumni_data', function (Blueprint $table) {
            $table->dropColumn('tampilkan_testimoni');
        });
    }
};