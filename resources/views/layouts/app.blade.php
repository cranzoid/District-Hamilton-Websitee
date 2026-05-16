<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('partials.seo')
    @include('partials.schema')

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,300;1,9..144,400&family=Inter+Tight:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script async src="https://www.googletagmanager.com/gtag/js?id=G-1KJXQC79GH"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-1KJXQC79GH');
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="bg-paper text-ink antialiased overflow-x-hidden flex flex-col min-h-screen">
    @include('partials.nav')

    <main class="flex-1">
        <div class="container-ed pt-6">
            @include('partials.flash')
        </div>
        @yield('content')
    </main>

    @include('partials.footer')

    @include('partials.toast')

    @stack('scripts')

    {{-- Mobile panel lives here (outside nav) to avoid backdrop-filter containing-block trap --}}
    @stack('mobile-nav')
</body>
</html>
