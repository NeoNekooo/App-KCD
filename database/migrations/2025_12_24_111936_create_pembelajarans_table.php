<?php



use Illuminate\Database\Migrations\Migration;

use Illuminate\Database\Schema\Blueprint;

use Illuminate\Support\Facades\Schema;



return new class extends Migration

{

    /**

     * Run the migrations.

     *

     * @return void

     */

    public function up()

    {

        Schema::create('jadwal_pelajarans', function (Blueprint $table) {

            $table->id();



            // 1. Relasi Utama (Wajib diisi)

            // Relasi ke Tahun Ajaran (Table: tapel)

            $table->unsignedBigInteger('tahun_ajaran_id');



            // Relasi ke Rombel (Table: rombels)

            $table->unsignedBigInteger('rombel_id');



            // Relasi ke Master Jam (Table: jam_pelajarans) -> Menentukan Hari & Jam

            $table->foreignId('jam_pelajaran_id')

                  ->constrained('jam_pelajarans')

                  ->onDelete('cascade');



            // Relasi ke Master Pembelajaran (Table: pembelajarans) -> Menentukan Guru & Mapel

            $table->foreignId('pembelajaran_id')

                  ->nullable() // Nullable agar jadwal bisa dikosongkan tanpa menghapus row (opsional)

                  ->constrained('pembelajarans')

                  ->onDelete('cascade');



            // 2. Data Tambahan

            $table->integer('semester_id')->nullable(); // 1: Ganjil, 2: Genap



            // 3. Kolom Legacy (Penting dibuat Nullable agar tidak error dengan kode lama)

            // Kolom-kolom ini dulu dipakai menyimpan data mentah, sekarang sudah digantikan relasi di atas.

            $table->string('mata_pelajaran')->nullable();

            $table->string('ptk_id')->nullable();

            $table->string('hari')->nullable();       // Sudah diambil dari jam_pelajarans

            $table->time('jam_mulai')->nullable();    // Sudah diambil dari jam_pelajarans

            $table->time('jam_selesai')->nullable();  // Sudah diambil dari jam_pelajarans



            $table->timestamps();



            // 4. Foreign Key Constraints (Opsional, aktifkan jika struktur tabel lain sudah fix)

            // $table->foreign('tahun_ajaran_id')->references('id')->on('tapel')->onDelete('cascade');

            // $table->foreign('rombel_id')->references('id')->on('rombels')->onDelete('cascade');

        });

    }



    /**

     * Reverse the migrations.

     *

     * @return void

     */

    public function down()

    {

        Schema::dropIfExists('jadwal_pelajarans');

    }

};
