<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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

    public function down(): void
    {
        // No rollback needed for emergency fix
    }
};
