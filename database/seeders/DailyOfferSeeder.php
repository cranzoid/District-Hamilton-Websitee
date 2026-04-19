<?php

namespace Database\Seeders;

use App\Models\DailyOffer;
use Illuminate\Database\Seeder;

class DailyOfferSeeder extends Seeder
{
    public function run(): void
    {
        DailyOffer::truncate();

        $data = json_decode(file_get_contents(database_path('data/daily-offers.json')), true);

        foreach ($data['offers'] as $offer) {
            DailyOffer::create([
                'day_of_week'  => $offer['day_of_week'],
                'section'      => $offer['section'],
                'title'        => $offer['title'],
                'description'  => $offer['description'] ?? null,
                'price_display'=> $offer['price_display'] ?? null,
                'sort_order'   => $offer['sort_order'],
                'is_active'    => $offer['is_active'] ?? true,
            ]);
        }

        $this->command->info('Daily offers seeded: ' . DailyOffer::count() . ' offers.');
    }
}
