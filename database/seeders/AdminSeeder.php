<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin Weekendplan',
            'username' => 'adminweekendplan',
            'email' => 'adminweekendplan@gmail.com',
            'password' => Hash::make('admin123'), // Ganti dengan password aman
            'email_verified_at' => now(), // Anggap langsung verifikasi
            'role' => 'admin',
        ]);
    }
}
