<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pengumumans')) {
            Schema::create('pengumumans', function (Blueprint $table) {
                $table->id();
                $table->string('judul');
                $table->string('slug')->unique();
                $table->longText('isi');
                $table->string('lampiran')->nullable();
                $table->enum('prioritas', ['biasa', 'penting', 'urgent'])->default('biasa');
                $table->date('tanggal_terbit')->nullable();
                $table->date('tanggal_berakhir')->nullable();
                $table->enum('status', ['draft', 'publish'])->default('draft');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('unduhans')) {
            Schema::create('unduhans', function (Blueprint $table) {
                $table->id();
                $table->string('judul');
                $table->text('deskripsi')->nullable();
                $table->string('file');
                $table->string('kategori')->default('Umum');
                $table->integer('jumlah_unduhan')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('galeris')) {
            Schema::create('galeris', function (Blueprint $table) {
                $table->id();
                $table->string('judul');
                $table->date('tanggal')->nullable();
                $table->text('deskripsi')->nullable();
                $table->string('foto')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('galeri_items')) {
            Schema::create('galeri_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('galeri_id')->constrained('galeris')->onDelete('cascade');
                $table->string('file');
                $table->string('jenis')->default('foto');
                $table->string('caption')->nullable();
                $table->timestamps();
            });
        }

        // Add published_at to beritas if missing
        if (Schema::hasTable('beritas') && !Schema::hasColumn('beritas', 'published_at')) {
            Schema::table('beritas', function (Blueprint $table) {
                $table->timestamp('published_at')->nullable()->after('status');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('galeri_items');
        Schema::dropIfExists('galeris');
        Schema::dropIfExists('unduhans');
        Schema::dropIfExists('pengumumans');

        if (Schema::hasColumn('beritas', 'published_at')) {
            Schema::table('beritas', function (Blueprint $table) {
                $table->dropColumn('published_at');
            });
        }
    }
};
