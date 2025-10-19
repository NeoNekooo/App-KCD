<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('syarat_pendaftarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahunPelajaran_id')->constrained('tahun_pelajarans')->onDelete('cascade');
            $table->foreignId('jalurPendaftaran_id')->constrained('jalur_pendaftarans')->onDelete('cascade');
            $table->text('syarat')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('syarat_pendaftarans');
    }
};
