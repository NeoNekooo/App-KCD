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
    Schema::create('instansis', function (Blueprint $table) {
        $table->id();
        $table->string('nama_instansi')->default('KCD Wilayah X');
        $table->string('nama_kepala')->nullable();
        $table->string('nip_kepala')->nullable();
        $table->text('alamat')->nullable();
        $table->string('email')->nullable();
        $table->string('telepon')->nullable();
        $table->string('website')->nullable();
        $table->string('logo')->nullable(); // Untuk upload foto
        $table->text('visi')->nullable();
        $table->text('misi')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instansis');
    }
};
