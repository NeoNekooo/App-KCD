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
        Schema::table('gtks', function (Blueprint $table) {
            if (!Schema::hasColumn('gtks', 'status')) {
                $table->string('status', 100)->default('Aktif')->after('nama');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gtks', function (Blueprint $table) {
            if (Schema::hasColumn('gtks', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
