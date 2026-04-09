<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WebContentSeeder extends Seeder
{
    public function run(): void
    {
        // ================================================
        // 1. BERITA (clear & re-seed for clean demo data)
        // ================================================
        DB::table('beritas')->whereIn('slug', [
            'rakor-peningkatan-mutu-pendidikan-2026',
            'workshop-digitalisasi-administrasi-sekolah',
            'penyerahan-penghargaan-sekolah-berprestasi',
        ])->delete();

        DB::table('beritas')->insert([
            [
                'judul'        => 'Rakor Peningkatan Mutu Pendidikan Wilayah VI Tahun 2026',
                'slug'         => 'rakor-peningkatan-mutu-pendidikan-2026',
                'ringkasan'    => 'Kantor Cabang Dinas Pendidikan Wilayah VI menggelar Rapat Koordinasi Peningkatan Mutu Pendidikan yang dihadiri oleh seluruh kepala sekolah binaan.',
                'isi'          => "Pada hari Senin, 7 April 2026, KCD Wilayah VI menggelar Rakor Peningkatan Mutu Pendidikan bertempat di Aula Utama KCD.\n\nKegiatan ini dihadiri oleh seluruh Kepala Sekolah dari 45 satuan pendidikan binaan, para pengawas sekolah, serta pejabat struktural KCD Wilayah VI.\n\nDalam sambutannya, Kepala KCD menyampaikan pentingnya kolaborasi antar satuan pendidikan dalam meningkatkan kualitas pembelajaran.\n\n\"Kita harus bersinergi untuk memastikan setiap peserta didik mendapatkan pelayanan pendidikan yang terbaik,\" ujarnya.\n\nBeberapa poin penting yang dibahas:\n1. Evaluasi implementasi Kurikulum Merdeka\n2. Pemetaan kebutuhan guru dan tenaga kependidikan\n3. Persiapan Asesmen Nasional 2026\n4. Program literasi dan numerasi\n\nRakor berlangsung dari pukul 08.00 hingga 15.00 WIB dan ditutup dengan penandatanganan komitmen bersama.",
                'gambar'       => '',
                'penulis'      => 'Admin KCD',
                'status'       => 'publish',
                'published_at' => Carbon::now()->subDays(2),
                'created_at'   => Carbon::now()->subDays(2),
                'updated_at'   => Carbon::now()->subDays(2),
            ],
            [
                'judul'        => 'Workshop Digitalisasi Administrasi Sekolah Berbasis Cloud',
                'slug'         => 'workshop-digitalisasi-administrasi-sekolah',
                'ringkasan'    => 'KCD Wilayah VI menyelenggarakan workshop digitalisasi administrasi untuk operator sekolah guna mempercepat transformasi digital.',
                'isi'          => "KCD Wilayah VI menyelenggarakan Workshop Digitalisasi Administrasi Sekolah selama dua hari, 3-4 April 2026.\n\nWorkshop ini diikuti oleh 90 operator sekolah dari seluruh satuan pendidikan binaan. Peserta dilatih menggunakan sistem informasi manajemen sekolah terbaru.\n\nMateri yang disampaikan:\n- Pengelolaan data peserta didik secara digital\n- Sistem pelaporan online\n- Manajemen arsip digital\n- Keamanan data pendidikan\n\n\"Dengan digitalisasi ini, proses administrasi yang biasanya memakan waktu berhari-hari bisa diselesaikan dalam hitungan jam,\" jelas koordinator acara.",
                'gambar'       => '',
                'penulis'      => 'Tim Humas',
                'status'       => 'publish',
                'published_at' => Carbon::now()->subDays(5),
                'created_at'   => Carbon::now()->subDays(5),
                'updated_at'   => Carbon::now()->subDays(5),
            ],
            [
                'judul'        => 'Penyerahan Penghargaan Sekolah Berprestasi Tingkat Wilayah',
                'slug'         => 'penyerahan-penghargaan-sekolah-berprestasi',
                'ringkasan'    => 'Lima sekolah binaan KCD Wilayah VI menerima penghargaan atas prestasi di bidang akademik dan non-akademik tahun 2025.',
                'isi'          => "Kepala KCD menyerahkan penghargaan kepada lima sekolah berprestasi tingkat wilayah.\n\nPenghargaan diberikan berdasarkan evaluasi komprehensif yang meliputi prestasi akademik siswa, inovasi pembelajaran, dan tata kelola sekolah.\n\nLima sekolah penerima penghargaan:\n1. SMAN 1 - Sekolah Terbaik Kategori Akademik\n2. SMKN 2 - Sekolah Terbaik Inovasi Pembelajaran\n3. SMPN 3 - Sekolah Terbaik Karakter\n4. SMAN 4 - Sekolah Terbaik Ekstrakurikuler\n5. SMKN 1 - Sekolah Terbaik Tata Kelola\n\n\"Penghargaan ini menjadi motivasi bagi seluruh sekolah untuk terus meningkatkan kualitas pendidikan,\" ujar Kepala KCD.",
                'gambar'       => '',
                'penulis'      => 'Admin KCD',
                'status'       => 'publish',
                'published_at' => Carbon::now()->subDays(10),
                'created_at'   => Carbon::now()->subDays(10),
                'updated_at'   => Carbon::now()->subDays(10),
            ],
        ]);

        // ================================================
        // 2. PENGUMUMAN
        // ================================================
        DB::table('pengumumans')->whereIn('slug', [
            'jadwal-verifikasi-kp-april-2026',
            'libur-idul-fitri-1447h',
            'pendataan-gtk-genap-2026',
        ])->delete();

        DB::table('pengumumans')->insert([
            [
                'judul'            => 'Jadwal Verifikasi Berkas Kenaikan Pangkat Periode April 2026',
                'slug'             => 'jadwal-verifikasi-kp-april-2026',
                'isi'              => "Diberitahukan kepada seluruh GTK di KCD Wilayah VI bahwa verifikasi berkas Kenaikan Pangkat periode April 2026 akan dilaksanakan pada:\n\nHari/Tanggal: Senin - Rabu, 14-16 April 2026\nWaktu: 08.00 - 15.00 WIB\nTempat: Ruang Layanan KCD Wilayah VI\n\nDokumen yang harus disiapkan:\n1. SK Pangkat Terakhir (asli + fotokopi)\n2. PAK (Penetapan Angka Kredit)\n3. DP3/SKP 2 tahun terakhir\n4. Ijazah terakhir (legalisir)\n5. Surat pengantar dari sekolah",
                'lampiran'         => null,
                'prioritas'        => 'penting',
                'tanggal_terbit'   => Carbon::now()->subDays(1)->toDateString(),
                'tanggal_berakhir' => Carbon::now()->addDays(14)->toDateString(),
                'status'           => 'publish',
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'judul'            => 'Libur Hari Raya Idul Fitri 1447 H dan Cuti Bersama',
                'slug'             => 'libur-idul-fitri-1447h',
                'isi'              => "Sehubungan dengan Hari Raya Idul Fitri 1447 H, diberitahukan bahwa:\n\n1. Libur Hari Raya: 30-31 Maret 2026\n2. Cuti Bersama: 1-4 April 2026\n3. Pelayanan kantor kembali normal: Senin, 7 April 2026\n\nUntuk layanan mendesak selama periode libur, hubungi nomor darurat KCD.\n\nSelamat Hari Raya Idul Fitri 1447 H. Mohon maaf lahir dan batin.",
                'lampiran'         => null,
                'prioritas'        => 'urgent',
                'tanggal_terbit'   => Carbon::now()->subDays(10)->toDateString(),
                'tanggal_berakhir' => null,
                'status'           => 'publish',
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'judul'            => 'Pendataan Ulang GTK Semester Genap TA 2025/2026',
                'slug'             => 'pendataan-gtk-genap-2026',
                'isi'              => "Kepada Yth. Kepala Sekolah di lingkungan KCD Wilayah VI,\n\nDimohon seluruh satuan pendidikan untuk melakukan pendataan ulang GTK semester genap TA 2025/2026.\n\nBatas waktu: 30 April 2026\nFormat: Template yang dikirim via email resmi\nPengumpulan: Upload melalui sistem informasi KCD\n\nData yang perlu diperbarui:\n- Status kepegawaian terkini\n- Riwayat pendidikan\n- Sertifikasi pendidik\n- Jam mengajar",
                'lampiran'         => null,
                'prioritas'        => 'biasa',
                'tanggal_terbit'   => Carbon::now()->toDateString(),
                'tanggal_berakhir' => null,
                'status'           => 'publish',
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
        ]);

        // ================================================
        // 3. UNDUHAN
        // ================================================
        DB::table('unduhans')->whereIn('judul', [
            'Formulir Pengajuan Kenaikan Pangkat',
            'Juknis Penyusunan RKAS 2026',
            'Permendikbud No. 5 Tahun 2026',
            'Laporan Kinerja KCD Triwulan I 2026',
        ])->delete();

        DB::table('unduhans')->insert([
            ['judul' => 'Formulir Pengajuan Kenaikan Pangkat', 'deskripsi' => 'Template formulir untuk pengajuan kenaikan pangkat reguler GTK.', 'file' => 'unduhan/formulir-kp.pdf', 'kategori' => 'Formulir', 'jumlah_unduhan' => 45, 'created_at' => now(), 'updated_at' => now()],
            ['judul' => 'Juknis Penyusunan RKAS 2026', 'deskripsi' => 'Petunjuk teknis penyusunan Rencana Kegiatan dan Anggaran Sekolah tahun 2026.', 'file' => 'unduhan/juknis-rkas-2026.pdf', 'kategori' => 'Panduan', 'jumlah_unduhan' => 128, 'created_at' => now(), 'updated_at' => now()],
            ['judul' => 'Permendikbud No. 5 Tahun 2026', 'deskripsi' => 'Peraturan tentang Standar Kompetensi Lulusan pada Pendidikan Dasar dan Menengah.', 'file' => 'unduhan/permendikbud-5-2026.pdf', 'kategori' => 'Peraturan', 'jumlah_unduhan' => 89, 'created_at' => now(), 'updated_at' => now()],
            ['judul' => 'Laporan Kinerja KCD Triwulan I 2026', 'deskripsi' => 'Laporan akuntabilitas kinerja instansi periode Januari - Maret 2026.', 'file' => 'unduhan/lakip-tw1-2026.pdf', 'kategori' => 'Laporan', 'jumlah_unduhan' => 23, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $this->command->info('✅ Berhasil: 3 Berita, 3 Pengumuman, 4 Unduhan telah di-seed!');
        $this->command->info('💡 Jalankan "php artisan db:seed --class=MenuSeeder" untuk update menu sidebar admin.');
    }
}
