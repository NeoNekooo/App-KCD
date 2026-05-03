<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengawas_pembinas', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->unsignedBigInteger('pengawas_id'); // ID dari tabel users
            $blueprint->string('sekolah_id'); // UUID dari tabel sekolahs
            $blueprint->timestamps();

            // Index biar cepet
            $blueprint->index('pengawas_id');
            $blueprint->index('sekolah_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengawas_pembinas');
    }
};
