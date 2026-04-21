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
        Schema::table('pengajuan_sekolahs', function (Blueprint $table) {
            // 1. Tambahkan instansi_id jika belum ada (untuk FilterRegional)
            if (!Schema::hasColumn('pengajuan_sekolahs', 'instansi_id')) {
                $table->unsignedBigInteger('instansi_id')->nullable()->after('id')->index();
            }

            // 2. Tambahkan tipe_pengaju (GTK atau PD)
            if (!Schema::hasColumn('pengajuan_sekolahs', 'tipe_pengaju')) {
                $table->string('tipe_pengaju')->nullable()->after('judul')->index();
            }

            // 3. Tambahkan data_siswa_json (Snapshot data siswa)
            if (!Schema::hasColumn('pengajuan_sekolahs', 'data_siswa_json')) {
                $table->json('data_siswa_json')->nullable()->after('tipe_pengaju');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_sekolahs', function (Blueprint $table) {
            $table->dropColumn(['instansi_id', 'tipe_pengaju', 'data_siswa_json']);
        });
    }
};
