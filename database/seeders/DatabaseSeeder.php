<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin Utama
        User::create([
            'name' => 'Administrator Superseed',
            'username' => 'admin',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // 2. Akun Orang Tua / Wali Murid Dummy
        User::create([
            'name' => 'Budi Santoso (Ortu Davi)',
            'username' => 'ortu_davi',
            'password' => Hash::make('password123'),
            'role' => 'wali_murid',
        ]);
    }
}