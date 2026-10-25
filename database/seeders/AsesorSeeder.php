<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AsesorSeeder extends Seeder
{
    public function run()
    {
        // Membuat 4 Akun Asesor tanpa kolom email
        for ($i = 1; $i <= 4; $i++) {
            User::updateOrCreate(
                ['username' => 'asesor' . $i], // Username login: asesor1, asesor2, dst.
                [
                    'name'     => 'Asesor Lapangan ' . $i,
                    // 'email'    => 'asesor'.$i.'@superseedacademy.id', // BARIS INI DIHAPUS/DICOMMENT
                    'password' => Hash::make('password123'), // Password default: password123
                    'role'     => 'asesor',
                ]
            );
        }
    }
}