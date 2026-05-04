<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('categories')->insert([
            ['id' => 1, 'name' => 'Martial Arts', 'slug' => 'martial-arts', 'icon' => 'martial-arts.png'],
            ['id' => 2, 'name' => 'Boxing', 'slug' => 'boxing', 'icon' => 'boxing.png'],
            ['id' => 3, 'name' => 'Arts & Crafts', 'slug' => 'arts-crafts', 'icon' => 'arts-crafts.png'],
            ['id' => 4, 'name' => 'Swimming', 'slug' => 'swimming', 'icon' => 'swimming.png'],
            ['id' => 5, 'name' => 'Pilates', 'slug' => 'pilates', 'icon' => 'pilates.png'],
            ['id' => 6, 'name' => 'Fitness & Gym', 'slug' => 'fitness-gym', 'icon' => 'fitness-gym.png'],
            ['id' => 7, 'name' => 'Adventure & Outdoor', 'slug' => 'adventure-outdoor', 'icon' => 'adventure-outdoor.png'],
            ['id' => 8, 'name' => 'Football', 'slug' => 'football', 'icon' => 'football.png'],
            ['id' => 9, 'name' => 'Basketball', 'slug' => 'basketball', 'icon' => 'basketball.png'],
            ['id' => 10, 'name' => 'Tennis', 'slug' => 'tennis', 'icon' => 'tennis.png'],
            ['id' => 11, 'name' => 'Padel', 'slug' => 'padel', 'icon' => 'padel.png'],
            ['id' => 12, 'name' => 'Skiing & Snowboarding', 'slug' => 'skiing-snowboarding', 'icon' => 'skiing.png'],
            ['id' => 13, 'name' => 'Cooking Classes', 'slug' => 'cooking-classes', 'icon' => 'cooking.png'],
            ['id' => 14, 'name' => 'Photography', 'slug' => 'photography', 'icon' => 'photography.png'],
            ['id' => 15, 'name' => 'Language Learning', 'slug' => 'languages', 'icon' => 'languages.png'],
            ['id' => 16, 'name' => 'Coding & Robotics', 'slug' => 'coding-robotics', 'icon' => 'coding.png'],
            ['id' => 17, 'name' => 'Water Sports', 'slug' => 'water-sports', 'icon' => 'water-sports.png'],
            ['id' => 18, 'name' => 'Drama & Acting', 'slug' => 'drama-acting', 'icon' => 'drama.png'],
        ]);
    }
}
