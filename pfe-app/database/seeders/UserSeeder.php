<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('users')->insert([
            // Admin
            [
                'id' => 1,
                'name' => 'Admin Activio',
                'email' => 'admin@activio.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '+961 70 000 000',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Center owners
            [
                'id' => 2,
                'name' => 'Ahmad Khalil',
                'email' => 'ahmad@dragonacademy.lb',
                'password' => Hash::make('password'),
                'role' => 'center_owner',
                'phone' => '+961 70 111 001',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Lara Nassar',
                'email' => 'lara@zenstudio.lb',
                'password' => Hash::make('password'),
                'role' => 'center_owner',
                'phone' => '+961 71 222 002',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Karim Haddad',
                'email' => 'karim@fightclub.lb',
                'password' => Hash::make('password'),
                'role' => 'center_owner',
                'phone' => '+961 76 333 003',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'name' => 'Maya Frem',
                'email' => 'maya@aqualebanon.lb',
                'password' => Hash::make('password'),
                'role' => 'center_owner',
                'phone' => '+961 70 444 004',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'name' => 'Rami Khoury',
                'email' => 'rami@cedarsports.lb',
                'password' => Hash::make('password'),
                'role' => 'center_owner',
                'phone' => '+961 71 555 005',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'name' => 'Nadia Saab',
                'email' => 'nadia@creativeminds.lb',
                'password' => Hash::make('password'),
                'role' => 'center_owner',
                'phone' => '+961 76 666 006',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Regular users للـ demo
            [
                'id' => 8,
                'name' => 'Sara Khalil',
                'email' => 'sara@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'phone' => '+961 70 777 007',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 9,
                'name' => 'Omar Mansour',
                'email' => 'omar@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'phone' => '+961 71 888 008',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 10,
                'name' => 'Lina Karam',
                'email' => 'lina@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'phone' => '+961 76 999 009',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
