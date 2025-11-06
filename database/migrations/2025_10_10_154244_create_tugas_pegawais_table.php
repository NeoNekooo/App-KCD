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
        Schema::create('tugas_pegawais', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pegawai_id')->index('tugas_pegawais_pegawai_id_foreign');
            $table->string('tahun_pelajaran');
            $table->string('semester');
            $table->string('tugas_pokok');
            $table->string('nomor_sk')->nullable();
            $table->date('tmt')->nullable();
            $table->integer('jumlah_jam')->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tugas_pegawais');
    }
};
