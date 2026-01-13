<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('pegawai_kcds', function (Blueprint $table) {
        // Tambahan kolom profil lengkap
        $table->string('nik', 16)->nullable()->after('nama');
        $table->string('tempat_lahir')->nullable()->after('nik');
        $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
        $table->enum('jenis_kelamin', ['L', 'P'])->nullable()->after('tanggal_lahir');
        $table->text('alamat')->nullable()->after('no_hp');
        $table->string('foto')->nullable()->after('alamat'); // Path foto
        $table->string('email_pribadi')->nullable()->after('no_hp');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pegawai_kcds', function (Blueprint $table) {
            //
        });
    }
};
