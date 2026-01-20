<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class KasubagUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat atau memperbarui akun Kasubag
        // Role diset 'Kasubag' agar sinkron dengan logika di VerifikasiController
        User::updateOrCreate(
            ['username' => 'kasubag'], // Cek berdasarkan username
            [
                'name'     => 'Kasubag',
                'email'    => 'kasubag@kcd.system',
                'password' => Hash::make('kasubag123'), // Password Default
                'role'     => 'Kasubag', // Role spesifik Kasubag
            ]
        );
    }
}