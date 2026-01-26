<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Jangan lupa import ini

return new class extends Migration
{
    public function up()
    {
        // Kita ubah kolom 'kategori' biar support opsi 'internal'
        // Pastikan urutan ENUM sesuai sama yang lama + yang baru
        DB::statement("ALTER TABLE tipe_surats MODIFY COLUMN kategori ENUM('siswa', 'guru', 'sk', 'layanan', 'internal') NOT NULL DEFAULT 'siswa'");
    }

    public function down()
    {
        // Balikin ke kondisi awal (hapus 'internal')
        // HATI-HATI: Data yang kategorinya 'internal' bakal error/hilang kalau di-rollback
        DB::statement("ALTER TABLE tipe_surats MODIFY COLUMN kategori ENUM('siswa', 'guru', 'sk', 'layanan') NOT NULL DEFAULT 'siswa'");
    }
};