<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name'     => 'Admin Activio',
            'email'    => 'admin@activio.com',
            'password' => Hash::make('password'),
            'phone'    => '+961 70 000 001',
            'role'     => 'admin',
        ]);

        // Center Owners
        User::create([
            'name'     => 'Dragon Academy',
            'email'    => 'dragon@activio.com',
            'password' => Hash::make('password'),
            'phone'    => '+961 70 000 002',
            'role'     => 'center_owner',
        ]);

        User::create([
            'name'     => 'Zen Studio',
            'email'    => 'zen@activio.com',
            'password' => Hash::make('password'),
            'phone'    => '+961 70 000 003',
            'role'     => 'center_owner',
        ]);

        User::create([
            'name'     => 'Fight Club Gym',
            'email'    => 'fightclub@activio.com',
            'password' => Hash::make('password'),
            'phone'    => '+961 70 000 004',
            'role'     => 'center_owner',
        ]);

        // Regular Users
        User::create([
            'name'     => 'Sara Khalil',
            'email'    => 'sara@gmail.com',
            'password' => Hash::make('password'),
            'phone'    => '+961 71 000 001',
            'role'     => 'user',
        ]);

        User::create([
            'name'     => 'Omar Nassar',
            'email'    => 'omar@gmail.com',
            'password' => Hash::make('password'),
            'phone'    => '+961 71 000 002',
            'role'     => 'user',
        ]);
    }
}
