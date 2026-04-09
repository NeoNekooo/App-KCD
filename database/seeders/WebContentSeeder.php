<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class WebContentSeeder extends Seeder
{
    public function run(): void
    {
        // ================================================
        // BERITA
        // ================================================
        $beritas = [
            [
                'judul' => 'Rakor Peningkatan Mutu Pendidikan Wilayah VI Tahun 2026',
                'slug' => 'rakor-peningkatan-mutu-pendidikan-' . Str::random(5),
                'ringkasan' => 'Kantor Cabang Dinas Pendidikan Wilayah VI menggelar Rapat Koordinasi Peningkatan Mutu Pendidikan yang dihadiri oleh seluruh kepala sekolah binaan.',
                'isi' => "Pada hari Senin, 7 April 2026, Kantor Cabang Dinas Pendidikan Wilayah VI menggelar Rapat Koordinasi (Rakor) Peningkatan Mutu Pendidikan bertempat di Aula Utama KCD.\n\nKegiatan ini dihadiri oleh seluruh Kepala Sekolah dari 45 satuan pendidikan binaan, para pengawas sekolah, serta pejabat struktural KCD Wilayah VI.\n\nDalam sambutannya, Kepala KCD menyampaikan pentingnya kolaborasi antar satuan pendidikan dalam meningkatkan kualitas pembelajaran pasca pandemi.\n\n\"Kita harus bersinergi untuk memastikan setiap peserta didik mendapatkan pelayanan pendidikan yang terbaik,\" ujarnya.\n\nBeberapa poin penting yang dibahas meliputi:\n1. Evaluasi implementasi Kurikulum Merdeka\n2. Pemetaan kebutuhan guru dan tenaga kependidikan\n3. Persiapan Asesmen Nasional 2026\n4. Program literasi dan numerasi\n\nRakor berlangsung dari pukul 08.00 hingga 15.00 WIB dan ditutup dengan penandatanganan komitmen bersama peningkatan mutu pendidikan.",
                'penulis' => 'Admin KCD',
                'status' => 'publish',
                'published_at' => Carbon::now()->subDays(2),
            ],
            [
                'judul' => 'Workshop Digitalisasi Administrasi Sekolah Berbasis Cloud',
                'slug' => 'workshop-digitalisasi-administrasi-' . Str::random(5),
                'ringkasan' => 'KCD Wilayah VI menyelenggarakan workshop digitalisasi administrasi untuk operator sekolah guna mempercepat transformasi digital.',
                'isi' => "Kantor Cabang Dinas Pendidikan Wilayah VI menyelenggarakan Workshop Digitalisasi Administrasi Sekolah Berbasis Cloud selama dua hari, 3-4 April 2026.\n\nWorkshop ini diikuti oleh 90 operator sekolah dari seluruh satuan pendidikan binaan. Peserta dilatih menggunakan sistem informasi manajemen sekolah terbaru yang terintegrasi dengan database KCD.\n\nMateri yang disampaikan meliputi:\n- Pengelolaan data peserta didik secara digital\n- Sistem pelaporan online\n- Manajemen arsip digital\n- Keamanan data pendidikan\n\nNarasumber workshop berasal dari Tim IT Dinas Pendidikan Provinsi dan praktisi teknologi pendidikan.\n\n\"Dengan digitalisasi ini, proses administrasi yang biasanya memakan waktu berhari-hari bisa diselesaikan dalam hitungan jam,\" jelas koordinator acara.",
                'penulis' => 'Tim Humas',
                'status' => 'publish',
                'published_at' => Carbon::now()->subDays(5),
            ],
            [
                'judul' => 'Penyerahan Penghargaan Sekolah Berprestasi Tingkat Wilayah',
                'slug' => 'penyerahan-penghargaan-sekolah-' . Str::random(5),
                'ringkasan' => 'Lima sekolah binaan KCD Wilayah VI menerima penghargaan atas prestasi di bidang akademik dan non-akademik tahun 2025.',
                'isi' => "Kepala Kantor Cabang Dinas Pendidikan Wilayah VI menyerahkan penghargaan kepada lima sekolah berprestasi tingkat wilayah.\n\nPenghargaan ini diberikan berdasarkan evaluasi komprehensif yang meliputi prestasi akademik siswa, inovasi pembelajaran guru, dan tata kelola sekolah yang baik.\n\nLima sekolah penerima penghargaan:\n1. SMAN 1 - Sekolah Terbaik Kategori Akademik\n2. SMKN 2 - Sekolah Terbaik Inovasi Pembelajaran\n3. SMPN 3 - Sekolah Terbaik Karakter\n4. SMAN 4 - Sekolah Terbaik Ekstrakurikuler\n5. SMKN 1 - Sekolah Terbaik Tata Kelola\n\n\"Penghargaan ini menjadi motivasi bagi seluruh sekolah untuk terus meningkatkan kualitas pendidikan,\" ujar Kepala KCD.",
                'penulis' => 'Admin KCD',
                'gambar' => '',
                'status' => 'publish',
                'published_at' => Carbon::now()->subDays(10),
            ],
        ];

        foreach ($beritas as $berita) {
            DB::table('beritas')->updateOrInsert(
                ['slug' => $berita['slug']],
                array_merge($berita, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // ================================================
        // PENGUMUMAN
        // ================================================
        $pengumumans = [
            [
                'judul' => 'Jadwal Verifikasi Berkas Kenaikan Pangkat Periode April 2026',
                'slug' => 'jadwal-verifikasi-kp-april-' . Str::random(5),
                'isi' => "Diberitahukan kepada seluruh GTK di lingkungan KCD Wilayah VI bahwa verifikasi berkas Kenaikan Pangkat periode April 2026 akan dilaksanakan pada:\n\nHari/Tanggal: Senin - Rabu, 14-16 April 2026\nWaktu: 08.00 - 15.00 WIB\nTempat: Ruang Layanan KCD Wilayah VI\n\nDokumen yang harus disiapkan:\n1. SK Pangkat Terakhir (asli + fotokopi)\n2. PAK (Penetapan Angka Kredit)\n3. DP3/SKP 2 tahun terakhir\n4. Ijazah terakhir (legalisir)\n5. Surat pengantar dari sekolah\n\nMohon memperhatikan jadwal dan melengkapi berkas sesuai ketentuan.",
                'prioritas' => 'penting',
                'tanggal_terbit' => Carbon::now()->subDays(1),
                'tanggal_berakhir' => Carbon::now()->addDays(14),
                'status' => 'publish',
            ],
            [
                'judul' => 'Libur Hari Raya Idul Fitri 1447 H dan Cuti Bersama',
                'slug' => 'libur-idul-fitri-' . Str::random(5),
                'isi' => "Sehubungan dengan Hari Raya Idul Fitri 1447 H, diberitahukan bahwa:\n\n1. Libur Hari Raya: 30-31 Maret 2026\n2. Cuti Bersama: 1-4 April 2026\n3. Pelayanan kantor kembali normal: Senin, 7 April 2026\n\nUntuk layanan mendesak selama periode libur, silakan menghubungi nomor darurat: 0812-XXXX-XXXX\n\nSelamat Hari Raya Idul Fitri 1447 H. Mohon maaf lahir dan batin.",
                'prioritas' => 'urgent',
                'tanggal_terbit' => Carbon::now()->subDays(10),
                'status' => 'publish',
            ],
            [
                'judul' => 'Pendataan Ulang GTK Semester Genap TA 2025/2026',
                'slug' => 'pendataan-gtk-genap-' . Str::random(5),
                'isi' => "Kepada Yth. Kepala Sekolah di lingkungan KCD Wilayah VI,\n\nDalam rangka updating database GTK, dimohon seluruh satuan pendidikan untuk melakukan pendataan ulang GTK semester genap TA 2025/2026.\n\nBatas waktu pengumpulan: 30 April 2026\nFormat: Menggunakan template yang telah dikirimkan via email resmi\nPengumpulan: Upload melalui sistem informasi KCD\n\nData yang perlu diperbarui:\n- Status kepegawaian terkini\n- Riwayat pendidikan\n- Sertifikasi pendidik\n- Jam mengajar\n\nAtas perhatian dan kerjasamanya diucapkan terima kasih.",
                'prioritas' => 'biasa',
                'tanggal_terbit' => Carbon::now(),
                'status' => 'publish',
            ],
        ];

        foreach ($pengumumans as $p) {
            DB::table('pengumumans')->updateOrInsert(
                ['slug' => $p['slug']],
                array_merge($p, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // ================================================
        // UNDUHAN
        // ================================================
        // Note: Files won't actually exist, but entries are created for demonstration
        $unduhans = [
            ['judul' => 'Formulir Pengajuan Kenaikan Pangkat', 'deskripsi' => 'Template formulir untuk pengajuan kenaikan pangkat reguler GTK.', 'file' => 'unduhan/formulir-kp.pdf', 'kategori' => 'Formulir', 'jumlah_unduhan' => 45],
            ['judul' => 'Juknis Penyusunan RKAS 2026', 'deskripsi' => 'Petunjuk teknis penyusunan Rencana Kegiatan dan Anggaran Sekolah tahun 2026.', 'file' => 'unduhan/juknis-rkas-2026.pdf', 'kategori' => 'Panduan', 'jumlah_unduhan' => 128],
            ['judul' => 'Permendikbud No. 5 Tahun 2026', 'deskripsi' => 'Peraturan tentang Standar Kompetensi Lulusan pada Pendidikan Dasar dan Menengah.', 'file' => 'unduhan/permendikbud-5-2026.pdf', 'kategori' => 'Peraturan', 'jumlah_unduhan' => 89],
            ['judul' => 'Laporan Kinerja KCD Triwulan I 2026', 'deskripsi' => 'Laporan akuntabilitas kinerja instansi periode Januari - Maret 2026.', 'file' => 'unduhan/lakip-tw1-2026.pdf', 'kategori' => 'Laporan', 'jumlah_unduhan' => 23],
        ];

        foreach ($unduhans as $u) {
            DB::table('unduhans')->updateOrInsert(
                ['judul' => $u['judul']],
                array_merge($u, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // ================================================
        // GALERI
        // ================================================
        $galeris = [
            [
                'judul' => 'Kegiatan Hari Guru Nasional 2025',
                'tanggal' => '2025-11-25',
                'deskripsi' => 'Dokumentasi perayaan Hari Guru Nasional di lingkungan KCD Wilayah VI.',
                'foto' => 'galeri/hgn-2025.jpg',
            ],
            [
                'judul' => 'Peresmian Gedung Layanan Terpadu KCD',
                'tanggal' => '2026-02-10',
                'deskripsi' => 'Momen peresmian gedung baru untuk meningkatkan kualitas layanan publik.',
                'foto' => 'galeri/peresmian-gedung.jpg',
            ],
        ];

        foreach ($galeris as $g) {
            $galeriId = DB::table('galeris')->insertGetId(
                array_merge($g, ['created_at' => now(), 'updated_at' => now()])
            );

            // Item Galeri (Foto di dalam album)
            DB::table('galeri_items')->insert([
                [
                    'galeri_id' => $galeriId,
                    'file' => 'galeri/items/foto1.jpg',
                    'jenis' => 'foto',
                    'caption' => 'Suasana kegiatan di pagi hari.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'galeri_id' => $galeriId,
                    'file' => 'galeri/items/foto2.jpg',
                    'jenis' => 'foto',
                    'caption' => 'Penyerahan cinderamata secara simbolis.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        $this->command->info('✅ Konten website (Berita, Pengumuman, Unduhan, Galeri) berhasil di-seed!');
    }
}
