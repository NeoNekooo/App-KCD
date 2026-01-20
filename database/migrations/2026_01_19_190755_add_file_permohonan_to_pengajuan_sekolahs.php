<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk menambahkan kolom file_permohonan.
     * Kolom ini berfungsi untuk menyimpan link/path surat permohonan awal dari sekolah.
     */
    public function up(): void
    {
        Schema::table('pengajuan_sekolahs', function (Blueprint $table) {
            // Kita tambahkan kolom file_permohonan setelah kolom judul
            // Menggunakan conditional check agar aman bagi database tim
            if (!Schema::hasColumn('pengajuan_sekolahs', 'file_permohonan')) {
                $table->string('file_permohonan')->after('judul')->nullable()
                      ->comment('URL atau path surat permohonan awal dari sekolah');
            }
        });
    }

    /**
     * Batalkan migrasi (Rollback).
     */
    public function down(): void
    {
        Schema::table('pengajuan_sekolahs', function (Blueprint $table) {
            if (Schema::hasColumn('pengajuan_sekolahs', 'file_permohonan')) {
                $table->dropColumn('file_permohonan');
            }
        });
    }
};