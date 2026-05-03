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
        Schema::table('pkks_instrumens', function (Blueprint $table) {
            $table->unsignedBigInteger('instansi_id')->nullable()->after('id');
            $table->index('instansi_id');
        });

        Schema::table('pkks_penilaians', function (Blueprint $table) {
            $table->unsignedBigInteger('instansi_id')->nullable()->after('id');
            $table->index('instansi_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pkks_instrumens', function (Blueprint $table) {
            $table->dropColumn('instansi_id');
        });

        Schema::table('pkks_penilaians', function (Blueprint $table) {
            $table->dropColumn('instansi_id');
        });
    }
};
