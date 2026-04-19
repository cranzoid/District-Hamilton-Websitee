@extends('layouts.app')

@section('title', 'Weekly Specials')
@section('description', 'Seven days, seven reasons to come in. Wine nights, half-price share plates, and rotating tapas specials at The District.')

@section('content')
@php
    use Illuminate\Support\Carbon;
    $tz = config('restaurant.timezone', 'America/Toronto');
    $todayDow = (int) Carbon::now($tz)->dayOfWeek;
    $grid = \App\Models\DailyOffer::weekGrid();
    $todayOffers = collect($grid[$todayDow]['offers'] ?? []);
@endphp

    <section class="subhero">
        <div class="container-ed">
            <p class="eyebrow">Weekly specials</p>
            <h1 class="font-display text-6xl md:text-8xl font-light leading-[1.02] mt-3">
                Seven nights, <br>seven <em class="italic text-brand">reasons</em>.
            </h1>
            <p class="lede mt-8 max-w-2xl">
                The board changes every night. Whether you come for half-price bottles or five tapas for fifty, here's what's on tonight and the rest of the week — live from the kitchen, updated as we go.
            </p>
            <p class="eyebrow mt-6 text-brand">Today · {{ Carbon::now($tz)->format('l, F j') }}</p>
        </div>
    </section>

    <section class="section-pad bg-ink text-paper">
        <div class="container-ed">
            <div class="space-y-4 max-w-5xl mx-auto">
                @foreach($grid as $day => $row)
                    @php
                        $isToday = $day === $todayDow;
                        $isClosed = $row['offers']->isEmpty();
                    @endphp
                    <article class="rounded-2xl p-7 md:p-8 transition {{ $isToday ? 'bg-brand/10 border-2 border-brand shadow-[0_10px_40px_rgba(184,134,11,0.2)]' : 'bg-paper/5 border border-paper/10' }} {{ $isClosed ? 'opacity-60' : '' }}">
                        <div class="flex items-center justify-between gap-4 flex-wrap mb-5">
                            <h2 class="font-display text-3xl md:text-4xl font-light italic {{ $isToday ? 'text-brand-light' : '' }}">{{ $row['name'] }}</h2>
                            @if($isToday)
                                <span class="chip" style="background: var(--color-brand); color: var(--color-paper);">Tonight</span>
                            @endif
                        </div>

                        @if($isClosed)
                            <p class="italic text-paper/60">
                                @if($isToday)
                                    We're closed today — see you tomorrow.
                                @else
                                    Closed.
                                @endif
                            </p>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($row['offers'] as $offer)
                                    <div class="p-5 rounded-xl bg-ink-soft/60 border-l-4 border-brand">
                                        <p class="eyebrow text-paper/50 mb-2">{{ $offer->section === 'food' ? 'Food' : 'Drink' }}</p>
                                        <h3 class="font-display text-xl leading-tight">{{ $offer->title }}</h3>
                                        @if($offer->description)
                                            <p class="mt-2 text-sm text-paper/70 leading-relaxed">{{ $offer->description }}</p>
                                        @endif
                                        @if($offer->price_display)
                                            <p class="mt-3 numeral text-brand-light text-sm">{{ $offer->price_display }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>

            <div class="text-center mt-14">
                <a href="#reservation-widget" class="btn btn-ember btn-lg">Reserve for tonight</a>
            </div>
        </div>
    </section>

    @include('partials.schema-offer', ['offers' => $todayOffers])
@endsection
