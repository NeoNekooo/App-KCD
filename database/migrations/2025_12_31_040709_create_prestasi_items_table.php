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
    Schema::create('prestasi_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('prestasi_id')->constrained('prestasis')->onDelete('cascade');
        $table->string('file'); // Nama file foto
        $table->string('caption')->nullable();
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('prestasi_items');
}
};
