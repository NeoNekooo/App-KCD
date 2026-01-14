<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Tambahkan ini buat fitur Auto Sync

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. TAMBAH KOLOM
        Schema::table('users', function (Blueprint $table) {
            // Kita taruh setelah kolom 'id' biar rapi
            // Pakai nullable() karena admin mungkin bukan pegawai
            $table->foreignId('pegawai_kcd_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('pegawai_kcds') // Relasi otomatis ke tabel pegawai
                  ->onDelete('set null');       // Kalau pegawai dihapus, user jangan error, set null aja
        });

        // 2. AUTO SYNC DATA (FITUR SPESIAL)
        // Ini akan otomatis mencari user yang sudah punya data pegawai,
        // lalu menyalin ID-nya ke kolom baru ini.
        DB::statement("
            UPDATE users u
            JOIN pegawai_kcds p ON u.id = p.user_id
            SET u.pegawai_kcd_id = p.id
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus constraint foreign key dulu (nama_tabel_nama_kolom_foreign)
            $table->dropForeign(['pegawai_kcd_id']);
            $table->dropColumn('pegawai_kcd_id');
        });
    }
};