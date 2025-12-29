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
    Schema::table('sambutan_kepala_sekolahs', function (Blueprint $table) {
        $table->text('sejarah')->nullable()->after('isi_sambutan');
        $table->string('foto_gedung')->nullable()->after('foto');
    });
}

public function down()
{
    Schema::table('sambutan_kepala_sekolahs', function (Blueprint $table) {
        $table->dropColumn(['sejarah', 'foto_gedung']);
    });
}
};