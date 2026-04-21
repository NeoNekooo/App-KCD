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
        // 1. Tambahkan jembatan UUID di tabel instansis agar kenal dengan SIAKAD
        if (Schema::hasTable('instansis')) {
            Schema::table('instansis', function (Blueprint $table) {
                if (!Schema::hasColumn('instansis', 'cadisdik_id')) {
                    $table->uuid('cadisdik_id')->nullable()->unique()->after('id');
                }
            });
        }

        // 2. Daftar Tabel Target Lengkap untuk Isolasi Regional Total
        $tables = [
            'antrian_tamus',
            'beritas',
            'dokumen_layanans',
            'fasilitas',
            'galeris',
            'galeri_items',
            'jabatan_kcds',
            'keperluan_categories',
            'landing_sliders',
            'nomor_surat_settings',
            'pegawai_kcds',
            'pengajuan_sekolahs',
            'pengumumans',
            'prestasis',
            'prestasi_items',
            'settings',
            'sliders',
            'struktur_organisasis',
            'surat_keluar_internals',
            'surat_logs',
            'surat_masuks',
            'sync_logs',
            'tahun_pelajarans',
            'tapel',
            'testimonis',
            'tipe_surats',
            'tugas_pegawai_kcds',
            'unduhans',
            'users',
            'sekolahs',
            'video_profils',
            'welcome_messages'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $bp) use ($table) {
                    if (!Schema::hasColumn($table, 'instansi_id')) {
                        $bp->foreignId('instansi_id')->nullable()->constrained('instansis')->onDelete('set null')->after('id');
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'antrian_tamus',
            'beritas',
            'dokumen_layanans',
            'fasilitas',
            'galeris',
            'galeri_items',
            'jabatan_kcds',
            'keperluan_categories',
            'landing_sliders',
            'nomor_surat_settings',
            'pegawai_kcds',
            'pengajuan_sekolahs',
            'pengumumans',
            'prestasis',
            'prestasi_items',
            'settings',
            'sliders',
            'struktur_organisasis',
            'surat_keluar_internals',
            'surat_logs',
            'surat_masuks',
            'sync_logs',
            'tahun_pelajarans',
            'tapel',
            'testimonis',
            'tipe_surats',
            'tugas_pegawai_kcds',
            'unduhans',
            'users',
            'sekolahs',
            'video_profils',
            'welcome_messages'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $bp) use ($table) {
                    $bp->dropForeign([$table . '_instansi_id_foreign']);
                    $bp->dropColumn('instansi_id');
                });
            }
        }

        if (Schema::hasTable('instansis')) {
            Schema::table('instansis', function (Blueprint $table) {
                $table->dropColumn('cadisdik_id');
            });
        }
    }
};
