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
        Schema::table('izin_siswa', function (Blueprint $table) {
            $table->foreign(['dicatat_oleh'])->references(['id'])->on('users')->onUpdate('no action')->onDelete('restrict');
            $table->foreign(['siswa_id'])->references(['id'])->on('siswas')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('izin_siswa', function (Blueprint $table) {
            $table->dropForeign('izin_siswa_dicatat_oleh_foreign');
            $table->dropForeign('izin_siswa_siswa_id_foreign');
        });
    }
};
