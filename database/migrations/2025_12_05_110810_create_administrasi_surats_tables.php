<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Tabel Template Surat (Master)
        // Hanya tabel ini yang dibuat karena data surat keluar tidak disimpan (langsung cetak)
        Schema::create('tipe_surats', function (Blueprint $table) {
            $table->id();
            $table->string('judul_surat');     // Contoh: Surat Panggilan, Surat Izin
            $table->enum('kategori', ['siswa', 'guru'])->default('siswa'); 
            $table->longText('template_isi');  // Isi HTML template surat dari Summernote/CKEditor
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tipe_surats');
    }
};