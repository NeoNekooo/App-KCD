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
        Schema::table('absensi_gtk', function (Blueprint $table) {
            $table->foreign(['gtk_id'])->references(['id'])->on('gtks')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensi_gtk', function (Blueprint $table) {
            $table->dropForeign('absensi_gtk_gtk_id_foreign');
        });
    }
};
