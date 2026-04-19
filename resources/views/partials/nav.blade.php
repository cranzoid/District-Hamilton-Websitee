@php
    $current = request()->route()?->getName();
    $cartCount = session()->has('cart') ? count(session('cart')) : 0;
    $enableFrench = \App\Models\SiteSetting::getSettings()->enable_french ?? false;
    $alert = \App\Models\SiteSetting::getSettings()->alert_message ?? null;

    $navLinks = [
        ['name' => 'menu.index',   'label' => 'Menu',          'match' => ['menu.index', 'menu.category', 'menu.item']],
        ['name' => 'menu.drinks',  'label' => 'Drinks',        'match' => ['menu.drinks']],
        ['name' => 'daily-offers', 'label' => 'Weekly Specials','match' => ['daily-offers']],
        ['name' => 'events',       'label' => 'Events',        'match' => ['events', 'events.inquiry']],
        ['name' => 'about',        'label' => 'About',         'match' => ['about']],
        ['name' => 'contact',      'label' => 'Contact',       'match' => ['contact', 'contact.submit']],
    ];
@endphp

@if ($alert)
    <div class="w-full bg-ink text-paper text-center text-xs uppercase tracking-[0.22em] py-2.5 px-4">
        {{ $alert }}
    </div>
@endif

<nav class="nav-ed" x-data="{ open: false }" :data-scrolled="$store.ui.scrolled">
    <div class="container-ed flex items-center justify-between py-4 md:py-5">
        <a href="{{ route('home') }}" class="flex items-center gap-2 group" aria-label="{{ config('app.name') }} — Home">
            <span class="font-display text-xl md:text-2xl leading-none tracking-tight">
                The District
                <span class="italic text-brand"> Tapas + Bar</span>
            </span>
        </a>

        <div class="hidden lg:flex items-center gap-7">
            @foreach ($navLinks as $link)
                <a href="{{ Route::has($link['name']) ? route($link['name']) : '#' }}"
                   class="nav-link"
                   @if (in_array($current, $link['match'])) aria-current="page" @endif>
                    {{ $link['label'] }}
                </a>
            @endforeach
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('cart.index') }}"
               class="relative flex items-center gap-2 text-ink hover:text-ember transition"
               aria-label="View cart">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span class="hidden sm:inline text-xs uppercase tracking-[0.14em] font-semibold">Cart</span>
                <span x-show="$store.ui.cartCount > 0"
                      data-cart-count="{{ $cartCount }}"
                      class="absolute -top-2 -right-2 min-w-[18px] h-[18px] rounded-full bg-ember text-paper text-[10px] font-bold flex items-center justify-center px-1"
                      x-text="$store.ui.cartCount"
                      x-cloak>
                    {{ $cartCount }}
                </span>
            </a>

            <a href="#reservation-widget" class="hidden md:inline-flex btn btn-ember btn-sm">Reserve</a>

            @if ($enableFrench)
                <a href="{{ route('language.switch', app()->getLocale() == 'en' ? 'fr' : 'en') }}"
                   class="hidden md:inline-flex text-xs uppercase tracking-[0.2em] font-semibold text-ink-muted hover:text-ink transition">
                    {{ app()->getLocale() == 'en' ? 'FR' : 'EN' }}
                </a>
            @endif

            <button @click="open = true" class="lg:hidden p-2 -mr-2" aria-label="Open menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 8h16M4 16h16"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile panel --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-x-4"
         x-transition:enter-end="opacity-100 translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-x-0"
         x-transition:leave-end="opacity-0 translate-x-4"
         @keydown.escape.window="open = false"
         class="mobile-panel"
         x-cloak>
        <button @click="open = false" class="absolute top-5 right-5 p-2" aria-label="Close menu">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        <div class="flex flex-col gap-2 mt-8">
            @foreach ($navLinks as $link)
                <a href="{{ Route::has($link['name']) ? route($link['name']) : '#' }}"
                   @click="open = false"
                   class="block py-2 {{ in_array($current, $link['match']) ? 'text-brand' : '' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach
            <a href="{{ route('cart.index') }}" @click="open = false" class="block py-2">Cart</a>
        </div>
        <div class="mt-auto pt-8 border-t border-white/10">
            <a href="#reservation-widget" @click="open = false" class="btn btn-ember btn-block">Reserve a table</a>
            <p class="text-center text-xs text-paper/60 tracking-[0.2em] uppercase mt-6">
                61 Barton St E · Hamilton
            </p>
            <p class="text-center text-sm text-paper/80 mt-1">
                <a href="tel:+19055222580">(905) 522-2580</a>
            </p>
        </div>
    </div>
</nav>
