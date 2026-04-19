<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class DailyOffer extends Model
{
    use HasFactory;

    public const SECTION_FOOD = 'food';
    public const SECTION_DRINK = 'drink';

    public const DAY_SUNDAY = 0;
    public const DAY_MONDAY = 1;
    public const DAY_TUESDAY = 2;
    public const DAY_WEDNESDAY = 3;
    public const DAY_THURSDAY = 4;
    public const DAY_FRIDAY = 5;
    public const DAY_SATURDAY = 6;

    public const DAY_NAMES = [
        self::DAY_SUNDAY => 'Sunday',
        self::DAY_MONDAY => 'Monday',
        self::DAY_TUESDAY => 'Tuesday',
        self::DAY_WEDNESDAY => 'Wednesday',
        self::DAY_THURSDAY => 'Thursday',
        self::DAY_FRIDAY => 'Friday',
        self::DAY_SATURDAY => 'Saturday',
    ];

    protected $fillable = [
        'day_of_week', 'section', 'title', 'description',
        'price_display', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function getDayNameAttribute(): string
    {
        return self::DAY_NAMES[$this->day_of_week] ?? 'Unknown';
    }

    public static function forToday(): Collection
    {
        return self::where('day_of_week', Carbon::now()->dayOfWeek)
            ->where('is_active', true)
            ->orderBy('section')
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Return every day of the week with its (possibly empty) offer set.
     * Monday is intentionally supported as "closed" via an absence of offers.
     */
    public static function weekGrid(): array
    {
        $grouped = self::where('is_active', true)
            ->orderBy('day_of_week')
            ->orderBy('section')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('day_of_week');

        $grid = [];
        foreach (self::DAY_NAMES as $day => $name) {
            $grid[$day] = [
                'day' => $day,
                'name' => $name,
                'offers' => $grouped->get($day, collect()),
            ];
        }

        return $grid;
    }
}
