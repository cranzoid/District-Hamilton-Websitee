<?php

namespace App\Http\Controllers;

use App\Models\DailyOffer;
use App\Models\MenuItem;
use Illuminate\Support\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        $featuredItems = MenuItem::where('is_featured', true)
            ->where('is_visible', true)
            ->where('is_available', true)
            ->orderBy('sort_order')
            ->take(3)
            ->get();

        if ($featuredItems->count() < 3) {
            $additionalItems = MenuItem::where('is_visible', true)
                ->where('is_available', true)
                ->whereNotIn('id', $featuredItems->pluck('id')->toArray())
                ->orderBy('created_at', 'desc')
                ->take(3 - $featuredItems->count())
                ->get();

            $featuredItems = $featuredItems->merge($additionalItems);
        }

        $tz = config('restaurant.timezone', 'America/Toronto');
        $now = Carbon::now($tz);
        $todayOffers = DailyOffer::forToday();
        $todayDayName = $now->format('l');

        return view('home.index', compact('featuredItems', 'todayOffers', 'todayDayName'));
    }
}
