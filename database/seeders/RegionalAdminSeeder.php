<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\PegawaiKcd;
use App\Models\Instansi;
use Illuminate\Support\Facades\Hash;

class RegionalAdminSeeder extends Seeder
{
    /**
     * Membuat akun contoh Admin Wilayah VI untuk pengujian.
     */
    public function run(): void
    {
        // 1. Cari Instansi Wilayah VI berdasarkan UUID dari SIAKAD
        $instansiVi = Instansi::where('cadisdik_id', 'd8e9a0c1-b2d3-4e5f-9a0b-1c2d3e4f5a6b')->first();

        if (!$instansiVi) {
            $this->command->error('Instansi Wilayah VI tidak ditemukan. Pastikan CadisdikSeeder dan InstansiSeeder sudah dijalankan!');
            return;
        }

        // 2. Bikin Akun Admin Wilayah VI
        $user = User::updateOrCreate(
            ['username' => 'admin6'],
            [
                'name'        => 'Admin Wilayah VI',
                'email'       => 'admin6@kcd.system',
                'password'    => Hash::make('admin123'),
                'role'        => 'admin', // Role untuk Admin Wilayah
                'instansi_id' => $instansiVi->id,
            ]
        );

        // 3. Hubungkan ke data detail Pegawai (KCD butuh data ini untuk relasi)
        PegawaiKcd::updateOrCreate(
            ['user_id' => $user->id],
            [
                'instansi_id' => $instansiVi->id,
                'nama'        => 'Petugas Admin Wilayah VI',
                'nip'         => '199001010000000006',
                'jabatan'     => 'Admin Wilayah', // Kolom WAJIB di migrasi
                // Pangkat & Golongan dihilangkan karena tidak ada di migrasi
            ]
        );

        // Update user agar punya pegawai_kcd_id
        $user->update([
            'pegawai_kcd_id' => PegawaiKcd::where('user_id', $user->id)->first()->id
        ]);
    }
}
