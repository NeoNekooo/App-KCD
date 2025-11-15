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
        Schema::create('calon_siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_id')->constrained('tahun_pelajarans')->onDelete('cascade');
            $table->foreignId('jalur_id')->constrained('jalur_pendaftarans')->onDelete('cascade');
            $table->string('nis')->nullable();;
            $table->string('nomor_resi');
            $table->string('nama_lengkap');
            $table->string('nisn')->nullable();
            $table->string('npun')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->string('nama_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('alamat')->nullable();
            $table->string('desa')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kabupaten')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kode_pos')->nullable();
            $table->string('kontak')->nullable();
            $table->string('asal_sekolah')->nullable();
            $table->string('kelas')->nullable();
            $table->string('kelas_tujuan')->nullable();
            $table->string('jurusan')->nullable();
            $table->string('ukuran_pakaian')->nullable();
            $table->decimal('pembayaran', 12, 2)->default(0);
            $table->string('alamat_lengkap')->nullable();
            $table->string('tingkat')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calon_siswas');
    }
};
