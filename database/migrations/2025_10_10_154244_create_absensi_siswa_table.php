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
        Schema::create('absensi_siswa', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('jadwal_id')->nullable()->index('absensi_siswa_jadwal_id_foreign');
            $table->unsignedBigInteger('siswa_id')->index('absensi_siswa_siswa_id_foreign');
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();
            $table->enum('status', ['Hadir', 'Sakit', 'Izin', 'Alfa']);
            $table->string('status_kehadiran')->nullable()->comment('Tepat Waktu, Terlambat');
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('dicatat_oleh')->index('absensi_siswa_dicatat_oleh_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_siswa');
    }
};
