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
    Schema::create('pengeluarans', function (Blueprint $table) {
    $table->id();
    $table->date('tanggal');
    $table->string('kode_akun')->nullable(); // Dari COA (Chart of Accounts)
    $table->text('uraian');
    $table->unsignedBigInteger('nominal');
    $table->unsignedBigInteger('petugas_id')->nullable();
    $table->unsignedBigInteger('master_kas_id');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengeluarans');
    }
};
