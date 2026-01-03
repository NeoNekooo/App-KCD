<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('surat_logs', function (Blueprint $table) {
        $table->unsignedBigInteger('template_id')->nullable();
        $table->unsignedBigInteger('target_id')->nullable(); // ID Siswa atau ID Guru
        $table->string('target_type')->nullable(); // 'App\Models\Siswa' atau 'App\Models\Gtk'
    });
}

public function down()
{
    Schema::table('surat_logs', function (Blueprint $table) {
        $table->dropColumn(['template_id', 'target_id', 'target_type']);
    });
}
  
};
