@extends('layouts.app')

@section('title', $item->name)
@section('description', \Illuminate\Support\Str::limit(strip_tags($item->description ?? ''), 155))

@php
    $imgUrl = $item->image_path
        ? asset('storage/' . $item->image_path)
        : asset('images/placeholder-food.jpg');
    $seo = [
        'type' => 'product',
        'og_image' => $imgUrl,
    ];
    $menuItemSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'MenuItem',
        'name' => $item->name,
        'description' => strip_tags($item->description ?? ''),
        'image' => $imgUrl,
        'url' => url()->current(),
        'offers' => [
            '@type' => 'Offer',
            'price' => number_format($item->price, 2, '.', ''),
            'priceCurrency' => 'CAD',
            'availability' => $item->is_available
                ? 'https://schema.org/InStock'
                : 'https://schema.org/OutOfStock',
        ],
    ];
    if (!empty($item->category?->name)) {
        $menuItemSchema['menuAddOn'] = [];
        $breadcrumbSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Menu', 'item' => route('menu.index')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => $item->category->name, 'item' => url('/menu/category/' . $item->category->slug)],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $item->name, 'item' => url()->current()],
            ],
        ];
    }
@endphp

@section('content')
    <section class="section-pad pt-10">
        <div class="container-ed">
            <a href="{{ route('menu.index') }}" class="arrow-link mb-8">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to menu
            </a>

            <div class="grid lg:grid-cols-2 gap-10 lg:gap-16 mt-6">
                <div class="relative aspect-[4/5] overflow-hidden rounded-2xl">
                    <img src="{{ asset('storage/' . ($item->image_path ?: 'images/placeholder-food.jpg')) }}"
                         alt="{{ $item->name }}"
                         class="w-full h-full object-cover">
                    @if($item->is_featured)
                        <span class="absolute top-4 left-4 chip chip--dark">Chef's pick</span>
                    @endif
                    @if(!$item->is_available)
                        <div class="absolute inset-0 bg-ink/60 flex items-center justify-center">
                            <span class="chip chip--ember">Unavailable tonight</span>
                        </div>
                    @endif
                </div>

                <div class="flex flex-col">
                    <p class="eyebrow">{{ $item->category->name ?? 'Menu' }}</p>
                    <h1 class="font-display text-4xl md:text-6xl font-light mt-3 leading-tight">{{ $item->name }}</h1>
                    <p class="numeral text-3xl text-brand mt-4">${{ number_format($item->price, 2) }}</p>

                    @if($item->description)
                        <p class="mt-6 text-ink-muted leading-relaxed">{{ $item->description }}</p>
                    @endif

                    @if($item->tags->count() > 0)
                        <div class="mt-6">
                            <p class="eyebrow mb-2">Dietary</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($item->tags as $tag)
                                    <span class="chip">{{ $tag->tag_name }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($item->is_available)
                        <form id="add-to-cart-form" action="{{ route('cart.add') }}" method="POST" class="mt-10 border-t border-line pt-8">
                            @csrf
                            <input type="hidden" name="item_id" value="{{ $item->id }}">

                            @if($item->addOns->count() > 0)
                                <fieldset class="mb-8">
                                    <legend class="label mb-3">Add-ons</legend>
                                    <div class="space-y-2">
                                        @foreach($item->addOns as $addOn)
                                            <label for="add-on-{{ $addOn->id }}" class="flex items-center gap-3 p-3 border border-line rounded-lg cursor-pointer hover:border-brand transition">
                                                <input type="checkbox" name="add_ons[]" id="add-on-{{ $addOn->id }}" value="{{ $addOn->id }}" class="h-4 w-4 accent-brand">
                                                <span class="flex-1">{{ $addOn->name }}</span>
                                                <span class="numeral text-ink-muted">+${{ number_format($addOn->price, 2) }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </fieldset>
                            @endif

                            <div class="mb-6">
                                <label for="special_instructions" class="label">Special instructions</label>
                                <textarea name="special_instructions" id="special_instructions" rows="2" class="field" placeholder="Allergies, temperature, how it's plated…"></textarea>
                            </div>

                            <div class="flex items-center justify-between gap-4 mb-8">
                                <div>
                                    <p class="label mb-2">Quantity</p>
                                    <div class="inline-flex items-center border border-line rounded-full">
                                        <button type="button" id="decrease-quantity" class="w-10 h-10 text-lg hover:text-ember">−</button>
                                        <input type="number" name="quantity" id="quantity" value="1" min="1" max="99" class="w-12 text-center bg-transparent border-0 focus:ring-0 numeral">
                                        <button type="button" id="increase-quantity" class="w-10 h-10 text-lg hover:text-ember">+</button>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="eyebrow">Total</p>
                                    <p class="numeral text-3xl mt-1">$<span id="total-price">{{ number_format($item->price, 2) }}</span></p>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-ember btn-lg btn-block">Add to order</button>
                        </form>
                    @else
                        <div class="mt-10 alert alert-warn">
                            <span>This item is currently unavailable. Please check back later.</span>
                        </div>
                    @endif

                    <div class="mt-8 pt-6 border-t border-line flex flex-wrap gap-6 text-sm text-ink-muted">
                        <span class="inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Ready in ~{{ $item->preparation_time_minutes }} min
                        </span>
                        @if($item->is_pickup_only)
                            <span class="inline-flex items-center gap-2 text-ember">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14l-1 12H6L5 8zM9 8V6a3 3 0 116 0v2"/></svg>
                                Pickup only
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            @if($relatedItems->count() > 0)
                <div class="mt-24 pt-16 border-t border-line">
                    <p class="eyebrow">Pairs well with</p>
                    <h2 class="font-display text-3xl md:text-4xl font-light mt-3 mb-10">You might also like</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach($relatedItems as $relatedItem)
                            @include('partials.dish-card', ['item' => $relatedItem, 'showAddToCart' => false])
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const quantityInput = document.getElementById('quantity');
        const decreaseBtn = document.getElementById('decrease-quantity');
        const increaseBtn = document.getElementById('increase-quantity');
        const totalPrice = document.getElementById('total-price');
        const addOns = document.querySelectorAll('input[name="add_ons[]"]');
        const addToCartForm = document.getElementById('add-to-cart-form');

        if (!addToCartForm) return;

        const basePrice = {{ $item->price }};
        const addOnPrices = {
            @foreach($item->addOns as $addOn)
            {{ $addOn->id }}: {{ $addOn->price }},
            @endforeach
        };

        function updateTotalPrice() {
            let quantity = parseInt(quantityInput.value) || 1;
            let price = basePrice;
            addOns.forEach(a => { if (a.checked) price += addOnPrices[a.value]; });
            totalPrice.textContent = (price * quantity).toFixed(2);
        }

        decreaseBtn?.addEventListener('click', () => {
            let v = parseInt(quantityInput.value);
            if (v > 1) { quantityInput.value = v - 1; updateTotalPrice(); }
        });
        increaseBtn?.addEventListener('click', () => {
            let v = parseInt(quantityInput.value);
            if (v < 99) { quantityInput.value = v + 1; updateTotalPrice(); }
        });
        quantityInput?.addEventListener('change', () => {
            let v = parseInt(quantityInput.value);
            if (v < 1) quantityInput.value = 1;
            if (v > 99) quantityInput.value = 99;
            updateTotalPrice();
        });
        addOns.forEach(a => a.addEventListener('change', updateTotalPrice));

        addToCartForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const payload = {};
            formData.forEach((v, k) => {
                if (k === 'add_ons[]') { payload.add_ons = payload.add_ons || []; payload.add_ons.push(v); }
                else payload[k] = v;
            });

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalLabel = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Adding…';

            fetch('{{ route('cart.add') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(r => r.json())
            .then(() => {
                if (window.Alpine?.store('ui')) {
                    window.Alpine.store('ui').bumpCart(parseInt(quantityInput.value) || 1);
                    window.Alpine.store('ui').flash('Added to your order');
                }
                submitBtn.textContent = 'Added ✓';
                setTimeout(() => { submitBtn.textContent = originalLabel; submitBtn.disabled = false; }, 1400);
            })
            .catch(() => {
                if (window.Alpine?.store('ui')) window.Alpine.store('ui').flash('Could not add — try again', 'error');
                submitBtn.textContent = originalLabel;
                submitBtn.disabled = false;
            });
        });
    });
</script>
@endpush
