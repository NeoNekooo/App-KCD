<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calon_siswa_syarat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_siswa_id')->constrained('calon_siswas')->onDelete('cascade');
            $table->foreignId('syarat_id')->constrained('syarat_pendaftarans')->onDelete('cascade');
            $table->boolean('is_checked')->default(false);
            $table->string('file_path')->nullable(); // untuk simpan path file upload
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calon_siswa_syarat');
    }
};

