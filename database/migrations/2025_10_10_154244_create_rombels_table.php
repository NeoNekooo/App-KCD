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
        Schema::create('rombels', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('rombongan_belajar_id', 191)->nullable()->index();
            $table->text('nama')->nullable();
            $table->string('tingkat_pendidikan_id', 191)->nullable()->index();
            $table->text('tingkat_pendidikan_id_str')->nullable();
            $table->string('semester_id', 191)->nullable()->index();
            $table->text('jenis_rombel')->nullable();
            $table->text('jenis_rombel_str')->nullable();
            $table->string('kurikulum_id', 191)->nullable()->index();
            $table->text('kurikulum_id_str')->nullable();
            $table->text('id_ruang')->nullable();
            $table->text('id_ruang_str')->nullable();
            $table->text('moving_class')->nullable();
            $table->string('ptk_id', 191)->nullable()->index();
            $table->text('ptk_id_str')->nullable();
            $table->string('jurusan_id', 191)->nullable()->index();
            $table->text('jurusan_id_str')->nullable();
            $table->text('anggota_rombel')->nullable();
            $table->text('pembelajaran')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rombels');
    }
};
