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
    Schema::table('instansis', function (Blueprint $table) {
        $table->string('nama_brand')->nullable()->after('nama_instansi'); // "KCD ENAM"
        $table->text('peta')->nullable()->after('alamat'); // Iframe Google Maps
        $table->json('social_media')->nullable()->after('website'); // JSON untuk FB, IG, dll
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instansis', function (Blueprint $table) {
            //
        });
    }
};
