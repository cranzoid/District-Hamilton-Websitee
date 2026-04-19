@extends('layouts.app')

@section('title', 'Order confirmed')

@section('content')
    <section class="section-pad">
        <div class="container-ed--narrow">
            <div class="text-center">
                <div class="w-16 h-16 mx-auto rounded-full bg-brand/10 flex items-center justify-center">
                    <svg class="w-8 h-8 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <p class="eyebrow mt-6 text-brand">Confirmed</p>
                <h1 class="font-display text-5xl md:text-6xl font-light mt-3 leading-tight">
                    Thank you — <em class="italic">your order is in</em>.
                </h1>
                <p class="lede mt-5">
                    Order <span class="numeral text-ink">#{{ $order->order_number }}</span>. We've emailed a copy of your receipt.
                </p>
            </div>

            <div class="mt-12 bg-paper-warm rounded-2xl p-8">
                <div class="flex items-start justify-between gap-4 flex-wrap">
                    <div>
                        <p class="eyebrow">Details</p>
                        <dl class="mt-3 space-y-1.5 text-sm">
                            <div class="flex gap-3"><dt class="text-ink-muted w-32">Order</dt><dd class="numeral">#{{ $order->order_number }}</dd></div>
                            <div class="flex gap-3"><dt class="text-ink-muted w-32">Placed</dt><dd>{{ $order->created_at->format('F j, Y · g:i a') }}</dd></div>
                            <div class="flex gap-3"><dt class="text-ink-muted w-32">Status</dt><dd class="chip chip--dark">{{ ucfirst($order->status) }}</dd></div>
                            <div class="flex gap-3"><dt class="text-ink-muted w-32">Payment</dt><dd>{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }} · <span class="{{ $order->payment_status == 'paid' ? 'text-brand' : 'text-ember' }}">{{ ucfirst($order->payment_status) }}</span></dd></div>
                            @if($order->pickup_time)
                                <div class="flex gap-3"><dt class="text-ink-muted w-32">Pickup</dt><dd>{{ \Carbon\Carbon::parse($order->pickup_time)->format('F j, g:i a') }}</dd></div>
                            @endif
                        </dl>
                    </div>
                    <a href="{{ route('orders.print', $order) }}" target="_blank" class="btn btn-outline btn-sm">Print receipt</a>
                </div>
            </div>

            <div class="mt-10">
                <p class="eyebrow mb-4">Summary</p>
                <div class="divide-y divide-line-soft" style="--tw-divide-opacity:1;">
                    @foreach($order->items as $item)
                        <div class="py-4 flex justify-between gap-4">
                            <div class="flex-1">
                                <p class="font-medium">{{ $item->quantity }} × {{ $item->menuItem->name ?? 'Item' }}</p>
                                @if($item->addOns->count() > 0)
                                    <p class="text-xs text-ink-muted mt-1">+ {{ $item->addOns->pluck('name')->join(', ') }}</p>
                                @endif
                                @if($item->special_instructions)
                                    <p class="text-xs text-ink-muted mt-1 italic">"{{ $item->special_instructions }}"</p>
                                @endif
                            </div>
                            <span class="numeral shrink-0">${{ number_format($item->subtotal, 2) }}</span>
                        </div>
                    @endforeach
                </div>
                <dl class="mt-4 pt-4 border-t border-line space-y-1.5 text-sm">
                    <div class="flex justify-between"><dt class="text-ink-muted">Subtotal</dt><dd class="numeral">${{ number_format($order->subtotal, 2) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-ink-muted">Tax</dt><dd class="numeral">${{ number_format($order->tax, 2) }}</dd></div>
                    @if($order->delivery_fee > 0)
                        <div class="flex justify-between"><dt class="text-ink-muted">Delivery</dt><dd class="numeral">${{ number_format($order->delivery_fee, 2) }}</dd></div>
                    @endif
                    <div class="flex justify-between pt-3 border-t border-line mt-3"><dt class="font-semibold">Total</dt><dd class="numeral text-xl">${{ number_format($order->total, 2) }}</dd></div>
                </dl>
            </div>

            <div class="mt-12 flex flex-wrap justify-center gap-3">
                <a href="{{ route('home') }}" class="btn btn-ink">Back home</a>
                <a href="{{ route('menu.index') }}" class="btn btn-outline">Order again</a>
            </div>
        </div>
    </section>
@endsection
