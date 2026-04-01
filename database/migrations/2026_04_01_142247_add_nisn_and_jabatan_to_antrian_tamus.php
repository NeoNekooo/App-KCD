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
            $table->string('nisn', 20)->nullable()->after('nama');
            $table->string('jabatan_pengunjung', 100)->nullable()->after('asal_instansi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('antrian_tamus', function (Blueprint $table) {
            $table->dropColumn(['nisn', 'jabatan_pengunjung']);
        });
    }
};
