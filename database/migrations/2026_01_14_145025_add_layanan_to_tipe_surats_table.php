<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tipe_surats', function (Blueprint $table) {
            // 1. Tambahkan kolom sub_kategori setelah kolom kategori
            $table->string('sub_kategori', 100)->nullable()->after('kategori');
        });

        // 2. Ubah tipe data enum kategori menggunakan Raw SQL (karena Blueprint->change() sering bermasalah dengan enum)
        DB::statement("ALTER TABLE tipe_surats MODIFY COLUMN kategori ENUM('siswa', 'guru', 'sk', 'layanan') NOT NULL DEFAULT 'siswa'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipe_surats', function (Blueprint $table) {
            // Hapus kolom sub_kategori
            $table->dropColumn('sub_kategori');
        });

        // Kembalikan enum ke awal
        DB::statement("ALTER TABLE tipe_surats MODIFY COLUMN kategori ENUM('siswa', 'guru', 'sk') NOT NULL DEFAULT 'siswa'");
    }
};