<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activity;
use App\Models\ActivityImage;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        $activities = [
            [
                'data' => [
                    'center_id' => 1,
                    'category_id' => 1,
                    'title' => 'Kids Karate — Beginners',
                    'description' => 'Fun and structured karate classes for young beginners.',
                    'min_age' => 5,
                    'max_age' => 12,
                    'price' => 25.00,
                    'capacity' => 15,
                    'level' => 'beginner',
                    'is_private' => false,
                ],
                'image' => 'martial-arts.jpg',
            ],
            [
                'data' => [
                    'center_id' => 1,
                    'category_id' => 1,
                    'title' => 'Adult Karate — Intermediate',
                    'description' => 'Take your karate skills to the next level.',
                    'min_age' => 16,
                    'max_age' => null,
                    'price' => 30.00,
                    'capacity' => 12,
                    'level' => 'intermediate',
                    'is_private' => false,
                ],
                'image' => 'martial-arts.jpg',
            ],
            [
                'data' => [
                    'center_id' => 2,
                    'category_id' => 5,
                    'title' => 'Morning Pilates Flow',
                    'description' => 'A gentle morning pilates session.',
                    'min_age' => null,
                    'max_age' => null,
                    'price' => 30.00,
                    'capacity' => 10,
                    'level' => 'beginner',
                    'is_private' => false,
                ],
                'image' => 'pilates.jpg',
            ],
            [
                'data' => [
                    'center_id' => 2,
                    'category_id' => 5,
                    'title' => 'Private Pilates Session',
                    'description' => 'One-on-one pilates session.',
                    'min_age' => null,
                    'max_age' => null,
                    'price' => 60.00,
                    'capacity' => 1,
                    'level' => 'beginner',
                    'is_private' => true,
                ],
                'image' => 'pilates.jpg',
            ],
            [
                'data' => [
                    'center_id' => 3,
                    'category_id' => 2,
                    'title' => 'Boxing — Beginners',
                    'description' => 'Learn the fundamentals of boxing.',
                    'min_age' => 14,
                    'max_age' => null,
                    'price' => 28.00,
                    'capacity' => 12,
                    'level' => 'beginner',
                    'is_private' => false,
                ],
                'image' => 'boxing.jpg',
            ],
            [
                'data' => [
                    'center_id' => 3,
                    'category_id' => 6,
                    'title' => 'Fitness & Conditioning',
                    'description' => 'High-intensity fitness training.',
                    'min_age' => 16,
                    'max_age' => null,
                    'price' => 22.00,
                    'capacity' => 20,
                    'level' => 'intermediate',
                    'is_private' => false,
                ],
                'image' => 'fitness.jpg',
            ],
        ];

        foreach ($activities as $item) {
            $activity = Activity::create($item['data']);
            ActivityImage::create([
                'activity_id' => $activity->id,
                'image_path' => 'images/activities/' . $item['image'],
            ]);
        }
    }
}
