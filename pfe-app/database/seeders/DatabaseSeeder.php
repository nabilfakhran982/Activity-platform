<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Keep default scaffold seeders minimal.
        // (Factories can be used later when we add real models.)
        // User::factory(10)->create();
        // User::factory()->create([...]);
    }
}

