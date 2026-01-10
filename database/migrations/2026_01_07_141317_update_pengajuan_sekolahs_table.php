<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    // 1. Tambah kolom JSON buat nyimpen syarat (KTP, KK, Status Validasi)
    Schema::table('pengajuan_sekolahs', function (Blueprint $table) {
        $table->json('dokumen_syarat')->nullable()->after('judul');
    });

    // 2. Ubah file_path jadi BOLEH KOSONG (NULL)
    // Karena pas request awal, sekolah cuma kirim judul doang
    DB::statement("ALTER TABLE pengajuan_sekolahs MODIFY COLUMN file_path VARCHAR(255) NULL");
}

public function down()
{
    Schema::table('pengajuan_sekolahs', function (Blueprint $table) {
        $table->dropColumn('dokumen_syarat');
    });
}
};
