<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            [
                'is_main' => true,
                'city' => ['ar' => 'عمّان', 'en' => 'Amman'],
                'name' => ['ar' => 'الفرع الرئيسي', 'en' => 'Main branch'],
                'address' => ['ar' => 'عمّان — اكتب العنوان التفصيلي هنا', 'en' => 'Amman — detailed address here'],
                'hours' => ['ar' => 'السبت – الخميس · 8:30 ص – 6:30 م', 'en' => 'Sat – Thu · 8:30 am – 6:30 pm'],
                'phone' => '06 000 0000',
                'map_url' => 'https://maps.google.com/?q=Amman',
            ],
            [
                'city' => ['ar' => 'الزرقاء', 'en' => 'Zarqa'],
                'name' => ['ar' => 'فرع الزرقاء', 'en' => 'Zarqa branch'],
                'address' => ['ar' => 'الزرقاء — اكتب العنوان التفصيلي هنا', 'en' => 'Zarqa — detailed address here'],
                'hours' => ['ar' => 'السبت – الخميس · 9:00 ص – 6:00 م', 'en' => 'Sat – Thu · 9:00 am – 6:00 pm'],
                'phone' => '05 000 0000',
                'map_url' => 'https://maps.google.com/?q=Zarqa',
            ],
            [
                'city' => ['ar' => 'إربد', 'en' => 'Irbid'],
                'name' => ['ar' => 'فرع إربد', 'en' => 'Irbid branch'],
                'address' => ['ar' => 'إربد — اكتب العنوان التفصيلي هنا', 'en' => 'Irbid — detailed address here'],
                'hours' => ['ar' => 'السبت – الخميس · 9:00 ص – 6:00 م', 'en' => 'Sat – Thu · 9:00 am – 6:00 pm'],
                'phone' => '02 000 0000',
                'map_url' => 'https://maps.google.com/?q=Irbid',
            ],
        ];

        foreach ($branches as $position => $branch) {
            Branch::query()->create($branch + ['sort_order' => $position + 1]);
        }
    }
}
