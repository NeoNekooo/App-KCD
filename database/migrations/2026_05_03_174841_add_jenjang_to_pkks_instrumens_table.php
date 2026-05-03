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
            $table->enum('jenjang', ['SMA', 'SMK', 'SLB'])->default('SMA')->after('nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pkks_instrumens', function (Blueprint $table) {
            $table->dropColumn('jenjang');
        });
    }
};
