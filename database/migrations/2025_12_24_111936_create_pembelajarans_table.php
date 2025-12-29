<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pembelajarans', function (Blueprint $table) {
            $table->id();
            // Relasi ke Rombel
            $table->unsignedBigInteger('rombel_id');
            // Pastikan tipe data foreign key sesuai dengan id di tabel rombels (biasanya bigInteger/uuid)

            // Data Mapel
            $table->string('mata_pelajaran_id')->nullable(); // ID kode mapel
            $table->string('nama_mata_pelajaran'); // Nama Mapel (Matematika, dll)

            // Data Guru
            $table->string('ptk_id')->nullable(); // UUID Guru (penghubung ke tabel GTK)

            // Opsional: Jika ingin menyimpan beban jam
            $table->integer('jam_mengajar_per_minggu')->default(0);

            $table->timestamps();

            // Indexing biar query cepat saat Drag & Drop
            $table->index('rombel_id');
            $table->index('ptk_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pembelajarans');
    }
};