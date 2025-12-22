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
        Schema::table('sekolahs', function (Blueprint $table) {
            // Kita gunakan tipe data JSON agar bisa menyimpan banyak sosmed sekaligus
            // Format datanya nanti array: [{"platform": "facebook", "url": "...", "username": "..."}, ...]
            $table->json('social_media')->nullable()->after('peta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->dropColumn('social_media');
        });
    }
};