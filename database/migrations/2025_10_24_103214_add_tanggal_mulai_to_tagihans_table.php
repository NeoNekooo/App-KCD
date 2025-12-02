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
    Schema::table('tagihans', function (Blueprint $table) {
        $table->date('tanggal_mulai')->nullable()->after('periode'); // Tambahkan kolom
    });
}

public function down()
{
    Schema::table('tagihans', function (Blueprint $table) {
        $table->dropColumn('tanggal_mulai');
    });
}
};
