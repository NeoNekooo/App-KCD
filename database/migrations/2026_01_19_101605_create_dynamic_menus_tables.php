<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->string('route')->nullable();
            $table->string('url')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            
            $table->json('params')->nullable();
            $table->string('badge_key')->nullable();
            $table->boolean('is_header')->default(false);
            
            // 🔥 PERBAIKAN DI SINI 🔥
            $table->integer('urutan')->default(0); // Pake 'urutan'
            $table->boolean('is_active')->default(true); // Tambah 'is_active'
            
            $table->timestamps();
        });

        Schema::create('menu_accesses', function (Blueprint $table) {
            $table->id();
            $table->string('role_name');
            $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_accesses');
        Schema::dropIfExists('menus');
    }
};
