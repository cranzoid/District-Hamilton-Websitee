@php
    $seo = $seo ?? [];
    $siteName = config('app.name', 'The District Tapas + Bar');
    $titleBase = $seo['title'] ?? trim(\Illuminate\Support\Facades\View::yieldContent('title'));
    $title = $titleBase ? $titleBase . ' · ' . $siteName : $siteName;
    $descYield = trim(\Illuminate\Support\Facades\View::yieldContent('description'));
    $description = $seo['description'] ?? ($descYield !== '' ? $descYield : 'Bold global tapas & a lively bar in the heart of Hamilton. Share plates, signature cocktails, weekly specials & private events at 61 Barton St East.');
    $canonical = $seo['canonical'] ?? url()->current();
    $ogImage = $seo['og_image'] ?? asset('images/Restaurant_food/hero.jpg');
    $type = $seo['type'] ?? 'website';
    $keywords = $seo['keywords'] ?? 'tapas Hamilton, tapas bar Hamilton, District Hamilton, Barton Street restaurant, Spanish tapas Hamilton, cocktails Hamilton';
    $robots = $seo['robots'] ?? 'index, follow, max-image-preview:large';
    $locale = app()->getLocale() === 'fr' ? 'fr_CA' : 'en_CA';
    $altLocale = $locale === 'fr_CA' ? 'en_CA' : 'fr_CA';
@endphp
<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">
<meta name="keywords" content="{{ $keywords }}">
<meta name="robots" content="{{ $robots }}">
<meta name="author" content="{{ $siteName }}">
<link rel="canonical" href="{{ $canonical }}">

{{-- hreflang: session-based locale switcher means both locales share the URL; emit x-default + language alternates --}}
<link rel="alternate" hreflang="en-CA" href="{{ $canonical }}">
<link rel="alternate" hreflang="fr-CA" href="{{ $canonical }}">
<link rel="alternate" hreflang="x-default" href="{{ $canonical }}">

{{-- Open Graph --}}
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:type" content="{{ $type }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:image" content="{{ $ogImage }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:locale" content="{{ $locale }}">
<meta property="og:locale:alternate" content="{{ $altLocale }}">

{{-- Twitter --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $ogImage }}">

<meta name="theme-color" content="#0E0E0E">
