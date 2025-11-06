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
        Schema::create('izin_siswa', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('siswa_id');
            $table->date('tanggal_izin');
            $table->enum('tipe_izin', ['DATANG_TERLAMBAT', 'PULANG_AWAL', 'KELUAR_SEMENTARA']);
            $table->time('jam_izin_mulai')->nullable()->comment('Untuk Pulang Awal & Keluar Sementara');
            $table->time('jam_izin_selesai')->nullable()->comment('Untuk Datang Terlambat & Keluar Sementara');
            $table->text('alasan');
            $table->enum('status', ['DISETUJUI', 'DIGUNAKAN', 'KADALUARSA', 'SEDANG_KELUAR'])->default('DISETUJUI');
            $table->unsignedBigInteger('dicatat_oleh')->index('izin_siswa_dicatat_oleh_foreign');
            $table->string('token_sementara')->nullable()->unique();
            $table->timestamp('waktu_keluar')->nullable();
            $table->timestamp('waktu_kembali')->nullable();
            $table->timestamps();

            $table->index(['siswa_id', 'tanggal_izin']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('izin_siswa');
    }
};
