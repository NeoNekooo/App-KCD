<?php

        use Illuminate\Database\Migrations\Migration;
        use Illuminate\Database\Schema\Blueprint;
        use Illuminate\Support\Facades\Schema;

        return new class extends Migration
        {
            public function up(): void
            {
                Schema::create('tugas_pegawais', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('pegawai_id')->constrained('gtks')->onDelete('cascade');
                    $table->string('tahun_pelajaran');
                    $table->string('semester');
                    $table->string('tugas_pokok');
                    $table->string('nomor_sk')->nullable();
                    $table->date('tmt')->nullable(); // Terhitung Mulai Tanggal
                    $table->integer('jumlah_jam')->default(0);
                    $table->text('keterangan')->nullable();
                    $table->timestamps();
                });
            }

            public function down(): void
            {
                Schema::dropIfExists('tugas_pegawais');
            }
        };