<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instansis', function (Blueprint $blueprint) {
            // Kita tambahkan kolom tanda_tangan setelah kolom logo
            $blueprint->string('tanda_tangan')->nullable()->after('logo');
        });
    }

    public function down(): void
    {
        Schema::table('instansis', function (Blueprint $blueprint) {
            $blueprint->dropColumn('tanda_tangan');
        });
    }
};