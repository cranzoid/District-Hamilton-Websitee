<?php

namespace App\Services;

use App\Models\DailyOffer;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OffersSyncService
{
    public const OWNED_FIELDS = [
        'day_of_week', 'section', 'title', 'description',
        'price_display', 'sort_order', 'is_active',
    ];

    public function load(string $path): array
    {
        if (! file_exists($path)) {
            throw new RuntimeException("Offers snapshot not found: {$path}");
        }

        $data = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);

        return array_values($data['offers'] ?? []);
    }

    public function save(string $path, array $offers): void
    {
        $dir = dirname($path);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $payload = ['offers' => array_values($offers)];

        file_put_contents(
            $path,
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)."\n"
        );
    }

    public function exportFromDb(): array
    {
        return DailyOffer::orderBy('day_of_week')->orderBy('section')->orderBy('sort_order')
            ->get()
            ->map(fn (DailyOffer $o) => [
                'day_of_week' => (int) $o->day_of_week,
                'section' => $o->section,
                'title' => $o->title,
                'description' => $o->description,
                'price_display' => $o->price_display,
                'sort_order' => (int) $o->sort_order,
                'is_active' => (bool) $o->is_active,
            ])
            ->all();
    }

    /**
     * Offers are small (~12 rows) and the committed JSON is authoritative.
     * Apply = full replace inside a transaction. Admin-only fields would need
     * a separate owned-field model if this ever needed to preserve DB state.
     */
    public function apply(array $offers): array
    {
        return DB::transaction(function () use ($offers) {
            $before = DailyOffer::count();
            DailyOffer::query()->delete();

            $inserted = [];
            foreach ($offers as $entry) {
                $payload = array_intersect_key($entry, array_flip(self::OWNED_FIELDS));
                $payload['is_active'] = $payload['is_active'] ?? true;
                DailyOffer::create($payload);
                $dayName = DailyOffer::DAY_NAMES[$entry['day_of_week'] ?? -1] ?? '?';
                $inserted[] = ($entry['title'] ?? '?')." [{$dayName}]";
            }

            return [
                'removed' => $before,
                'inserted' => $inserted,
            ];
        });
    }
}
