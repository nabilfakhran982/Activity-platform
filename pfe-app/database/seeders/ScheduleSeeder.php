<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Schedule;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        // Activity 1 — Kids Karate
        Schedule::create(['activity_id' => 1, 'day_of_week' => 'monday',    'start_time' => '16:00', 'end_time' => '17:00']);
        Schedule::create(['activity_id' => 1, 'day_of_week' => 'wednesday', 'start_time' => '16:00', 'end_time' => '17:00']);
        Schedule::create(['activity_id' => 1, 'day_of_week' => 'saturday',  'start_time' => '10:00', 'end_time' => '11:00']);

        // Activity 2 — Adult Karate
        Schedule::create(['activity_id' => 2, 'day_of_week' => 'tuesday',   'start_time' => '19:00', 'end_time' => '20:30']);
        Schedule::create(['activity_id' => 2, 'day_of_week' => 'thursday',  'start_time' => '19:00', 'end_time' => '20:30']);

        // Activity 3 — Morning Pilates
        Schedule::create(['activity_id' => 3, 'day_of_week' => 'monday',    'start_time' => '07:00', 'end_time' => '08:00']);
        Schedule::create(['activity_id' => 3, 'day_of_week' => 'wednesday', 'start_time' => '07:00', 'end_time' => '08:00']);
        Schedule::create(['activity_id' => 3, 'day_of_week' => 'friday',    'start_time' => '07:00', 'end_time' => '08:00']);

        // Activity 4 — Private Pilates
        Schedule::create(['activity_id' => 4, 'day_of_week' => 'tuesday',   'start_time' => '10:00', 'end_time' => '11:00']);
        Schedule::create(['activity_id' => 4, 'day_of_week' => 'saturday',  'start_time' => '09:00', 'end_time' => '10:00']);

        // Activity 5 — Boxing Beginners
        Schedule::create(['activity_id' => 5, 'day_of_week' => 'monday',    'start_time' => '18:00', 'end_time' => '19:00']);
        Schedule::create(['activity_id' => 5, 'day_of_week' => 'wednesday', 'start_time' => '18:00', 'end_time' => '19:00']);
        Schedule::create(['activity_id' => 5, 'day_of_week' => 'saturday',  'start_time' => '11:00', 'end_time' => '12:00']);

        // Activity 6 — Fitness & Conditioning
        Schedule::create(['activity_id' => 6, 'day_of_week' => 'tuesday',   'start_time' => '18:00', 'end_time' => '19:00']);
        Schedule::create(['activity_id' => 6, 'day_of_week' => 'thursday',  'start_time' => '18:00', 'end_time' => '19:00']);
        Schedule::create(['activity_id' => 6, 'day_of_week' => 'friday',    'start_time' => '17:00', 'end_time' => '18:00']);
    }
}
