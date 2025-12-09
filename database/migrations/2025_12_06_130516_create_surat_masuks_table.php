<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_masuks', function (Blueprint $table) {
            $table->id();
            $table->string('no_agenda')->nullable();
            $table->string('no_surat');
            $table->date('tanggal_surat');
            $table->date('tanggal_diterima');
            $table->string('asal_surat');
            $table->text('perihal');
            $table->string('tujuan_disposisi')->nullable();
            $table->string('file_surat')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_masuks');
    }
};
