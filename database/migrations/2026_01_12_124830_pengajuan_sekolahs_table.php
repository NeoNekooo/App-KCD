<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('pengajuan_sekolahs', function (Blueprint $table) {
        $table->id();
        $table->uuid('uuid')->unique();
        
        // Data Pemohon
        $table->string('npsn')->nullable();
        $table->string('nama_sekolah');
        $table->string('nama_guru');
        $table->string('nip')->nullable();
        
        // Data Surat
        $table->string('kategori');
        $table->string('judul');
        
        // File & Syarat
        $table->json('dokumen_syarat')->nullable();
        $table->string('file_path')->nullable();
        $table->string('url_callback')->nullable();
        
        // Flow Status
        $table->string('status')->default('Proses');
        $table->text('alasan_tolak')->nullable();     // Pesan untuk Sekolah
        $table->text('catatan_internal')->nullable(); // [WAJIB] Pesan antar Admin (Kasubag->Kepala)

        // Log Waktu
        $table->dateTime('acc_admin_at')->nullable();
        $table->dateTime('acc_kasubag_at')->nullable();
        $table->dateTime('acc_kepala_at')->nullable();

        // Output SK
        $table->string('nomor_sk')->nullable();
        $table->string('file_sk')->nullable();
        
        // [WAJIB TAMBAH DUA INI]
        $table->unsignedBigInteger('template_id')->nullable(); // Biar tahu pakai desain mana
        $table->date('tgl_selesai')->nullable(); // Tanggal fix surat diterbitkan

        $table->timestamps();
    });
}

    public function down()
    {
        Schema::dropIfExists('pengajuan_sekolahs');
    }
};