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
        // Perintah untuk MENAMBAHKAN kolom baru
        Schema::table('gtks', function (Blueprint $table) {
            $table->string('foto')->nullable()->after('rwy_kepangkatan');
            $table->string('tandatangan')->nullable()->after('foto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Perintah untuk MENGHAPUS kolom jika di-rollback
        Schema::table('gtks', function (Blueprint $table) {
            $table->dropColumn(['foto', 'tandatangan']);
        });
    }
};