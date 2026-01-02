<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('ekskul', function (Blueprint $table) {
        $table->string('tempat')->nullable()->after('jadwal'); // Lokasi Latihan
        $table->string('status')->default('Aktif')->after('tempat'); // Aktif / Buka Pendaftaran
    });
}

public function down()
{
    Schema::table('ekskul', function (Blueprint $table) {
        $table->dropColumn(['tempat', 'status']);
    });
}
};
