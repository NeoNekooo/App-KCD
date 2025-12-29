<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            // Kolom ID Telegram (Hasil Connect - Skenario A: Timpah Data)
            $table->string('telegram_chat_id')->nullable()->index()->after('nomor_telepon_seluler');

            // Kolom Token (Kunci Rahasia - Unique)
            $table->string('telegram_token', 64)->nullable()->unique()->after('telegram_chat_id');
        });

        // --- AUTO SEEDING TOKEN UNTUK SISWA LAMA ---
        // Kita generate token langsung pakai SQL biar cepat jika datanya banyak
        // Menggunakan UUID sementara atau string random
        $results = DB::table('siswas')->select('id')->get();
        foreach ($results as $row) {
            DB::table('siswas')
                ->where('id', $row->id)
                ->update(['telegram_token' => Str::random(32)]);
        }
    }

    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn(['telegram_chat_id', 'telegram_token']);
        });
    }
};
