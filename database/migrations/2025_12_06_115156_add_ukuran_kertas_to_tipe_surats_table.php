<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::table('tipe_surats', function (Blueprint $table) {
        $table->string('ukuran_kertas')->default('A4')->after('template_isi');
    });
}

public function down()
{
    Schema::table('tipe_surats', function (Blueprint $table) {
        $table->dropColumn('ukuran_kertas');
    });
}

};
