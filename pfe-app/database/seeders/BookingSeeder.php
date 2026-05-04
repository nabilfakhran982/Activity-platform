<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('reviews')->truncate();
        DB::table('bookings')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Schedule IDs:
        // Act 1 (Kids Karate):        1,2,3
        // Act 2 (Adult Karate):       4,5,6
        // Act 4 (Morning Pilates):    10,11,12
        // Act 5 (Reformer Pilates):   13,14,15
        // Act 7 (Boxing Beginners):   20,21,22
        // Act 8 (Kids Boxing):        23,24,25
        // Act 9 (Fitness):            26,27,28,29,30
        // Act 11 (Kids Swimming):     34,35,36,37
        // Act 12 (Adult Swimming):    38,39,40
        // Act 14 (Football):          45,46,47
        // Act 18 (Painting):          58,59
        // Act 19 (Photography):       60,61
        // Act 22 (Cooking):           66,67

        $bookings = [

            // ===== Sara (user_id=8) =====
            // confirmed + reviewed
            ['id' => 1,  'user_id' => 8,  'schedule_id' => 1,  'status' => 'confirmed', 'booking_date' => '2026-04-07', 'notes' => null],
            // confirmed + reviewed
            ['id' => 2,  'user_id' => 8,  'schedule_id' => 10, 'status' => 'confirmed', 'booking_date' => '2026-04-07', 'notes' => null],
            // confirmed no review
            ['id' => 3,  'user_id' => 8,  'schedule_id' => 20, 'status' => 'confirmed', 'booking_date' => '2026-04-14', 'notes' => null],
            // pending
            ['id' => 4,  'user_id' => 8,  'schedule_id' => 45, 'status' => 'pending',   'booking_date' => '2026-04-28', 'notes' => null],
            // cancelled
            ['id' => 5,  'user_id' => 8,  'schedule_id' => 34, 'status' => 'cancelled', 'booking_date' => '2026-04-10', 'notes' => null],

            // ===== Omar (user_id=9) =====
            // confirmed + reviewed
            ['id' => 6,  'user_id' => 9,  'schedule_id' => 4,  'status' => 'confirmed', 'booking_date' => '2026-04-07', 'notes' => null],
            // confirmed + reviewed
            ['id' => 7,  'user_id' => 9,  'schedule_id' => 13, 'status' => 'confirmed', 'booking_date' => '2026-04-07', 'notes' => null],
            // confirmed + reviewed
            ['id' => 8,  'user_id' => 9,  'schedule_id' => 26, 'status' => 'confirmed', 'booking_date' => '2026-04-14', 'notes' => null],
            // confirmed no review
            ['id' => 9,  'user_id' => 9,  'schedule_id' => 38, 'status' => 'confirmed', 'booking_date' => '2026-04-21', 'notes' => null],
            // pending
            ['id' => 10, 'user_id' => 9,  'schedule_id' => 60, 'status' => 'pending',   'booking_date' => '2026-04-28', 'notes' => null],
            // cancelled
            ['id' => 11, 'user_id' => 9,  'schedule_id' => 46, 'status' => 'cancelled', 'booking_date' => '2026-04-10', 'notes' => null],

            // ===== Lina (user_id=10) =====
            // confirmed + reviewed
            ['id' => 12, 'user_id' => 10, 'schedule_id' => 2,  'status' => 'confirmed', 'booking_date' => '2026-04-07', 'notes' => null],
            // confirmed + reviewed
            ['id' => 13, 'user_id' => 10, 'schedule_id' => 11, 'status' => 'confirmed', 'booking_date' => '2026-04-07', 'notes' => null],
            // confirmed + reviewed
            ['id' => 14, 'user_id' => 10, 'schedule_id' => 35, 'status' => 'confirmed', 'booking_date' => '2026-04-14', 'notes' => null],
            // confirmed no review
            ['id' => 15, 'user_id' => 10, 'schedule_id' => 23, 'status' => 'confirmed', 'booking_date' => '2026-04-21', 'notes' => null],
            // pending
            ['id' => 16, 'user_id' => 10, 'schedule_id' => 66, 'status' => 'pending',   'booking_date' => '2026-04-28', 'notes' => null],
            // cancelled
            ['id' => 17, 'user_id' => 10, 'schedule_id' => 58, 'status' => 'cancelled', 'booking_date' => '2026-04-10', 'notes' => null],
        ];

        foreach ($bookings as $booking) {
            DB::table('bookings')->insert(array_merge($booking, [
                'created_at' => now()->subDays(rand(1, 21)),
                'updated_at' => now(),
            ]));
        }

        // ===== REVIEWS (confirmed bookings only) =====
        $reviews = [
            // Sara reviews (bookings 1, 2)
            [
                'booking_id' => 1, 'user_id' => 8, 'activity_id' => 1,
                'rating'  => 5,
                'comment' => 'Absolutely amazing karate class for my daughter! The instructor is patient, encouraging, and very professional. She looks forward to every session.',
            ],
            [
                'booking_id' => 2, 'user_id' => 8, 'activity_id' => 4,
                'rating'  => 5,
                'comment' => 'The morning Pilates sessions at Zen Studio are a game changer. The instructor really knows how to push you without overwhelming you. Highly recommend!',
            ],

            // Omar reviews (bookings 6, 7, 8)
            [
                'booking_id' => 6, 'user_id' => 9, 'activity_id' => 2,
                'rating'  => 4,
                'comment' => 'Great intermediate karate class. The instructor is knowledgeable and the group is very motivated. Would love even more sparring time.',
            ],
            [
                'booking_id' => 7, 'user_id' => 9, 'activity_id' => 5,
                'rating'  => 5,
                'comment' => 'The Reformer Pilates class is challenging but incredibly rewarding. My posture and core strength have improved significantly after just a few sessions.',
            ],
            [
                'booking_id' => 8, 'user_id' => 9, 'activity_id' => 9,
                'rating'  => 4,
                'comment' => 'Excellent fitness conditioning class. The trainer pushes you hard but always with proper form in mind. Great energy in the group.',
            ],

            // Lina reviews (bookings 12, 13, 14)
            [
                'booking_id' => 12, 'user_id' => 10, 'activity_id' => 1,
                'rating'  => 5,
                'comment' => 'My son loves the karate class at Dragon Academy! He has become much more disciplined and confident. The sensei is wonderful with kids.',
            ],
            [
                'booking_id' => 13, 'user_id' => 10, 'activity_id' => 4,
                'rating'  => 4,
                'comment' => 'Great morning Pilates class — well-structured and the instructor explains every movement clearly. The studio has a very calm and welcoming atmosphere.',
            ],
            [
                'booking_id' => 14, 'user_id' => 10, 'activity_id' => 11,
                'rating'  => 5,
                'comment' => 'My child is thriving in the swimming program at Aqua Lebanon. The instructors are incredibly patient and the pool facilities are top-notch.',
            ],
        ];

        foreach ($reviews as $review) {
            DB::table('reviews')->insert(array_merge($review, [
                'created_at' => now()->subDays(rand(1, 7)),
                'updated_at' => now(),
            ]));
        }
    }
}
