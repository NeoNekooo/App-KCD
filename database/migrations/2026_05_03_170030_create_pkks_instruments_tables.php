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
        // 1. Master Paket Instrumen
        Schema::create('pkks_instrumens', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // Contoh: PKKS Kepala Sekolah 2024
            $table->string('tahun', 4);
            $table->integer('skor_min')->default(1);
            $table->integer('skor_maks')->default(4); // Bisa di-custom pas bikin paket
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        // 2. Kompetensi (Kategori Soal)
        Schema::create('pkks_kompetensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pkks_instrumen_id')->constrained('pkks_instrumens')->onDelete('cascade');
            $table->string('nama'); // Contoh: Kompetensi 1: Manajemen
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        // 3. Indikator / Butir Soal
        Schema::create('pkks_indikators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pkks_kompetensi_id')->constrained('pkks_kompetensis')->onDelete('cascade');
            $table->string('nomor', 10); // 1, 2, 3...
            $table->text('kriteria');
            $table->text('bukti_identifikasi')->nullable();
            $table->timestamps();
        });

        // 4. Tabel Penilaian (Header)
        Schema::create('pkks_penilaians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pkks_instrumen_id')->constrained('pkks_instrumens');
            $table->foreignId('pengawas_id')->constrained('users'); // Penilai
            $table->string('sekolah_id'); // Sekolah yang dinilai (NPSN)
            $table->date('tanggal_penilaian');
            $table->decimal('total_skor', 8, 2)->default(0);
            $table->text('catatan_umum')->nullable();
            $table->enum('status', ['draft', 'final'])->default('draft');
            $table->timestamps();
        });

        // 5. Tabel Jawaban per Butir
        Schema::create('pkks_jawaban_indikators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pkks_penilaian_id')->constrained('pkks_penilaians')->onDelete('cascade');
            $table->foreignId('pkks_indikator_id')->constrained('pkks_indikators');
            $table->integer('skor')->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pkks_jawaban_indikators');
        Schema::dropIfExists('pkks_penilaians');
        Schema::dropIfExists('pkks_indikators');
        Schema::dropIfExists('pkks_kompetensis');
        Schema::dropIfExists('pkks_instrumens');
    }
};
