<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::table('users', function (Blueprint $table) {
        // Tambah kolom username setelah email
        $table->string('username')->nullable()->unique()->after('email');
        
        // Tambah kolom role setelah password
        $table->string('role')->default('user')->after('password');
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['username', 'role']);
    });
}
};
