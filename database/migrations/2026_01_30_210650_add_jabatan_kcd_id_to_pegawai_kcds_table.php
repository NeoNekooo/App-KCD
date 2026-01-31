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
        Schema::table('pegawai_kcds', function (Blueprint $table) {
            $table->foreignId('jabatan_kcd_id')->nullable()->after('jabatan')->constrained('jabatan_kcd')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pegawai_kcds', function (Blueprint $table) {
            $table->dropForeign(['jabatan_kcd_id']);
            $table->dropColumn('jabatan_kcd_id');
        });
    }
};