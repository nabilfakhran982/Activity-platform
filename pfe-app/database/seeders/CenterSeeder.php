<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Center;

class CenterSeeder extends Seeder
{
    public function run(): void
    {
        Center::create([
            'user_id'     => 2,
            'name'        => 'Dragon Academy',
            'description' => 'Premier martial arts academy in Beirut specializing in karate and self-defense for all ages.',
            'address'     => 'Rue Gouraud, Gemmayzeh',
            'city'        => 'Beirut',
            'phone'       => '+961 1 000 001',
            'lat'         => 33.8938,
            'lng'         => 35.5180,
        ]);

        Center::create([
            'user_id'     => 3,
            'name'        => 'Zen Studio',
            'description' => 'A calm and welcoming space for pilates, yoga, and mindful movement.',
            'address'     => 'Hamra Street, Hamra',
            'city'        => 'Beirut',
            'phone'       => '+961 1 000 002',
            'lat'         => 33.8959,
            'lng'         => 35.4784,
        ]);

        Center::create([
            'user_id'     => 4,
            'name'        => 'Fight Club Gym',
            'description' => 'High-energy boxing and fitness gym for all levels.',
            'address'     => 'Mar Mikhael Avenue',
            'city'        => 'Beirut',
            'phone'       => '+961 1 000 003',
            'lat'         => 33.8900,
            'lng'         => 35.5300,
        ]);
    }
}
