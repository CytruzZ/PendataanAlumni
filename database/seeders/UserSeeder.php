<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Admin Account
        User::updateOrCreate(
            ['email' => 'admin'],
            [
                'name' => 'Admin MNI IPB',
                'role' => 'admin',
                'nim_nip' => '198501012010121001',
                'no_wa' => '6281575006649',
                'password' => Hash::make('456'),
            ]
        );

        // Student Account
        User::updateOrCreate(
            ['email' => 'mahasiswa'],
            [
                'name' => 'Mahasiswa MNI IPB',
                'role' => 'mahasiswa',
                'nim_nip' => 'J3A120001',
                'no_wa' => '6285800001111',
                'password' => Hash::make('123'),
            ]
        );
    }
}
