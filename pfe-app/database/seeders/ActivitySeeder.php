<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('activity_images')->truncate();
        DB::table('schedules')->truncate();
        DB::table('activities')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $activities = [

            // ===== DRAGON ACADEMY (center_id=1) — Martial Arts =====
            [
                'activity' => [
                    'id' => 1, 'center_id' => 1, 'category_id' => 1,
                    'title'       => 'Kids Karate — Beginners',
                    'description' => 'A fun and structured karate program designed for young beginners. We focus on discipline, focus, and physical fitness in a safe and encouraging environment. Students work toward their Yellow Belt within 3 months.',
                    'min_age' => 5, 'max_age' => 12, 'price' => 25.00,
                    'capacity' => 15, 'level' => 'beginner', 'is_private' => false, 'is_active' => true,
                ],
                'image' => 'martial-arts.jpg',
                'schedules' => [
                    ['day_of_week' => 'monday',    'start_time' => '16:00:00', 'end_time' => '17:00:00'],
                    ['day_of_week' => 'wednesday', 'start_time' => '16:00:00', 'end_time' => '17:00:00'],
                    ['day_of_week' => 'saturday',  'start_time' => '10:00:00', 'end_time' => '11:00:00'],
                ],
            ],
            [
                'activity' => [
                    'id' => 2, 'center_id' => 1, 'category_id' => 1,
                    'title'       => 'Adult Karate — Intermediate',
                    'description' => 'Take your karate skills to the next level. This program is tailored for adult practitioners holding a Yellow or Orange belt. We focus on refining technique, building speed, and developing powerful combinations.',
                    'min_age' => 16, 'max_age' => null, 'price' => 30.00,
                    'capacity' => 12, 'level' => 'intermediate', 'is_private' => false, 'is_active' => true,
                ],
                'image' => 'martial-arts.jpg',
                'schedules' => [
                    ['day_of_week' => 'tuesday',  'start_time' => '19:00:00', 'end_time' => '20:30:00'],
                    ['day_of_week' => 'thursday', 'start_time' => '19:00:00', 'end_time' => '20:30:00'],
                    ['day_of_week' => 'saturday', 'start_time' => '11:30:00', 'end_time' => '13:00:00'],
                ],
            ],
            [
                'activity' => [
                    'id' => 3, 'center_id' => 1, 'category_id' => 1,
                    'title'       => 'Private Karate Session',
                    'description' => 'One-on-one karate training with a certified instructor, fully tailored to your personal goals and current level. Ideal for those who want accelerated progress or personalized attention at any skill level.',
                    'min_age' => null, 'max_age' => null, 'price' => 70.00,
                    'capacity' => 1, 'level' => 'beginner', 'is_private' => true, 'is_active' => true,
                ],
                'image' => 'martial-arts.jpg',
                'schedules' => [
                    ['day_of_week' => 'monday',    'start_time' => '09:00:00', 'end_time' => '10:00:00'],
                    ['day_of_week' => 'wednesday', 'start_time' => '09:00:00', 'end_time' => '10:00:00'],
                    ['day_of_week' => 'friday',    'start_time' => '09:00:00', 'end_time' => '10:00:00'],
                ],
            ],

            // ===== ZEN STUDIO (center_id=2) — Pilates =====
            [
                'activity' => [
                    'id' => 4, 'center_id' => 2, 'category_id' => 5,
                    'title'       => 'Morning Pilates Flow',
                    'description' => 'Start your day the right way with an energizing morning Pilates session. We focus on core strength, flexibility, and posture improvement. Suitable for all fitness levels — no prior Pilates experience required.',
                    'min_age' => null, 'max_age' => null, 'price' => 30.00,
                    'capacity' => 10, 'level' => 'beginner', 'is_private' => false, 'is_active' => true,
                ],
                'image' => 'pilates.jpg',
                'schedules' => [
                    ['day_of_week' => 'monday',    'start_time' => '07:30:00', 'end_time' => '08:30:00'],
                    ['day_of_week' => 'wednesday', 'start_time' => '07:30:00', 'end_time' => '08:30:00'],
                    ['day_of_week' => 'friday',    'start_time' => '07:30:00', 'end_time' => '08:30:00'],
                ],
            ],
            [
                'activity' => [
                    'id' => 5, 'center_id' => 2, 'category_id' => 5,
                    'title'       => 'Reformer Pilates — Advanced',
                    'description' => 'A challenging full-body workout using the Pilates Reformer machine. This class targets deep stabilizing muscles, improves balance, and enhances athletic performance. Prior Pilates experience is required.',
                    'min_age' => 16, 'max_age' => null, 'price' => 45.00,
                    'capacity' => 6, 'level' => 'advanced', 'is_private' => false, 'is_active' => true,
                ],
                'image' => 'pilates.jpg',
                'schedules' => [
                    ['day_of_week' => 'tuesday',  'start_time' => '18:00:00', 'end_time' => '19:00:00'],
                    ['day_of_week' => 'thursday', 'start_time' => '18:00:00', 'end_time' => '19:00:00'],
                    ['day_of_week' => 'saturday', 'start_time' => '09:00:00', 'end_time' => '10:00:00'],
                ],
            ],
            [
                'activity' => [
                    'id' => 6, 'center_id' => 2, 'category_id' => 5,
                    'title'       => 'Private Pilates Session',
                    'description' => 'A fully personalized Pilates session with a Stott Pilates certified instructor. Whether you are recovering from an injury, managing back pain, or looking to improve athletic performance, this session is designed around your specific needs.',
                    'min_age' => null, 'max_age' => null, 'price' => 60.00,
                    'capacity' => 1, 'level' => 'beginner', 'is_private' => true, 'is_active' => true,
                ],
                'image' => 'pilates.jpg',
                'schedules' => [
                    ['day_of_week' => 'monday',    'start_time' => '10:00:00', 'end_time' => '11:00:00'],
                    ['day_of_week' => 'tuesday',   'start_time' => '10:00:00', 'end_time' => '11:00:00'],
                    ['day_of_week' => 'wednesday', 'start_time' => '10:00:00', 'end_time' => '11:00:00'],
                    ['day_of_week' => 'thursday',  'start_time' => '10:00:00', 'end_time' => '11:00:00'],
                ],
            ],

            // ===== FIGHT CLUB GYM (center_id=3) — Boxing & Fitness =====
            [
                'activity' => [
                    'id' => 7, 'center_id' => 3, 'category_id' => 2,
                    'title'       => 'Boxing — Beginners',
                    'description' => 'Learn the fundamentals of boxing in a safe and professional setting. We cover stance, footwork, basic punches, and defensive techniques. No prior experience needed — just bring your energy and motivation.',
                    'min_age' => 14, 'max_age' => null, 'price' => 28.00,
                    'capacity' => 12, 'level' => 'beginner', 'is_private' => false, 'is_active' => true,
                ],
                'image' => 'boxing.jpg',
                'schedules' => [
                    ['day_of_week' => 'monday',    'start_time' => '18:00:00', 'end_time' => '19:30:00'],
                    ['day_of_week' => 'wednesday', 'start_time' => '18:00:00', 'end_time' => '19:30:00'],
                    ['day_of_week' => 'saturday',  'start_time' => '10:00:00', 'end_time' => '11:30:00'],
                ],
            ],
            [
                'activity' => [
                    'id' => 8, 'center_id' => 3, 'category_id' => 2,
                    'title'       => 'Kids Boxing',
                    'description' => 'A fun and safe boxing program for kids that builds fitness, discipline, and self-confidence. No contact sparring — just skills, movement, and a healthy challenge in a fully supervised environment.',
                    'min_age' => 8, 'max_age' => 13, 'price' => 20.00,
                    'capacity' => 10, 'level' => 'beginner', 'is_private' => false, 'is_active' => true,
                ],
                'image' => 'boxing.jpg',
                'schedules' => [
                    ['day_of_week' => 'tuesday',  'start_time' => '16:00:00', 'end_time' => '17:00:00'],
                    ['day_of_week' => 'thursday', 'start_time' => '16:00:00', 'end_time' => '17:00:00'],
                    ['day_of_week' => 'saturday', 'start_time' => '09:00:00', 'end_time' => '10:00:00'],
                ],
            ],
            [
                'activity' => [
                    'id' => 9, 'center_id' => 3, 'category_id' => 6,
                    'title'       => 'Fitness & Conditioning',
                    'description' => 'A high-intensity functional fitness class combining strength and endurance training. Perfect for anyone looking to burn fat, build muscle, and improve overall athletic performance in a motivating group setting.',
                    'min_age' => 16, 'max_age' => null, 'price' => 22.00,
                    'capacity' => 20, 'level' => 'intermediate', 'is_private' => false, 'is_active' => true,
                ],
                'image' => 'fitness-gym.jpg',
                'schedules' => [
                    ['day_of_week' => 'monday',    'start_time' => '07:00:00', 'end_time' => '08:00:00'],
                    ['day_of_week' => 'tuesday',   'start_time' => '07:00:00', 'end_time' => '08:00:00'],
                    ['day_of_week' => 'wednesday', 'start_time' => '19:00:00', 'end_time' => '20:00:00'],
                    ['day_of_week' => 'thursday',  'start_time' => '19:00:00', 'end_time' => '20:00:00'],
                    ['day_of_week' => 'saturday',  'start_time' => '08:00:00', 'end_time' => '09:00:00'],
                ],
            ],
            [
                'activity' => [
                    'id' => 10, 'center_id' => 3, 'category_id' => 2,
                    'title'       => 'Private Boxing Session',
                    'description' => 'One-on-one boxing training with a professional coach. Fully customized to match your current skill level and personal goals — whether you are a complete beginner or preparing for competition.',
                    'min_age' => 14, 'max_age' => null, 'price' => 55.00,
                    'capacity' => 1, 'level' => 'beginner', 'is_private' => true, 'is_active' => true,
                ],
                'image' => 'boxing.jpg',
                'schedules' => [
                    ['day_of_week' => 'monday',    'start_time' => '14:00:00', 'end_time' => '15:00:00'],
                    ['day_of_week' => 'wednesday', 'start_time' => '14:00:00', 'end_time' => '15:00:00'],
                    ['day_of_week' => 'friday',    'start_time' => '14:00:00', 'end_time' => '15:00:00'],
                ],
            ],

            // ===== AQUA LEBANON (center_id=4) — Swimming =====
            [
                'activity' => [
                    'id' => 11, 'center_id' => 4, 'category_id' => 4,
                    'title'       => 'Kids Swimming — Beginners',
                    'description' => 'A beginner swimming program for young children taught by specialized instructors. We use engaging and encouraging methods to build water confidence and master fundamental swimming techniques in a heated indoor pool.',
                    'min_age' => 4, 'max_age' => 10, 'price' => 35.00,
                    'capacity' => 8, 'level' => 'beginner', 'is_private' => false, 'is_active' => true,
                ],
                'image' => 'swimming.jpg',
                'schedules' => [
                    ['day_of_week' => 'monday',    'start_time' => '15:00:00', 'end_time' => '16:00:00'],
                    ['day_of_week' => 'wednesday', 'start_time' => '15:00:00', 'end_time' => '16:00:00'],
                    ['day_of_week' => 'saturday',  'start_time' => '09:00:00', 'end_time' => '10:00:00'],
                    ['day_of_week' => 'sunday',    'start_time' => '09:00:00', 'end_time' => '10:00:00'],
                ],
            ],
            [
                'activity' => [
                    'id' => 12, 'center_id' => 4, 'category_id' => 4,
                    'title'       => 'Adult Swimming — All Levels',
                    'description' => 'Swimming lessons for adults covering all levels from beginner to intermediate. Sessions focus on improving stroke technique, breathing efficiency, and building endurance in our Olympic-sized heated indoor pool.',
                    'min_age' => 16, 'max_age' => null, 'price' => 30.00,
                    'capacity' => 10, 'level' => 'beginner', 'is_private' => false, 'is_active' => true,
                ],
                'image' => 'swimming.jpg',
                'schedules' => [
                    ['day_of_week' => 'tuesday',  'start_time' => '07:00:00', 'end_time' => '08:00:00'],
                    ['day_of_week' => 'thursday', 'start_time' => '07:00:00', 'end_time' => '08:00:00'],
                    ['day_of_week' => 'saturday', 'start_time' => '11:00:00', 'end_time' => '12:00:00'],
                ],
            ],
            [
                'activity' => [
                    'id' => 13, 'center_id' => 4, 'category_id' => 4,
                    'title'       => 'Competitive Swimming Training',
                    'description' => 'An intensive training program for talented swimmers aiming to compete at the Lebanese and regional level. Coached by a certified Lebanese Swimming Federation coach, this program focuses on performance, speed, and race strategy.',
                    'min_age' => 10, 'max_age' => 18, 'price' => 50.00,
                    'capacity' => 12, 'level' => 'advanced', 'is_private' => false, 'is_active' => true,
                ],
                'image' => 'swimming.jpg',
                'schedules' => [
                    ['day_of_week' => 'monday',    'start_time' => '06:00:00', 'end_time' => '07:30:00'],
                    ['day_of_week' => 'wednesday', 'start_time' => '06:00:00', 'end_time' => '07:30:00'],
                    ['day_of_week' => 'friday',    'start_time' => '06:00:00', 'end_time' => '07:30:00'],
                    ['day_of_week' => 'sunday',    'start_time' => '08:00:00', 'end_time' => '09:30:00'],
                ],
            ],

            // ===== CEDAR SPORTS CLUB (center_id=5) — Football, Tennis, Padel, Basketball =====
            [
                'activity' => [
                    'id' => 14, 'center_id' => 5, 'category_id' => 8,
                    'title'       => 'Football Academy — Kids',
                    'description' => 'A professional football training program for young players on a FIFA Pro certified artificial grass pitch. Training covers technical skills, team play, and basic tactical understanding in an encouraging and competitive environment.',
                    'min_age' => 6, 'max_age' => 14, 'price' => 40.00,
                    'capacity' => 20, 'level' => 'beginner', 'is_private' => false, 'is_active' => true,
                ],
                'image' => 'football.jpg',
                'schedules' => [
                    ['day_of_week' => 'tuesday',  'start_time' => '16:30:00', 'end_time' => '18:00:00'],
                    ['day_of_week' => 'thursday', 'start_time' => '16:30:00', 'end_time' => '18:00:00'],
                    ['day_of_week' => 'saturday', 'start_time' => '09:00:00', 'end_time' => '10:30:00'],
                ],
            ],
            [
                'activity' => [
                    'id' => 15, 'center_id' => 5, 'category_id' => 10,
                    'title'       => 'Tennis — Beginners & Kids',
                    'description' => 'Beginner tennis lessons for children and adults on floodlit hard courts. We teach the fundamentals: proper grip, basic strokes, footwork, and court movement — all in a fun and patient learning environment.',
                    'min_age' => 7, 'max_age' => null, 'price' => 45.00,
                    'capacity' => 4, 'level' => 'beginner', 'is_private' => false, 'is_active' => true,
                ],
                'image' => 'tennis.jpg',
                'schedules' => [
                    ['day_of_week' => 'monday',    'start_time' => '17:00:00', 'end_time' => '18:00:00'],
                    ['day_of_week' => 'wednesday', 'start_time' => '17:00:00', 'end_time' => '18:00:00'],
                    ['day_of_week' => 'saturday',  'start_time' => '10:00:00', 'end_time' => '11:00:00'],
                ],
            ],
            [
                'activity' => [
                    'id' => 16, 'center_id' => 5, 'category_id' => 11,
                    'title'       => 'Padel — Open Group Session',
                    'description' => 'An open group padel session welcoming all skill levels. Meet new players, develop your game, and enjoy one of the fastest-growing sports in Lebanon on our professional padel courts.',
                    'min_age' => 14, 'max_age' => null, 'price' => 35.00,
                    'capacity' => 4, 'level' => 'beginner', 'is_private' => false, 'is_active' => true,
                ],
                'image' => 'padel.jpg',
                'schedules' => [
                    ['day_of_week' => 'tuesday',  'start_time' => '19:00:00', 'end_time' => '20:00:00'],
                    ['day_of_week' => 'thursday', 'start_time' => '19:00:00', 'end_time' => '20:00:00'],
                    ['day_of_week' => 'friday',   'start_time' => '18:00:00', 'end_time' => '19:00:00'],
                    ['day_of_week' => 'sunday',   'start_time' => '10:00:00', 'end_time' => '11:00:00'],
                ],
            ],
            [
                'activity' => [
                    'id' => 17, 'center_id' => 5, 'category_id' => 9,
                    'title'       => 'Basketball Training — Youth',
                    'description' => 'Youth basketball training focused on developing core skills including passing, shooting, dribbling, and team defense. Sessions are competitive, fun, and coached by experienced basketball trainers.',
                    'min_age' => 10, 'max_age' => 18, 'price' => 38.00,
                    'capacity' => 16, 'level' => 'beginner', 'is_private' => false, 'is_active' => true,
                ],
                'image' => 'basketball.jpg',
                'schedules' => [
                    ['day_of_week' => 'monday',    'start_time' => '18:00:00', 'end_time' => '19:30:00'],
                    ['day_of_week' => 'wednesday', 'start_time' => '18:00:00', 'end_time' => '19:30:00'],
                    ['day_of_week' => 'saturday',  'start_time' => '11:00:00', 'end_time' => '12:30:00'],
                ],
            ],

            // ===== CREATIVE MINDS (center_id=6) =====
            [
                'activity' => [
                    'id' => 18, 'center_id' => 6, 'category_id' => 3,
                    'title'       => 'Painting & Drawing for Kids',
                    'description' => 'A creative arts workshop for children exploring different techniques including watercolor, acrylic, and charcoal. We nurture creativity, self-expression, and artistic confidence in a warm and inspiring studio environment.',
                    'min_age' => 5, 'max_age' => 14, 'price' => 20.00,
                    'capacity' => 12, 'level' => 'beginner', 'is_private' => false, 'is_active' => true,
                ],
                'image' => 'arts-crafts.jpg',
                'schedules' => [
                    ['day_of_week' => 'wednesday', 'start_time' => '15:00:00', 'end_time' => '16:30:00'],
                    ['day_of_week' => 'saturday',  'start_time' => '10:00:00', 'end_time' => '11:30:00'],
                ],
            ],
            [
                'activity' => [
                    'id' => 19, 'center_id' => 6, 'category_id' => 14,
                    'title'       => 'Photography Fundamentals',
                    'description' => 'A beginner photography course covering camera basics, exposure settings, composition, and light. Combines theory sessions with hands-on shoots around the streets of Beirut. Suitable for DSLR and mirrorless camera users.',
                    'min_age' => 14, 'max_age' => null, 'price' => 55.00,
                    'capacity' => 8, 'level' => 'beginner', 'is_private' => false, 'is_active' => true,
                ],
                'image' => 'photography.jpg',
                'schedules' => [
                    ['day_of_week' => 'saturday', 'start_time' => '14:00:00', 'end_time' => '16:00:00'],
                    ['day_of_week' => 'sunday',   'start_time' => '10:00:00', 'end_time' => '12:00:00'],
                ],
            ],
            [
                'activity' => [
                    'id' => 20, 'center_id' => 6, 'category_id' => 18,
                    'title'       => 'Drama & Theater for Kids',
                    'description' => 'A dynamic theater and acting program for children that develops self-confidence, communication skills, and creativity through role-playing exercises and seasonal stage performances. Open to all experience levels.',
                    'min_age' => 7, 'max_age' => 15, 'price' => 25.00,
                    'capacity' => 15, 'level' => 'beginner', 'is_private' => false, 'is_active' => true,
                ],
                'image' => 'drama.jpg',
                'schedules' => [
                    ['day_of_week' => 'tuesday',  'start_time' => '16:00:00', 'end_time' => '17:30:00'],
                    ['day_of_week' => 'saturday', 'start_time' => '11:00:00', 'end_time' => '12:30:00'],
                ],
            ],
            [
                'activity' => [
                    'id' => 21, 'center_id' => 6, 'category_id' => 16,
                    'title'       => 'Coding & Robotics for Kids',
                    'description' => 'An engaging coding and robotics program for children using Scratch and LEGO Mindstorms. We build logical thinking, problem-solving skills, and introduce programming concepts through hands-on projects and friendly challenges.',
                    'min_age' => 8, 'max_age' => 14, 'price' => 35.00,
                    'capacity' => 10, 'level' => 'beginner', 'is_private' => false, 'is_active' => true,
                ],
                'image' => 'coding.jpg',
                'schedules' => [
                    ['day_of_week' => 'wednesday', 'start_time' => '16:30:00', 'end_time' => '18:00:00'],
                    ['day_of_week' => 'saturday',  'start_time' => '13:00:00', 'end_time' => '14:30:00'],
                ],
            ],
            [
                'activity' => [
                    'id' => 22, 'center_id' => 6, 'category_id' => 13,
                    'title'       => 'Cooking Classes — Lebanese Cuisine',
                    'description' => 'Discover the secrets of authentic Lebanese cooking. From kibbeh and tabbouleh to pastries and traditional desserts — all taught in a fully equipped professional kitchen. Open to teens and adults of all cooking levels.',
                    'min_age' => 12, 'max_age' => null, 'price' => 40.00,
                    'capacity' => 8, 'level' => 'beginner', 'is_private' => false, 'is_active' => true,
                ],
                'image' => 'cooking.jpg',
                'schedules' => [
                    ['day_of_week' => 'friday', 'start_time' => '16:00:00', 'end_time' => '18:00:00'],
                    ['day_of_week' => 'sunday', 'start_time' => '11:00:00', 'end_time' => '13:00:00'],
                ],
            ],
        ];

        foreach ($activities as $item) {
            DB::table('activities')->insert(array_merge($item['activity'], [
                'created_at' => now(),
                'updated_at' => now(),
            ]));

            DB::table('activity_images')->insert([
                'activity_id' => $item['activity']['id'],
                'image_path'  => 'images/activities/' . $item['image'],
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            foreach ($item['schedules'] as $schedule) {
                DB::table('schedules')->insert(array_merge($schedule, [
                    'activity_id' => $item['activity']['id'],
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]));
            }
        }
    }
}
