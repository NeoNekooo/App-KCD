<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('jabatan_kcd')) {
            Schema::table('jabatan_kcd', function (Blueprint $table) {
                if (!Schema::hasColumn('jabatan_kcd', 'instansi_id')) {
                    $table->foreignId('instansi_id')->nullable()->constrained('instansis')->onDelete('set null')->after('id');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('jabatan_kcd')) {
            Schema::table('jabatan_kcd', function (Blueprint $table) {
                if (Schema::hasColumn('jabatan_kcd', 'instansi_id')) {
                    $table->dropForeign(['instansi_id']);
                    $table->dropColumn('instansi_id');
                }
            });
        }
    }
};
