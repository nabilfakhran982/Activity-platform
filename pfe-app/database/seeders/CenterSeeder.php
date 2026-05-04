<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CenterSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('centers')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('centers')->insert([
            // 1. Dragon Academy — Gemmayzeh, Beirut
            [
                'id' => 1,
                'user_id' => 2,
                'name' => 'Dragon Academy',
                'description' => 'مدرسة الكاراتيه والفنون القتالية الرائدة في بيروت. نقدم برامج تدريبية لجميع الأعمار بإشراف مدربين معتمدين دولياً. تأسست عام 2008 في قلب الجميزة.',
                'address' => 'Rue Gouraud, Gemmayzeh',
                'city' => 'Beirut',
                'phone' => '+961 1 566 789',
                'lat' => 33.8938,
                'lng' => 35.5180,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 2. Zen Studio — Hamra, Beirut
            [
                'id' => 2,
                'user_id' => 3,
                'name' => 'Zen Studio',
                'description' => 'استوديو البيلاتيس في قلب الحمرا. نوفر بيئة هادئة ومريحة لممارسة الرياضة الذهنية والجسدية. مدربات معتمدات من Stott Pilates الكندية.',
                'address' => 'Hamra Street, near Starbucks',
                'city' => 'Beirut',
                'phone' => '+961 1 345 678',
                'lat' => 33.8959,
                'lng' => 35.4784,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 3. Fight Club Gym — Mar Mikhael, Beirut
            [
                'id' => 3,
                'user_id' => 4,
                'name' => 'Fight Club Gym',
                'description' => 'صالة الملاكمة واللياقة البدنية في مار مخايل. بيئة عالية الطاقة مع معدات احترافية ومدربين خبراء. نرحب بالمبتدئين والمحترفين.',
                'address' => 'Armenia Street, Mar Mikhael',
                'city' => 'Beirut',
                'phone' => '+961 1 444 567',
                'lat' => 33.8900,
                'lng' => 35.5300,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 4. Aqua Lebanon — Jounieh
            [
                'id' => 4,
                'user_id' => 5,
                'name' => 'Aqua Lebanon',
                'description' => 'أكاديمية السباحة الأولى في جونيه. حمامات سباحة أولمبية مغطاة ومدفأة طوال السنة. برامج للأطفال والبالغين مع مدربين معتمدين من الاتحاد اللبناني للسباحة.',
                'address' => 'Rue de Jounieh, near Casino du Liban',
                'city' => 'Jounieh',
                'phone' => '+961 9 912 345',
                'lat' => 33.9806,
                'lng' => 35.6178,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 5. Cedar Sports Club — Dbayeh
            [
                'id' => 5,
                'user_id' => 6,
                'name' => 'Cedar Sports Club',
                'description' => 'نادي رياضي متكامل في ضبية يضم ملاعب كرة قدم وتنس وبادل. مرافق حديثة وأرضيات احترافية. يضم أكثر من 500 عضو نشط.',
                'address' => 'Dbayeh Highway, near ABC Mall',
                'city' => 'Dbayeh',
                'phone' => '+961 4 523 456',
                'lat' => 33.9200,
                'lng' => 35.5800,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 6. Creative Minds — Achrafieh, Beirut
            [
                'id' => 6,
                'user_id' => 7,
                'name' => 'Creative Minds Center',
                'description' => 'مركز الفنون والمهارات الإبداعية في الأشرفية. نقدم دروساً في الرسم والتصوير والمسرح واللغات والبرمجة للأطفال والكبار.',
                'address' => 'Sassine Square, Achrafieh',
                'city' => 'Beirut',
                'phone' => '+961 1 217 890',
                'lat' => 33.8869,
                'lng' => 35.5235,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
