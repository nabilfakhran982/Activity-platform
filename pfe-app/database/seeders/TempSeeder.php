<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Center;
use App\Models\Activity;
use App\Models\ActivityImage;
use App\Models\Schedule;

class TempSeeder extends Seeder
{
    public function run(): void
    {
        // ===== CENTERS (user_id=6 = Rami Khoury) =====
        $c1 = Center::create([
            'user_id'     => 6,
            'name'        => 'Cedar Saida Club',
            'description' => 'A premier sports and fitness center in the heart of Saida, offering swimming, martial arts, and fitness programs for all ages.',
            'address'     => 'Riad El Solh Street, Saida',
            'city'        => 'Saida',
            'phone'       => '+961 7 123 456',
            'lat'         => 33.5571,
            'lng'         => 35.3729,
            'is_active'   => true,
        ]);

        $c2 = Center::create([
            'user_id'     => 6,
            'name'        => 'Cedar Khalde Sports',
            'description' => 'Modern sports facility in Khalde featuring Olympic swimming pool, karate dojo, and padel courts with stunning sea views.',
            'address'     => 'Airport Road, Khalde',
            'city'        => 'Khalde',
            'phone'       => '+961 5 456 789',
            'lat'         => 33.7500,
            'lng'         => 35.4800,
            'is_active'   => true,
        ]);

        $c3 = Center::create([
            'user_id'     => 6,
            'name'        => 'Cedar Damour Center',
            'description' => 'A family-friendly activity center in Damour offering swimming, martial arts, fitness, and outdoor activities surrounded by nature.',
            'address'     => 'Main Road, Damour',
            'city'        => 'Damour',
            'phone'       => '+961 5 789 012',
            'lat'         => 33.6800,
            'lng'         => 35.4500,
            'is_active'   => true,
        ]);

        // ===== ACTIVITIES =====
        $acts = [

            // ----- Saida -----
            [
                'center_id' => $c1->id, 'category_id' => 4,
                'title'       => 'Kids Swimming — Saida',
                'description' => 'Beginner swimming lessons for children in Saida. Heated indoor pool with certified instructors.',
                'min_age' => 4, 'max_age' => 12, 'price' => 30.00, 'capacity' => 8, 'level' => 'beginner', 'is_private' => false,
                'schedules' => [['monday','15:00','16:00'],['wednesday','15:00','16:00'],['saturday','09:00','10:00']],
            ],
            [
                'center_id' => $c1->id, 'category_id' => 4,
                'title'       => 'Adult Swimming — Saida',
                'description' => 'Swimming lessons for adults of all levels. Focus on technique, endurance, and water confidence.',
                'min_age' => 16, 'max_age' => null, 'price' => 28.00, 'capacity' => 10, 'level' => 'beginner', 'is_private' => false,
                'schedules' => [['tuesday','07:00','08:00'],['thursday','07:00','08:00'],['saturday','11:00','12:00']],
            ],
            [
                'center_id' => $c1->id, 'category_id' => 1,
                'title'       => 'Karate — Kids Beginners',
                'description' => 'Fun karate classes for kids in Saida. Discipline, fitness, and confidence in a safe environment.',
                'min_age' => 5, 'max_age' => 12, 'price' => 22.00, 'capacity' => 15, 'level' => 'beginner', 'is_private' => false,
                'schedules' => [['monday','16:00','17:00'],['wednesday','16:00','17:00'],['saturday','10:00','11:00']],
            ],
            [
                'center_id' => $c1->id, 'category_id' => 6,
                'title'       => 'Fitness & Gym — Saida',
                'description' => 'High-energy fitness classes combining strength and cardio training for adults.',
                'min_age' => 16, 'max_age' => null, 'price' => 20.00, 'capacity' => 20, 'level' => 'beginner', 'is_private' => false,
                'schedules' => [['monday','07:00','08:00'],['wednesday','19:00','20:00'],['friday','07:00','08:00']],
            ],

            // ----- Khalde -----
            [
                'center_id' => $c2->id, 'category_id' => 4,
                'title'       => 'Swimming — Khalde',
                'description' => 'Olympic pool swimming lessons in Khalde. All levels welcome, from beginners to competitive swimmers.',
                'min_age' => null, 'max_age' => null, 'price' => 35.00, 'capacity' => 12, 'level' => 'beginner', 'is_private' => false,
                'schedules' => [['tuesday','15:00','16:00'],['thursday','15:00','16:00'],['sunday','09:00','10:00']],
            ],
            [
                'center_id' => $c2->id, 'category_id' => 1,
                'title'       => 'Karate — All Levels',
                'description' => 'Karate classes for all levels in Khalde. Learn from certified black belt instructors.',
                'min_age' => null, 'max_age' => null, 'price' => 28.00, 'capacity' => 12, 'level' => 'beginner', 'is_private' => false,
                'schedules' => [['monday','18:00','19:30'],['wednesday','18:00','19:30'],['saturday','10:00','11:30']],
            ],
            [
                'center_id' => $c2->id, 'category_id' => 11,
                'title'       => 'Padel — Khalde',
                'description' => 'Padel sessions with sea views in Khalde. Open to all levels, equipment provided.',
                'min_age' => 14, 'max_age' => null, 'price' => 35.00, 'capacity' => 4, 'level' => 'beginner', 'is_private' => false,
                'schedules' => [['tuesday','19:00','20:00'],['friday','18:00','19:00'],['sunday','10:00','11:00']],
            ],
            [
                'center_id' => $c2->id, 'category_id' => 2,
                'title'       => 'Boxing — Khalde',
                'description' => 'Boxing fundamentals and conditioning in Khalde. Great for fitness and self-defense.',
                'min_age' => 14, 'max_age' => null, 'price' => 30.00, 'capacity' => 12, 'level' => 'beginner', 'is_private' => false,
                'schedules' => [['monday','07:00','08:00'],['thursday','18:00','19:30'],['saturday','09:00','10:30']],
            ],

            // ----- Damour -----
            [
                'center_id' => $c3->id, 'category_id' => 4,
                'title'       => 'Kids Swimming — Damour',
                'description' => 'Fun and safe swimming lessons for children in Damour. Small groups and patient instructors.',
                'min_age' => 4, 'max_age' => 12, 'price' => 28.00, 'capacity' => 8, 'level' => 'beginner', 'is_private' => false,
                'schedules' => [['saturday','09:00','10:00'],['sunday','09:00','10:00']],
            ],
            [
                'center_id' => $c3->id, 'category_id' => 1,
                'title'       => 'Private Karate — Damour',
                'description' => 'One-on-one karate training in Damour. Personalized program for any age or skill level.',
                'min_age' => null, 'max_age' => null, 'price' => 65.00, 'capacity' => 1, 'level' => 'beginner', 'is_private' => true,
                'schedules' => [['monday','09:00','10:00'],['wednesday','09:00','10:00'],['friday','09:00','10:00']],
            ],
            [
                'center_id' => $c3->id, 'category_id' => 7,
                'title'       => 'Outdoor Adventure — Damour',
                'description' => 'Exciting outdoor activity sessions in Damour surrounded by nature. Hiking, climbing, and team challenges.',
                'min_age' => 10, 'max_age' => null, 'price' => 35.00, 'capacity' => 15, 'level' => 'beginner', 'is_private' => false,
                'schedules' => [['saturday','08:00','11:00'],['sunday','08:00','11:00']],
            ],
            [
                'center_id' => $c3->id, 'category_id' => 5,
                'title'       => 'Pilates — Damour',
                'description' => 'Relaxing pilates sessions in Damour. Core strength, flexibility, and mindful movement in a peaceful setting.',
                'min_age' => null, 'max_age' => null, 'price' => 30.00, 'capacity' => 10, 'level' => 'beginner', 'is_private' => false,
                'schedules' => [['tuesday','09:00','10:00'],['thursday','09:00','10:00'],['saturday','11:00','12:00']],
            ],
        ];

        // Image mapping by category_id
        $imageMap = [
            1  => 'martial-arts.jpg',
            2  => 'boxing.jpg',
            4  => 'swimming.jpg',
            5  => 'pilates.jpg',
            6  => 'fitness-gym.jpg',
            7  => 'adventure-outdoor.jpg',
            11 => 'padel.jpg',
        ];

        foreach ($acts as $item) {
            $schedules = $item['schedules'];
            unset($item['schedules']);

            $act = Activity::create(array_merge($item, ['is_active' => true]));

            ActivityImage::create([
                'activity_id' => $act->id,
                'image_path'  => 'images/activities/' . ($imageMap[$item['category_id']] ?? 'martial-arts.jpg'),
            ]);

            foreach ($schedules as $s) {
                Schedule::create([
                    'activity_id' => $act->id,
                    'day_of_week' => $s[0],
                    'start_time'  => $s[1],
                    'end_time'    => $s[2],
                ]);
            }

            $this->command->info("Created: {$act->title}");
        }

        $this->command->info("Done! Centers: {$c1->id}, {$c2->id}, {$c3->id}");
    }
}
