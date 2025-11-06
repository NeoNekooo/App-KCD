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
        Schema::table('absensi_siswa', function (Blueprint $table) {
            $table->foreign(['dicatat_oleh'])->references(['id'])->on('penggunas')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['jadwal_id'])->references(['id'])->on('jadwal_pelajaran')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['siswa_id'])->references(['id'])->on('siswas')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensi_siswa', function (Blueprint $table) {
            $table->dropForeign('absensi_siswa_dicatat_oleh_foreign');
            $table->dropForeign('absensi_siswa_jadwal_id_foreign');
            $table->dropForeign('absensi_siswa_siswa_id_foreign');
        });
    }
};
