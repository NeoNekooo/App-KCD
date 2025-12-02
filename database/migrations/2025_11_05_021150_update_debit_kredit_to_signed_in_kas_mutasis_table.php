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
        Schema::table('kas_mutasis', function (Blueprint $table) {
            // Mengubah 'debit' dari BIGINT UNSIGNED menjadi BIGINT (SIGNED)
            $table->bigInteger('debit')->change();

            // Mengubah 'kredit' dari BIGINT UNSIGNED menjadi BIGINT (SIGNED)
            $table->bigInteger('kredit')->change();

            // CATATAN: Jika kolom Anda sebelumnya menggunakan 'unsignedDecimal',
            // Anda bisa menggantinya dengan:
            // $table->decimal('debit', 15, 2)->change();
            // $table->decimal('kredit', 15, 2)->change();
            // Namun, karena error mengindikasikan BIGINT, kita gunakan bigInteger.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kas_mutasis', function (Blueprint $table) {
            // Untuk rollback, kita kembalikan ke UNSIGNED jika memang diinginkan.
            // Peringatan: Rollback ini bisa gagal jika ada data negatif di kolom

            // Asumsi semula adalah BIGINT UNSIGNED:
            $table->bigInteger('debit')->unsigned()->change();
            $table->bigInteger('kredit')->unsigned()->change();

            // Jika semula adalah UNSIGNED DECIMAL:
            // $table->decimal('debit', 15, 2)->unsigned()->change();
            // $table->decimal('kredit', 15, 2)->unsigned()->change();
        });
    }
};
