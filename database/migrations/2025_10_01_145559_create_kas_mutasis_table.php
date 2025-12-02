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
        Schema::create('kas_mutasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_kas_id')->constrained('master_kas')->onDelete('cascade');
            $table->date('tanggal');
            $table->string('sumber_transaksi')->nullable(); // Contoh: 'Pembayaran', 'Pengeluaran', 'Gaji'
            $table->unsignedBigInteger('transaksi_id')->nullable(); // ID dari tabel sumber (pembayarans, pengeluarans, dll)
            $table->text('keterangan');
            $table->unsignedBigInteger('debit')->default(0);  // Kolom untuk uang masuk
            $table->unsignedBigInteger('kredit')->default(0); // Kolom untuk uang keluar
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kas_mutasis');
    }
};
