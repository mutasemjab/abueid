<?php

namespace Database\Seeders;

use App\Models\DeliveryArea;
use Illuminate\Database\Seeder;

class DeliveryAreaSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            [['ar' => 'عمّان', 'en' => 'Amman'], 2],
            [['ar' => 'الزرقاء', 'en' => 'Zarqa'], 3],
            [['ar' => 'إربد', 'en' => 'Irbid'], 3],
            [['ar' => 'البلقاء / السلط', 'en' => 'Balqa / Salt'], 3],
            [['ar' => 'مادبا', 'en' => 'Madaba'], 3],
            [['ar' => 'محافظة أخرى', 'en' => 'Other governorate'], 4],
        ];

        foreach ($areas as $position => [$name, $fee]) {
            DeliveryArea::query()->create(['name' => $name, 'fee' => $fee, 'sort_order' => $position + 1]);
        }
    }
}
