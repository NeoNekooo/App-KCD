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
    Schema::table('tipe_surats', function (Blueprint $table) {
        // Menambahkan kolom boolean dengan default 0 (mati)
        $table->boolean('use_kop')->default(0)->after('ukuran_kertas');
    });
}

public function down()
{
    Schema::table('tipe_surats', function (Blueprint $table) {
        $table->dropColumn('use_kop');
    });
}
};
