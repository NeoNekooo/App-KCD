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
        // 1. UPDATE TABEL SISWAS
        if (Schema::hasTable('siswas')) {
            Schema::table('siswas', function (Blueprint $table) {
                $columns = [
                    'nisn', 'nik', 'no_kk', 'nik_ayah', 'nik_ibu', 'nik_wali',
                    'tanggal_lahir', 'nomor_telepon_rumah', 'nomor_telepon_seluler',
                    'no_wa', 'no_wa_ayah', 'no_wa_ibu', 'no_wa_wali',
                ];

                foreach ($columns as $column) {
                    if (Schema::hasColumn('siswas', $column)) {
                        $table->text($column)->nullable()->change();
                    }
                }
            });
        }

        // 2. UPDATE TABEL GTKS
        if (Schema::hasTable('gtks')) {
            Schema::table('gtks', function (Blueprint $table) {
                $columns = [
                    'nik', 'nik_ibu_kandung', 'no_hp', 'no_wa', 'no_telepon_rumah',
                ];

                foreach ($columns as $column) {
                    if (Schema::hasColumn('gtks', $column)) {
                        $table->text($column)->nullable()->change();
                    }
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke format semula jika perlu, tapi biasanya TEXT ke STRING aman (namun data enkripsi akan terpotong).
        // Kita biarkan kosong atau balikkan ke string(191) jika yakin.
    }
};
