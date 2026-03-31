<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder {
    public function run(): void {
        $categories = [
            ['name' => 'Martial Arts',        'slug' => 'martial-arts',      'icon' => '🥋'],
            ['name' => 'Boxing',              'slug' => 'boxing',            'icon' => '🥊'],
            ['name' => 'Arts & Crafts',       'slug' => 'arts-crafts',       'icon' => '🎨'],
            ['name' => 'Swimming',            'slug' => 'swimming',          'icon' => '🏊'],
            ['name' => 'Pilates',             'slug' => 'pilates',           'icon' => '🧘'],
            ['name' => 'Fitness & Gym',       'slug' => 'fitness-gym',       'icon' => '🏋️'],
            ['name' => 'Adventure & Outdoor', 'slug' => 'adventure-outdoor', 'icon' => '🏞️'],
            ['name' => 'Football',            'slug' => 'football',          'icon' => '⚽'],
        ];
        DB::table('categories')->insert($categories);
    }
}