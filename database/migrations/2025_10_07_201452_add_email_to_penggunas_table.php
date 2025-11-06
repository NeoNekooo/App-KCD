<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmailToPenggunasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('penggunas', function (Blueprint $table) {
            // Tambahkan kolom email
                $table->string('email', 100)->nullable()->unique()->after('id');

        $table->timestamp('email_verified_at')->nullable()->after('email'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('penggunas', function (Blueprint $table) {
            // Hapus kolom email saat rollback
            $table->dropColumn('email');
            $table->dropColumn('email_verified_at');
        });
    }
}
