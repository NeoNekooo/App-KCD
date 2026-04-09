<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InstansiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $instansi = \App\Models\Instansi::first();
        if (!$instansi) {
            $instansi = \App\Models\Instansi::create(['nama_instansi' => 'Kantor Cabang Dinas X']);
        }

        $instansi->update([
            'visi' => '<p><strong>Mewujudkan Pendidikan Menengah yang Berkualitas, Inklusif, dan Berdaya Saing di Tingkat Nasional dan Global.</strong></p>',
            'misi' => '<ul><li>Meningkatkan akses dan pemerataan pendidikan bermutu.</li><li>Meningkatkan profesionalisme guru dan tenaga kependidikan.</li><li>Mengembangkan inovasi pembelajaran berwawasan global.</li><li>Membangun generasi cerdas berkarakter kuat.</li></ul>',
            'sejarah_singkat' => '<h3>Sejarah Berdirinya Instansi</h3><p>Kantor Cabang Dinas (KCD) Wilayah ini dibentuk sebagai perpanjangan tangan dinas provinsi untuk mempermudah koordinasi, pembinaan, dan pengawasan pendidikan menengah. Perjalanan KCD dimulai pada tahun ...</p><p><em>Visi besar kami diwujudkan melalui kerja sama antar sekolah, masyarakat, dan seluruh elemen pendidik.</em></p>'
        ]);
    }
}
