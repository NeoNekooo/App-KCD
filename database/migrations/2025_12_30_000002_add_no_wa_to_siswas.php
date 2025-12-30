<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds a nullable `no_wa` column to the `siswas` table.
     */
    public function up(): void
    {
        if (Schema::hasTable('siswas')) {
            Schema::table('siswas', function (Blueprint $table) {
                if (!Schema::hasColumn('siswas', 'no_wa')) {
                    $table->string('no_wa', 32)->nullable()->after('nomor_telepon_seluler');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('siswas')) {
            Schema::table('siswas', function (Blueprint $table) {
                if (Schema::hasColumn('siswas', 'no_wa')) {
                    $table->dropColumn('no_wa');
                }
            });
        }
    }
};
