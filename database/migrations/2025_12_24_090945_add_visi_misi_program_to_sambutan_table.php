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
        Schema::table('sambutan_kepala_sekolahs', function (Blueprint $table) {
            $table->text('visi')->nullable()->after('isi_sambutan');
            $table->text('misi')->nullable()->after('visi');
            $table->text('program_kerja')->nullable()->after('misi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sambutan_kepala_sekolahs', function (Blueprint $table) {
            $table->dropColumn(['visi', 'misi', 'program_kerja']);
        });
    }
};