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
        Schema::table('instansis', function (Blueprint $table) {
            $table->double('lintang', 10, 7)->nullable()->after('alamat');
            $table->double('bujur', 10, 7)->nullable()->after('lintang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instansis', function (Blueprint $table) {
            $table->dropColumn('lintang');
            $table->dropColumn('bujur');
        });
    }
};
