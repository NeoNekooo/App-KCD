<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    // 1. Tabel Setting (Menyimpan Format)
    Schema::create('nomor_surat_settings', function (Blueprint $table) {
        $table->id();
        $table->string('kategori')->unique(); // Value: 'siswa', 'guru', 'sk'
        $table->string('judul_kop');          // Label: 'Surat Keterangan Siswa'
        $table->string('format_surat');       // Format: 421/{no}/SMK/{tahun}
        $table->integer('nomor_terakhir')->default(0); 
        $table->timestamps();
    });

    // 2. Tabel History Log
    Schema::create('surat_logs', function (Blueprint $table) {
        $table->id();
        $table->string('kategori');
        $table->string('nomor_surat_final'); // 421/001/SMK/2025
        $table->string('nomor_urut');        // 001
        $table->string('tujuan');            // "Surat a.n Budi"
        $table->dateTime('tanggal_dibuat');
        $table->timestamps();
    });
}

    public function down()
    {
        Schema::dropIfExists('surat_logs');
        Schema::dropIfExists('nomor_surat_settings');
    }
};