<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQrTokenToGtksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gtks', function (Blueprint $table) {
            // Menambahkan kolom setelah 'nip' (Anda bisa sesuaikan posisinya)
            $table->string('qr_token')->nullable()->unique()->after('nip');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gtks', function (Blueprint $table) {
            $table->dropColumn('qr_token');
        });
    }
}