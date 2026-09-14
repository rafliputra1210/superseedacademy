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
        $adminPassword = env('ADMIN_PASSWORD', 'SSA_AdminSecure2026!');
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator Superseed',
                'password' => Hash::make($adminPassword),
                'role' => 'admin',
            ]
        );

        // 2. Akun Orang Tua / Wali Murid Dummy (Hanya untuk testing)
        if (app()->environment('local', 'testing')) {
            User::updateOrCreate(
                ['username' => 'ortu_davi'],
                [
                    'name' => 'Budi Santoso (Ortu Davi)',
                    'password' => Hash::make('ortu_SSA2026!'),
                    'role' => 'wali_murid',
                ]
            );
        }
    }
}