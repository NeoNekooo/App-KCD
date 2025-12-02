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
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('siswa_id');
            $table->unsignedBigInteger('iuran_id');
            $table->unsignedBigInteger('tagihan_id')->nullable();
            $table->unsignedBigInteger('tunggakan_id')->nullable();
            $table->date('tanggal_bayar');
            $table->unsignedBigInteger('jumlah_bayar');
            $table->unsignedBigInteger('master_kas_id');
            $table->unsignedBigInteger('acc_jurnal_id')->nullable();
            $table->unsignedBigInteger('petugas_id')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};

