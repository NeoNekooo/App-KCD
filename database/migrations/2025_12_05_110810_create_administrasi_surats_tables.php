<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1. JIKA TABEL BELUM ADA (Buat Baru)
        if (!Schema::hasTable('tipe_surats')) {
            Schema::create('tipe_surats', function (Blueprint $table) {
                $table->id();
                // Menambahkan relasi ke tabel tapel
                // nullable() dipasang agar jika template bersifat umum (tidak terikat tahun), bisa dikosongkan.
                $table->foreignId('tapel_id')
                      ->nullable()
                      ->constrained('tapel')
                      ->onDelete('set null'); 

                $table->string('judul_surat');
                $table->enum('kategori', ['siswa', 'guru', 'sk'])->default('siswa'); 
                $table->string('ukuran_kertas')->default('A4');
                $table->longText('template_isi');
                $table->timestamps();
            });
        } 
        // 2. JIKA TABEL SUDAH ADA (Update Struktur)
        else {
            Schema::table('tipe_surats', function (Blueprint $table) {
                // Cek & Tambah ukuran_kertas
                if (!Schema::hasColumn('tipe_surats', 'ukuran_kertas')) {
                    $table->string('ukuran_kertas')->default('A4')->after('judul_surat');
                }

                // --- TAMBAHAN KODE UNTUK TAPEL ---
                // Cek apakah kolom tapel_id sudah ada atau belum
                if (!Schema::hasColumn('tipe_surats', 'tapel_id')) {
                    $table->foreignId('tapel_id')
                          ->nullable()
                          ->after('id') // Posisi kolom (opsional, biar rapi di depan)
                          ->constrained('tapel')
                          ->onDelete('set null');
                }
                // ---------------------------------
                
                // Cek & Hapus font_size (Cleanup)
                if (Schema::hasColumn('tipe_surats', 'font_size')) {
                    $table->dropColumn('font_size');
                }
            });

            // Update Pilihan Enum Kategori secara Manual
            try {
                DB::statement("ALTER TABLE tipe_surats MODIFY COLUMN kategori ENUM('siswa', 'guru', 'sk') DEFAULT 'siswa'");
            } catch (\Exception $e) {
                // Abaikan error database non-MySQL
            }
        }
    }

    public function down()
    {
        // Opsional: Jika ingin rollback spesifik kolom saja
        if (Schema::hasColumn('tipe_surats', 'tapel_id')) {
            Schema::table('tipe_surats', function (Blueprint $table) {
                $table->dropForeign(['tapel_id']);
                $table->dropColumn('tapel_id');
            });
        }
        
        // Atau drop total (seperti kodemu sebelumnya)
        // Schema::dropIfExists('tipe_surats');
    }
};