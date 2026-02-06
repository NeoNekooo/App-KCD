<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SekolahCianjurSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();

        $dataSekolah = [
            [
                'sekolah_id' => Str::uuid(),
                'nama' => 'SMAN 1 Sukaluyu',
                'nss' => '301020714046',
                'npsn' => '20252046',
                'kode_sekolah' => 'SMAN1SKL',
                'bentuk_pendidikan_id' => 6, // Sesuaikan ID SMA di db akang
                'bentuk_pendidikan_id_str' => 'SMA',
                'status_sekolah' => 1,
                'status_sekolah_str' => 'Negeri',
                'alamat_jalan' => 'Jl. Bojongsari, Sukamulya, Kec. Sukaluyu',
                'rt' => '01',
                'rw' => '02',
                'kode_wilayah' => '020714',
                'kode_pos' => '43284',
                'nomor_telepon' => '0263-228xxxx',
                'email' => 'info@sman1sukaluyu.sch.id',
                'website' => 'sman1sukaluyu.sch.id',
                'lintang' => '-6.818500', // Koordinat Sukaluyu
                'bujur' => '107.235600',
                'kecamatan' => 'Sukaluyu',
                'kabupaten_kota' => 'Kab. Cianjur',
                'provinsi' => 'Jawa Barat',
                'logo' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'sekolah_id' => Str::uuid(),
                'nama' => 'SMK Plus Al-Ittihad Sukaluyu',
                'nss' => '402020714088',
                'npsn' => '69752134',
                'kode_sekolah' => 'SMKALIT',
                'bentuk_pendidikan_id' => 15, // Sesuaikan ID SMK
                'bentuk_pendidikan_id_str' => 'SMK',
                'status_sekolah' => 2,
                'status_sekolah_str' => 'Swasta',
                'alamat_jalan' => 'Jl. Raya Bandung KM 9, Bojong, Kec. Sukaluyu',
                'rt' => '03',
                'rw' => '05',
                'kode_wilayah' => '020714',
                'kode_pos' => '43284',
                'nomor_telepon' => '0263-229xxxx',
                'email' => 'admin@smkalittihad.sch.id',
                'website' => 'smkalittihad.sch.id',
                'lintang' => '-6.822100', // Koordinat agak geser dikit
                'bujur' => '107.241200',
                'kecamatan' => 'Sukaluyu',
                'kabupaten_kota' => 'Kab. Cianjur',
                'provinsi' => 'Jawa Barat',
                'logo' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'sekolah_id' => Str::uuid(),
                'nama' => 'SMPN 1 Sukaluyu',
                'nss' => '201020714001',
                'npsn' => '20203741',
                'kode_sekolah' => 'SMPN1SKL',
                'bentuk_pendidikan_id' => 5, // Sesuaikan ID SMP
                'bentuk_pendidikan_id_str' => 'SMP',
                'status_sekolah' => 1,
                'status_sekolah_str' => 'Negeri',
                'alamat_jalan' => 'Jl. Simpang, Sukaluyu',
                'rt' => '02',
                'rw' => '01',
                'kode_wilayah' => '020714',
                'kode_pos' => '43284',
                'nomor_telepon' => '0263-227xxxx',
                'email' => 'smpn1sukaluyu@yahoo.co.id',
                'website' => null,
                'lintang' => '-6.815000', 
                'bujur' => '107.229000',
                'kecamatan' => 'Sukaluyu',
                'kabupaten_kota' => 'Kab. Cianjur',
                'provinsi' => 'Jawa Barat',
                'logo' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('sekolahs')->insert($dataSekolah);
    }
}