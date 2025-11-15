<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        public function up(): void
        {
            Schema::create('gtks', function (Blueprint $table) {
                $table->id();
                $table->string('tahun_ajaran_id')->nullable();
                $table->string('ptk_terdaftar_id')->nullable();
                $table->string('ptk_id')->nullable();
                $table->text('ptk_induk')->nullable();
                $table->date('tanggal_surat_tugas')->nullable();
                $table->text('nama')->nullable();
                $table->text('jenis_kelamin')->nullable();
                $table->text('tempat_lahir')->nullable();
                $table->date('tanggal_lahir')->nullable();
                $table->string('agama_id')->nullable();
                $table->text('agama_id_str')->nullable();
                $table->text('nuptk')->nullable();
                $table->text('nik')->nullable();
                $table->string('jenis_ptk_id')->nullable();
                $table->text('jenis_ptk_id_str')->nullable();
                $table->string('jabatan_ptk_id')->nullable();
                $table->text('jabatan_ptk_id_str')->nullable();
                $table->string('status_kepegawaian_id')->nullable();
                $table->text('status_kepegawaian_id_str')->nullable();
                $table->text('nip')->nullable();
                $table->text('pendidikan_terakhir')->nullable();
                $table->text('bidang_studi_terakhir')->nullable();
                $table->text('pangkat_golongan_terakhir')->nullable();
                $table->text('rwy_pend_formal')->nullable();
                $table->text('rwy_kepangkatan')->nullable();
                $table->timestamps();
            });
        }

        public function down(): void
        {
            Schema::dropIfExists('gtks');
        }
    };