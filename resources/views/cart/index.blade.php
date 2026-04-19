@extends('layouts.app')

@section('title', 'Your cart')

@section('content')
    <section class="subhero">
        <div class="container-ed">
            <p class="eyebrow">Order</p>
            <h1 class="font-display text-5xl md:text-6xl font-light mt-3">Your cart</h1>
            <p class="lede mt-4 max-w-xl">Review your selection before checkout. Prices exclude tax — we'll add that and any delivery fee on the next step.</p>
        </div>
    </section>

    <section class="section-pad">
        <div class="container-ed">
            @if(empty($items))
                <div class="text-center py-20 bg-paper-warm rounded-2xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-ink-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <h2 class="font-display text-3xl mt-6">Nothing here yet</h2>
                    <p class="text-ink-muted mt-2">Your cart is waiting. Browse the menu to get started.</p>
                    <a href="{{ route('menu.index') }}" class="btn btn-ember mt-8">Browse the menu</a>
                </div>
            @else
                <div class="grid grid-cols-1 lg:grid-cols-[1fr_380px] gap-10">
                    <div class="order-2 lg:order-1">
                        <div class="bg-paper-warm rounded-2xl divide-y divide-line-soft overflow-hidden" style="--tw-divide-opacity:1;">
                            @foreach($items as $id => $item)
                                <div class="p-6 flex gap-4 items-start" style="border-color: rgba(14,14,14,0.06);">
                                    @if(isset($item['image_path']))
                                        <img src="{{ asset($item['image_path']) }}" alt="" class="w-20 h-20 rounded-lg object-cover shrink-0">
                                    @else
                                        <div class="w-20 h-20 rounded-lg bg-paper-deep shrink-0"></div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <div class="flex justify-between gap-4 items-start">
                                            <h3 class="font-display text-xl leading-tight">{{ $item['name'] }}</h3>
                                            <span class="numeral text-brand-dark shrink-0">${{ number_format($item['item_total'], 2) }}</span>
                                        </div>
                                        @if($item['selected_add_ons']->count() > 0)
                                            <p class="text-sm text-ink-muted mt-1">+ {{ $item['selected_add_ons']->pluck('name')->implode(', ') }}</p>
                                        @endif
                                        @if(!empty($item['special_instructions']))
                                            <p class="text-sm text-ink-muted mt-1 italic">"{{ $item['special_instructions'] }}"</p>
                                        @endif
                                        <div class="mt-4 flex items-center justify-between gap-4 flex-wrap">
                                            <form class="quantity-form inline-flex items-center border border-line rounded-full" data-id="{{ $id }}">
                                                <button type="button" class="decrease-qty w-9 h-9 text-lg hover:text-ember" aria-label="Decrease">−</button>
                                                <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="w-10 text-center bg-transparent border-0 focus:ring-0 numeral text-sm" aria-label="Quantity">
                                                <button type="button" class="increase-qty w-9 h-9 text-lg hover:text-ember" aria-label="Increase">+</button>
                                            </form>
                                            <form action="{{ route('cart.remove') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="item_id" value="{{ $id }}">
                                                <button type="submit" class="text-xs uppercase tracking-[0.14em] text-ember hover:underline">Remove</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 flex flex-wrap gap-3 justify-between">
                            <a href="{{ route('menu.index') }}" class="btn btn-outline btn-sm">← Continue browsing</a>
                            <form action="{{ route('cart.clear') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-ghost btn-sm text-ember">Clear cart</button>
                            </form>
                        </div>
                    </div>

                    <aside class="order-1 lg:order-2">
                        <div class="bg-ink text-paper rounded-2xl p-8 lg:sticky lg:top-24">
                            <p class="eyebrow text-brand-light">Summary</p>
                            <h2 class="font-display text-2xl mt-2">Your order</h2>

                            <dl class="mt-6 space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <dt class="text-paper/70">Subtotal</dt>
                                    <dd class="numeral">${{ number_format($subtotal, 2) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-paper/70">Tax (13%)</dt>
                                    <dd class="numeral">${{ number_format($subtotal * 0.13, 2) }}</dd>
                                </div>
                            </dl>
                            <div class="border-t border-paper/15 mt-5 pt-5 flex justify-between items-end">
                                <span class="eyebrow text-paper/70">Estimated total</span>
                                <span class="numeral text-2xl">${{ number_format($subtotal + ($subtotal * 0.13), 2) }}</span>
                            </div>
                            <p class="text-xs text-paper/50 mt-2">Delivery fees and tip are calculated at checkout.</p>

                            <a href="{{ route('checkout.index') }}" class="btn btn-ember btn-block btn-lg mt-6">Proceed to checkout</a>

                            <p class="text-xs text-paper/50 text-center mt-6">
                                Need help? Call us at<br>
                                <a href="tel:+19055222580" class="text-brand-light">(905) 522-2580</a>
                            </p>
                        </div>
                    </aside>
                </div>
            @endif
        </div>
    </section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.quantity-form').forEach(form => {
            const itemId = form.dataset.id;
            const decreaseBtn = form.querySelector('.decrease-qty');
            const increaseBtn = form.querySelector('.increase-qty');
            const qtyInput = form.querySelector('input[name="quantity"]');

            decreaseBtn.addEventListener('click', () => {
                let v = parseInt(qtyInput.value);
                if (v > 1) { qtyInput.value = v - 1; updateCartItem(itemId, v - 1); }
            });
            increaseBtn.addEventListener('click', () => {
                let v = parseInt(qtyInput.value);
                qtyInput.value = v + 1;
                updateCartItem(itemId, v + 1);
            });
            qtyInput.addEventListener('change', () => {
                let v = parseInt(qtyInput.value);
                if (v < 1) { v = 1; qtyInput.value = 1; }
                updateCartItem(itemId, v);
            });
        });

        function updateCartItem(itemId, quantity) {
            fetch('{{ route('cart.update') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ item_id: itemId, quantity })
            })
            .then(r => r.json())
            .then(data => { if (data.success) window.location.reload(); })
            .catch(() => {
                if (window.Alpine?.store('ui')) window.Alpine.store('ui').flash('Could not update', 'error');
            });
        }
    });
</script>
@endpush
