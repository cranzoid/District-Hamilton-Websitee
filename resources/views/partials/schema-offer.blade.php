@php
    use Illuminate\Support\Carbon;
    $tz = config('restaurant.timezone', 'America/Toronto');
    $todayStart = Carbon::now($tz)->startOfDay();
    $todayEnd = Carbon::now($tz)->endOfDay();
    $offers = $offers ?? collect();
@endphp
@if($offers->count() > 0)
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Restaurant',
    'name' => config('restaurant.name'),
    'address' => [
        '@type' => 'PostalAddress',
        'streetAddress' => '61 Barton St E',
        'addressLocality' => 'Hamilton',
        'addressRegion' => 'ON',
        'postalCode' => 'L8L 2V7',
        'addressCountry' => 'CA',
    ],
    'telephone' => config('restaurant.phone'),
    'makesOffer' => $offers->map(function ($offer) use ($todayStart, $todayEnd) {
        return [
            '@type' => 'Offer',
            'name' => $offer->title,
            'description' => $offer->description,
            'priceSpecification' => $offer->price_display ? [
                '@type' => 'PriceSpecification',
                'price' => $offer->price_display,
                'priceCurrency' => 'CAD',
            ] : null,
            'validFrom' => $todayStart->toIso8601String(),
            'validThrough' => $todayEnd->toIso8601String(),
            'category' => ucfirst($offer->section),
            'availability' => 'https://schema.org/InStock',
        ];
    })->values()->all(),
], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endif
