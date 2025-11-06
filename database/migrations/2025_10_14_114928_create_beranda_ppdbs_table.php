<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('beranda_ppdbs', function (Blueprint $table) {
            $table->id();
            $table->string('slogan_utama');
            $table->text('deskripsi_singkat');
            $table->string('point_keunggulan_1')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beranda_ppdbs');
    }
};
