<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun ADMINISTRATOR (Super Admin)
        // Ini yang punya akses full ke menu Settings
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'], // Kunci pencarian biar gak duplikat
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password'), // Password default: password
                'role' => 'Administrator', // 🔥 HARUS 'Administrator' (Sesuai role strict tadi)
            ]
        );
    }
}