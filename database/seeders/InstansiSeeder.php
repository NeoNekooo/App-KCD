<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Instansi;
use App\Models\Cadisdik;
use Illuminate\Support\Facades\Schema;

class InstansiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Instansi::truncate();
        Schema::enableForeignKeyConstraints();

        // 🔥 Paksa urut I sampai XIII agar ID (Auto Increment) sinkron dengan nomor wilayah
        $wilayahs = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII', 'XIII'];

        foreach ($wilayahs as $romawi) {
            $cadisdik = Cadisdik::where('nama', "Wilayah " . $romawi)->first();

            if ($cadisdik) {
                Instansi::create([
                    'cadisdik_id'   => $cadisdik->id,
                    'nama_instansi' => "Kantor Cabang Dinas " . $romawi,
                    'nama_brand'    => "KCD " . $romawi,
                    'visi' => '<p><strong>Mewujudkan Pendidikan Menengah yang Berkualitas, Inklusif, dan Berdaya Saing.</strong></p>',
                    'misi' => '<ul><li>Meningkatkan akses pendidikan bermutu.</li><li>Meningkatkan profesionalisme tenaga kependidikan.</li></ul>',
                    'sejarah_singkat' => '<p>Kantor Cabang Dinas ini dibentuk untuk mempermudah koordinasi pendidikan menengah di wilayahnya.</p>'
                ]);
            }
        }
    }
}
