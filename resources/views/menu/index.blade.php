@extends('layouts.app')

@section('title', $heading ?? 'Menu')
@section('description', ($section ?? 'food') === 'drink' ? 'Signature cocktails, wines, and beer at The District Tapas + Bar, Hamilton.' : 'Share plates and Spanish-leaning tapas at The District, downtown Hamilton.')

@section('content')
    @php $section = $section ?? 'food'; @endphp

    {{-- SUBHERO --}}
    <section class="subhero">
        <div class="container-ed">
            <p class="eyebrow">Menu · {{ $section === 'drink' ? 'Bar' : 'Kitchen' }}</p>
            <h1 class="font-display text-5xl md:text-7xl font-light leading-[1.05] mt-4">
                {{ $heading ?? 'Our menu' }}
            </h1>
            <p class="lede mt-6 max-w-2xl">
                @if($section === 'drink')
                    Signature cocktails, Spanish wines by the glass, and cold beer — made to pair with whatever's on your table.
                @else
                    Spanish-leaning share plates, seasonal specials, and a few quiet regulars. Designed to be passed.
                @endif
            </p>
            <div class="mt-8 inline-flex p-1 bg-paper-deep rounded-full">
                <a href="{{ route('menu.index') }}"
                   class="px-6 py-2 rounded-full text-sm font-semibold uppercase tracking-[0.14em] transition {{ $section === 'food' ? 'bg-ink text-paper' : 'text-ink hover:text-brand' }}">
                    Kitchen
                </a>
                <a href="{{ route('menu.drinks') }}"
                   class="px-6 py-2 rounded-full text-sm font-semibold uppercase tracking-[0.14em] transition {{ $section === 'drink' ? 'bg-ink text-paper' : 'text-ink hover:text-brand' }}">
                    Bar
                </a>
            </div>
        </div>
    </section>

    {{-- CATEGORY RAIL --}}
    <div class="sticky top-[72px] z-30 bg-paper/90 backdrop-blur border-y border-line" x-data="{ active: 'all' }">
        <div class="container-ed py-4 flex gap-2 overflow-x-auto no-scrollbar">
            <button type="button"
                    @click="active = 'all'; document.querySelectorAll('.menu-cat').forEach(el => el.style.display='block')"
                    :class="active === 'all' ? 'bg-ink text-paper' : 'bg-transparent text-ink-muted hover:text-ink'"
                    class="shrink-0 px-4 py-2 rounded-full text-xs uppercase tracking-[0.14em] font-semibold transition">
                All
            </button>
            @foreach($categories as $category)
                <button type="button"
                        @click="active = '{{ $category->slug }}'; document.querySelectorAll('.menu-cat').forEach(el => el.style.display = el.dataset.slug === '{{ $category->slug }}' ? 'block' : 'none'); document.getElementById('cat-{{ $category->slug }}')?.scrollIntoView({behavior:'smooth', block:'start'})"
                        :class="active === '{{ $category->slug }}' ? 'bg-ink text-paper' : 'bg-transparent text-ink-muted hover:text-ink'"
                        class="shrink-0 px-4 py-2 rounded-full text-xs uppercase tracking-[0.14em] font-semibold transition">
                    {{ $category->name }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- MENU BODY --}}
    <section class="section-pad">
        <div class="container-ed">
            @forelse($categories as $category)
                <div id="cat-{{ $category->slug }}" data-slug="{{ $category->slug }}" class="menu-cat mb-20 scroll-mt-36">
                    <div class="flex items-end justify-between gap-6 mb-10 border-b border-line pb-5">
                        <div>
                            <p class="eyebrow text-brand">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</p>
                            <h2 class="font-display text-4xl md:text-5xl font-light italic mt-2">{{ $category->name }}</h2>
                        </div>
                        @if($category->description)
                            <p class="hidden md:block text-ink-muted text-sm max-w-sm text-right">{{ $category->description }}</p>
                        @endif
                    </div>

                    @if($category->menuItems->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($category->menuItems as $item)
                                @include('partials.dish-card', ['item' => $item, 'showAddToCart' => $section !== 'drink'])
                            @endforeach
                        </div>
                    @else
                        <p class="text-ink-muted italic">Nothing on the board for this section right now — check back soon.</p>
                    @endif
                </div>
            @empty
                <div class="text-center py-20">
                    <p class="eyebrow">Menu</p>
                    <h2 class="font-display text-3xl mt-4">We're updating the menu.</h2>
                    <p class="text-ink-muted mt-3">Call us at <a href="tel:+19055222580" class="text-brand underline underline-offset-4">(905) 522-2580</a> for today's offerings.</p>
                </div>
            @endforelse
        </div>
    </section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.add-to-cart-btn').forEach(button => {
            button.addEventListener('click', function() {
                const itemId = this.dataset.id;
                const originalLabel = this.textContent;
                this.disabled = true;
                this.textContent = 'Adding…';

                fetch('{{ route('cart.add') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ item_id: itemId, quantity: 1, special_instructions: '' })
                })
                .then(r => r.json())
                .then(() => {
                    if (window.Alpine?.store('ui')) {
                        window.Alpine.store('ui').bumpCart(1);
                        window.Alpine.store('ui').flash('Added to your order');
                    }
                    this.textContent = 'Added ✓';
                    setTimeout(() => { this.textContent = originalLabel; this.disabled = false; }, 1400);
                })
                .catch(() => {
                    if (window.Alpine?.store('ui')) {
                        window.Alpine.store('ui').flash('Could not add — try again', 'error');
                    }
                    this.textContent = originalLabel;
                    this.disabled = false;
                });
            });
        });
    });
</script>
<style>.no-scrollbar::-webkit-scrollbar{display:none}.no-scrollbar{-ms-overflow-style:none;scrollbar-width:none}</style>
@endpush
