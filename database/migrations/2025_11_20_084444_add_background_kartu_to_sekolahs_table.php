<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::table('sekolahs', function (Blueprint $table) {
        // Menambah kolom background_kartu
        $table->string('background_kartu')->nullable()->after('logo');
    });
}

public function down()
{
    Schema::table('sekolahs', function (Blueprint $table) {
        $table->dropColumn('background_kartu');
    });
}
};
