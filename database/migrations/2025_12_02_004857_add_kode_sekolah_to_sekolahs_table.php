<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            // Menambahkan kolom kode_sekolah, nullable (boleh kosong)
            // diletakkan setelah kolom 'npsn' (atau sesuaikan dengan kolom yang ada)
            $table->string('kode_sekolah')->nullable()->after('npsn'); 
        });
    }

    public function down()
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->dropColumn('kode_sekolah');
        });
    }
};