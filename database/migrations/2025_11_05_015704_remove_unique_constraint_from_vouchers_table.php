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
    Schema::table('vouchers', function (Blueprint $table) {
        $table->dropUnique('vouchers_siswa_id_tahun_pelajaran_id_unique');
    });
}

public function down()
{
    Schema::table('vouchers', function (Blueprint $table) {
        $table->unique(['siswa_id', 'tahun_pelajaran_id']);
    });
}

};
