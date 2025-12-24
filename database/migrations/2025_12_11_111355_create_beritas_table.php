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
    Schema::create('beritas', function (Blueprint $table) {
        $table->id();
        $table->string('judul');
        $table->string('slug')->unique(); // Untuk URL SEO friendly
        $table->text('ringkasan')->nullable(); // Cuplikan pendek
        $table->longText('isi'); // Isi lengkap berita
        $table->string('gambar'); // Thumbnail
        $table->string('penulis')->default('Admin');
        $table->enum('status', ['published', 'draft'])->default('published');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beritas');
    }
};
