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
            // Ubah kolom 'status' menjadi VARCHAR(50) (atau angka yang cukup besar, misal 20)
            $table->string('status', 50)->change();
        });
    }

    public function down()
    {
        Schema::table('tagihans', function (Blueprint $table) {
            // Kembalikan ke panjang sebelumnya jika diperlukan
            // $table->string('status', 10)->change();
        });
    }
};
