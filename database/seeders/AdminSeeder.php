<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pengguna;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Cegah dobel admin
        if (Pengguna::where('username', 'admin')->exists()) {
            return;
        }

        Pengguna::create([
            'pengguna_id'        => (string) Str::uuid(),
            'sekolah_id'         => null, // isi kalau sudah ada sekolah
            'username'           => 'admin',
            'nama'               => 'Administrator',
            'password'           => Hash::make('admin123'), // ganti setelah login
            'peran_id_str'       => 'Admin',

            'alamat'             => null,
            'no_telepon'         => null,
            'no_hp'              => null,

            'ptk_id'             => null,
            'peserta_didik_id'   => null,
        ]);
    }
}
