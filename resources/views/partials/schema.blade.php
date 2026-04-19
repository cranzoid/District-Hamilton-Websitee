@php
    $restaurantName = config('restaurant.name');
    $restaurantPhone = config('restaurant.phone');
    $restaurantEmail = config('restaurant.email');
    $restaurantSite = rtrim(config('restaurant.website') ?: config('app.url'), '/');
    $social = config('restaurant.social_media', []);
    $hours = config('restaurant.hours', []);

    $dayMap = [
        'monday' => 'Mo', 'tuesday' => 'Tu', 'wednesday' => 'We',
        'thursday' => 'Th', 'friday' => 'Fr', 'saturday' => 'Sa', 'sunday' => 'Su',
    ];

    $openingHoursSpec = [];
    foreach ($hours as $day => $spec) {
        if (!isset($dayMap[$day])) continue;
        if (($spec['closed'] ?? false) === true) continue;
        if (empty($spec['open']) || empty($spec['close'])) continue;
        $openingHoursSpec[] = [
            '@type' => 'OpeningHoursSpecification',
            'dayOfWeek' => 'https://schema.org/' . ucfirst($day),
            'opens' => $spec['open'],
            'closes' => $spec['close'],
        ];
    }

    $sameAs = array_values(array_filter([
        $social['facebook'] ?? null,
        $social['instagram'] ?? null,
        $social['twitter'] ?? null,
    ]));

    $restaurantSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Restaurant',
        '@id' => $restaurantSite . '/#restaurant',
        'name' => $restaurantName,
        'url' => $restaurantSite,
        'telephone' => $restaurantPhone,
        'email' => $restaurantEmail,
        'image' => asset('images/Restaurant_food/hero.jpg'),
        'logo' => asset('images/logo.png'),
        'priceRange' => '$$',
        'servesCuisine' => ['Tapas', 'Spanish', 'Mediterranean', 'Global'],
        'acceptsReservations' => 'True',
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => '61 Barton Street East',
            'addressLocality' => 'Hamilton',
            'addressRegion' => 'ON',
            'postalCode' => 'L8L 2V7',
            'addressCountry' => 'CA',
        ],
        'geo' => [
            '@type' => 'GeoCoordinates',
            'latitude' => 43.2611,
            'longitude' => -79.8614,
        ],
        'hasMap' => 'https://www.google.com/maps/search/?api=1&query=The+District+Tapas+Bar+Hamilton',
        'openingHoursSpecification' => $openingHoursSpec,
        'sameAs' => $sameAs,
    ];
@endphp
<script type="application/ld+json">
{!! json_encode($restaurantSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>

@isset($menuItemSchema)
<script type="application/ld+json">
{!! json_encode($menuItemSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endisset

@isset($faqSchema)
<script type="application/ld+json">
{!! json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endisset

@isset($breadcrumbSchema)
<script type="application/ld+json">
{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endisset
