<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelanggaran_nilai_gtk', function (Blueprint $table) {
            if (!Schema::hasColumn('pelanggaran_nilai_gtk', 'semester_id')) {
                $table->string('semester_id', 10)->nullable()->after('poin');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pelanggaran_nilai_gtk', function (Blueprint $table) {
            if (Schema::hasColumn('pelanggaran_nilai_gtk', 'semester_id')) {
                $table->dropColumn('semester_id');
            }
        });
    }
};
