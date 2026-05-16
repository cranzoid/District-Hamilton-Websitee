@extends('layouts.app')

@section('title', 'Spanish Tapas + Cocktails in Downtown Hamilton')
@section('description', 'The District Tapas + Bar: share plates, signature cocktails, and weekly specials in downtown Hamilton. Reserve a table or order online.')

@section('content')
    {{-- HERO --}}
    <section class="hero-ed">
        <div class="hero-ed__bg" style="background-image: url('/images/Restaurant_food/hero.jpg'); background-size: cover; background-position: center; opacity: 0.50;"></div>
        <div class="hero-ed__scrim"></div>
        <div class="container-ed relative z-10">
            <p class="eyebrow text-paper/70 mb-6">Barton East · Hamilton</p>
            <h1 class="display-hero text-paper">
                Bold <em class="italic text-brand-light">tapas</em>,
                <br> late nights,
                <br> shared tables.
            </h1>
            <p class="lede text-paper/80 mt-8 max-w-xl">
                A Spanish-leaning tapas + bar in downtown Hamilton. Share plates, signature cocktails, and weekly specials that change every night.
            </p>
            <div class="mt-10 flex flex-wrap gap-4">
                <a href="#reservation-widget" class="btn btn-ember btn-lg">Reserve a table</a>
                <a href="{{ route('menu.index') }}" class="btn btn-outline-paper btn-lg">View the menu</a>
            </div>
            <div class="mt-16 grid grid-cols-3 gap-6 max-w-md text-paper/70 border-t border-paper/15 pt-6">
                <div>
                    <p class="eyebrow text-paper/50">Open</p>
                    <p class="mt-1 text-sm">Tue – Sun</p>
                </div>
                <div>
                    <p class="eyebrow text-paper/50">Call</p>
                    <p class="mt-1 numeral text-sm">905 · 522 · 2580</p>
                </div>
                <div>
                    <p class="eyebrow text-paper/50">Find</p>
                    <p class="mt-1 text-sm">61 Barton St E</p>
                </div>
            </div>
        </div>
    </section>

    {{-- TODAY'S SPECIALS --}}
    @if($todayOffers->count() > 0)
    <section class="section-pad bg-ink text-paper">
        <div class="container-ed">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6 mb-12">
                <div class="text-center md:text-left">
                    <p class="eyebrow text-brand-light mx-auto md:mx-0">Tonight · {{ $todayDayName }}</p>
                    <h2 class="font-display text-4xl md:text-5xl font-light italic mt-3">On the board today</h2>
                </div>
                <a href="{{ route('daily-offers') }}" class="arrow-link text-brand-light">
                    See the full week
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @foreach($todayOffers as $offer)
                    <article class="group border border-paper/10 rounded-xl p-7 hover:border-brand-light/50 hover:bg-paper/5 transition">
                        <p class="eyebrow text-paper/50">{{ ucfirst($offer->section) }}</p>
                        <h3 class="font-display text-2xl mt-2">{{ $offer->title }}</h3>
                        @if($offer->description)
                            <p class="text-paper/70 mt-2 leading-relaxed text-sm">{{ $offer->description }}</p>
                        @endif
                        @if($offer->price_display)
                            <p class="mt-4 numeral text-brand-light text-lg">{{ $offer->price_display }}</p>
                        @endif
                    </article>
                @endforeach
            </div>
        </div>
        @includeIf('partials.schema-offer', ['offers' => $todayOffers])
    </section>
    @endif

    {{-- WELCOME / INTRO --}}
    <section class="section-pad">
        <div class="container-ed--narrow text-center">
            <p class="eyebrow">Est. Hamilton</p>
            <h2 class="font-display text-4xl md:text-6xl font-light leading-[1.05] mt-5">
                A room built for <em class="italic text-brand">lingering</em> — where every plate is meant to be passed.
            </h2>
            <p class="lede mt-8">
                Our menu leans Spanish, our bar leans inventive, and our pace leans slow. Come for one plate, stay for five.
            </p>
            <a href="{{ route('about') }}" class="arrow-link mt-8">
                The story behind the room
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>
    </section>

    {{-- FEATURED DISHES --}}
    @if($featuredItems->count() > 0)
    <section class="section-pad bg-paper-warm">
        <div class="container-ed">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6 mb-12">
                <div>
                    <p class="eyebrow">From the kitchen</p>
                    <h2 class="font-display text-4xl md:text-5xl font-light mt-3">Chef's picks this week</h2>
                </div>
                <a href="{{ route('menu.index') }}" class="arrow-link">
                    Full menu
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($featuredItems as $item)
                    @include('partials.dish-card', ['item' => $item])
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- RESERVATION --}}
    <section id="reservation-widget" class="section-pad bg-ink text-paper" style="overflow: visible;">
        <div class="container-ed--narrow text-center" style="overflow: visible;">
            <p class="eyebrow text-brand-light">Reserve</p>
            <h2 class="font-display text-4xl md:text-5xl font-light mt-3">Hold your table</h2>
            <p class="lede text-paper/70 mt-5">Powered by OpenTable. Parties up to eight book directly — for larger groups,
                <a href="{{ route('events') }}" class="text-brand-light underline underline-offset-4">enquire about events</a>.
            </p>
            <div class="mt-10 bg-paper/5 border border-paper/10 rounded-xl p-6 md:p-8" style="overflow: visible;">
                <script type='text/javascript' src='https://www.opentable.ca/widget/reservation/loader?rid=1431862&type=standard&theme=standard&color=1&dark=true&iframe=true&domain=ca&lang=en-CA&newtab=true&ot_source=Restaurant%20website&cfe=true'></script>
            </div>
        </div>
    </section>

    {{-- ABOUT SPLIT --}}
    <section class="section-pad">
        <div class="container-ed grid md:grid-cols-2 gap-12 md:gap-20 items-center">
            <div class="relative">
                <img src="/images/Restaurant_food/int-6.jpg" alt="The District dining room" class="w-full h-[540px] object-cover rounded-xl shadow-lg">
                <div class="absolute -bottom-6 -right-6 hidden md:block bg-brand text-paper px-6 py-4 rounded-xl">
                    <p class="eyebrow">Est. 2024</p>
                    <p class="font-display italic text-2xl mt-1">Hamilton</p>
                </div>
            </div>
            <div>
                <p class="eyebrow">Our kitchen</p>
                <h2 class="font-display text-4xl md:text-5xl font-light mt-3 leading-tight">
                    Traditional technique, <em class="italic text-brand">local</em> ingredients.
                </h2>
                <p class="mt-6 text-ink-muted leading-relaxed">
                    Our chefs trained in Spain and bring that rigor to every plate — jamón sliced fine, patatas bravas crisp on the outside, paella on a proper flat pan. We buy as local as the season allows.
                </p>
                <p class="mt-4 text-ink-muted leading-relaxed">
                    Everything is built to share. Order a few plates, order too many, send more when you want.
                </p>
                <div class="mt-8 grid grid-cols-2 gap-6">
                    <div>
                        <p class="numeral text-3xl text-brand">14</p>
                        <p class="eyebrow mt-1">Share plates</p>
                    </div>
                    <div>
                        <p class="numeral text-3xl text-brand">7</p>
                        <p class="eyebrow mt-1">Nights / week specials</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- TESTIMONIALS --}}
    <section class="section-pad bg-paper-deep">
        <div class="container-ed">
            <div class="text-center mb-14">
                <p class="eyebrow">Guests</p>
                <h2 class="font-display text-4xl md:text-5xl font-light mt-3">What the room says</h2>
            </div>
            <div class="grid md:grid-cols-3 gap-6">
                @foreach([
                    ['quote' => 'The food was amazing and the atmosphere even better. I highly recommend this place and will be going back regularly.', 'name' => 'Jennifer S.', 'loc' => 'Hamilton, ON'],
                    ['quote' => 'First off, the food was amazing. Service, amazing. Atmosphere very relaxing. Cannot wait to have an excuse to go back to Hamilton just to eat here again.', 'name' => 'Crystal H.', 'loc' => 'London, ON'],
                    ['quote' => 'We got an assortment of tapas to share and they were all delicious. The drinks were tasty. Would recommend trying this spot out.', 'name' => 'Evan P.', 'loc' => 'Hamilton, ON'],
                ] as $t)
                    <figure class="bg-paper border border-line rounded-xl p-8 flex flex-col">
                        <svg class="w-8 h-8 text-brand mb-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6 17h3l2-4V7H5v6h3zm8 0h3l2-4V7h-6v6h3z"/></svg>
                        <blockquote class="font-display italic text-xl leading-snug flex-grow">"{{ $t['quote'] }}"</blockquote>
                        <figcaption class="mt-6 pt-6 border-t border-line-soft" style="border-color: rgba(14,14,14,0.06);">
                            <p class="font-semibold">{{ $t['name'] }}</p>
                            <p class="eyebrow mt-1">{{ $t['loc'] }}</p>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        </div>
    </section>

    {{-- GALLERY --}}
    <section class="section-pad">
        <div class="container-ed">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6 mb-10">
                <div>
                    <p class="eyebrow">The room</p>
                    <h2 class="font-display text-4xl md:text-5xl font-light mt-3">A taste of the atmosphere</h2>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach(['int-1.jpg', 'food-1.jpg', 'int-2.jpg', 'int-7.jpg'] as $img)
                    <div class="aspect-square overflow-hidden rounded-xl">
                        <img src="/images/Restaurant_food/{{ $img }}" alt="" loading="lazy" class="w-full h-full object-cover hover:scale-105 transition duration-700">
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- FINAL CTA --}}
    <section class="section-pad bg-ink text-paper">
        <div class="container-ed--narrow text-center">
            <p class="eyebrow text-brand-light">Come by</p>
            <h2 class="font-display text-5xl md:text-7xl font-light leading-[1.05] mt-4">
                See you at the <em class="italic text-brand-light">bar</em>.
            </h2>
            <div class="mt-10 flex flex-wrap gap-4 justify-center">
                <a href="#reservation-widget" class="btn btn-ember btn-lg">Reserve</a>
                <a href="tel:+19055222580" class="btn btn-outline-paper btn-lg">Call (905) 522-2580</a>
            </div>
        </div>
    </section>
@endsection
