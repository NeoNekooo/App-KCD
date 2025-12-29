<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::table('tipe_surats', function (Blueprint $table) {
        // Tambahkan kolom margin dengan default value
        $table->integer('margin_top')->default(20);
        $table->integer('margin_right')->default(25);
        $table->integer('margin_bottom')->default(0);
        $table->integer('margin_left')->default(25);
    });
}

public function down()
{
    Schema::table('tipe_surats', function (Blueprint $table) {
        $table->dropColumn(['margin_top', 'margin_right', 'margin_bottom', 'margin_left']);
    });
}
};
