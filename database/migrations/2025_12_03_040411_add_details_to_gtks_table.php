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
        Schema::table('gtks', function (Blueprint $table) {
            // --- TAB IDENTITAS ---
            $table->string('nama_ibu_kandung')->nullable()->after('tanggal_lahir');

            // --- TAB DATA PRIBADI ---
            // Kita gunakan text untuk alamat agar muat banyak
            $table->text('alamat_jalan')->nullable(); 
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->string('dusun')->nullable();
            $table->string('desa_kelurahan')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->string('lintang')->nullable();
            $table->string('bujur')->nullable();
            $table->string('no_kk', 30)->nullable();
            $table->string('npwp', 30)->nullable();
            $table->string('kewarganegaraan', 50)->nullable()->default('ID');
            $table->string('status_perkawinan')->nullable();
            $table->string('nama_suami_istri')->nullable();
            $table->string('pekerjaan_suami_istri')->nullable();

            // --- TAB KEPEGAWAIAN ---
            $table->string('niy_nigk')->nullable();
            $table->string('nrg')->nullable(); // Nomor Registrasi Guru
            $table->string('sk_pengangkatan')->nullable();
            $table->date('tmt_pengangkatan')->nullable();
            $table->string('lembaga_pengangkat')->nullable();
            $table->string('sk_cpns')->nullable();
            $table->date('tmt_cpns')->nullable();
            $table->date('tmt_pns')->nullable();
            $table->string('sumber_gaji')->nullable();

            // --- TAB KOMPETENSI KHUSUS ---
            $table->boolean('lisensi_kepsek')->default(0);
            $table->string('nuks')->nullable(); // Nomor Unik Kepala Sekolah
            $table->string('keahlian_laboratorium')->nullable();
            $table->string('mampu_menangani_kebutuhan_khusus')->nullable();
            $table->boolean('keahlian_braille')->default(0);
            $table->boolean('keahlian_bahasa_isyarat')->default(0);

            // --- TAB KONTAK ---
            $table->string('no_telepon_rumah', 20)->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->string('email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gtks', function (Blueprint $table) {
            // Hapus kolom jika rollback
            $table->dropColumn([
                'nama_ibu_kandung',
                'alamat_jalan', 'rt', 'rw', 'dusun', 'desa_kelurahan', 'kecamatan', 'kode_pos',
                'lintang', 'bujur', 'no_kk', 'npwp', 'kewarganegaraan', 
                'status_perkawinan', 'nama_suami_istri', 'pekerjaan_suami_istri',
                'niy_nigk', 'nrg', 'sk_pengangkatan', 'tmt_pengangkatan', 'lembaga_pengangkat',
                'sk_cpns', 'tmt_cpns', 'tmt_pns', 'sumber_gaji',
                'lisensi_kepsek', 'nuks', 'keahlian_laboratorium', 'mampu_menangani_kebutuhan_khusus',
                'keahlian_braille', 'keahlian_bahasa_isyarat',
                'no_telepon_rumah', 'no_hp', 'email'
            ]);
        });
    }
};