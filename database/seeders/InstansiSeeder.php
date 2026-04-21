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

        $cadisdiks = Cadisdik::all();

        foreach ($cadisdiks as $cadisdik) {
            Instansi::create([
                'cadisdik_id'   => $cadisdik->id,
                'nama_instansi' => "Kantor Cabang Dinas " . str_replace("Wilayah ", "", $cadisdik->nama),
                'nama_brand'    => "KCD " . str_replace("Wilayah ", "", $cadisdik->nama),
                'visi' => '<p><strong>Mewujudkan Pendidikan Menengah yang Berkualitas, Inklusif, dan Berdaya Saing.</strong></p>',
                'misi' => '<ul><li>Meningkatkan akses pendidikan bermutu.</li><li>Meningkatkan profesionalisme tenaga kependidikan.</li></ul>',
                'sejarah_singkat' => '<p>Kantor Cabang Dinas ini dibentuk untuk mempermudah koordinasi pendidikan menengah di wilayahnya.</p>'
            ]);
        }
    }
}
