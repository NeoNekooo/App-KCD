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
    Schema::table('gtks', function (Blueprint $table) {
        $table->string('sk_mengajar')->nullable()->after('nama');
    });
}

    /**
     * Reverse the migrations.p
     */
    public function down(): void
    {
        Schema::table('gtks', function (Blueprint $table) {
            //
        });
    }
};
