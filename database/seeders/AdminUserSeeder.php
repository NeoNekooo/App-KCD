<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bikin Akun Admin
        User::updateOrCreate(
            ['username' => 'admin'], // Cek apakah username 'admin' udah ada?
            [
                'name'     => 'Super Administrator',
                'email'    => 'admin@kcd.system',
                'password' => Hash::make('kcd123'), // Password Default
                'role'     => 'Admin', // Wajib 'Admin' biar menu muncul
            ]
        );
    }
}