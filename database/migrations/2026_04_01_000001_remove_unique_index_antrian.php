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
        Schema::table('antrian_tamus', function (Blueprint $table) {
            // Drop unique index to allow daily numbering reset (A-001 can exist on different days)
            $table->dropUnique(['nomor_antrian']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('antrian_tamus', function (Blueprint $table) {
            $table->unique('nomor_antrian');
        });
    }
};
