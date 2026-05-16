@extends('layouts.app')

@php
use App\Models\DeliverySetting;
use App\Models\TippingSetting;
@endphp

@section('title', 'Checkout')

@push('styles')
    @if(isset($stripeKey))
    <style>
        .StripeElement {
            background-color: #F7F3EC;
            padding: 14px 16px;
            border: 1px solid rgba(14,14,14,0.12);
            border-radius: 8px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .StripeElement--focus { border-color: #B8860B; box-shadow: 0 0 0 3px rgba(184,134,11,0.12); }
        .StripeElement--invalid { border-color: #B8381F; }
        .StripeElement--complete { border-color: #10B981; }

        .tip-option {
            padding: 0.6rem 1rem;
            border: 1px solid rgba(14,14,14,0.12);
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.06em;
            background: transparent;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .tip-option:hover { border-color: #B8860B; }
        .tip-option.tip-selected {
            background: #0E0E0E;
            color: #F7F3EC;
            border-color: #0E0E0E;
        }
    </style>
    @endif
@endpush

@section('content')
    <section class="subhero">
        <div class="container-ed">
            <p class="eyebrow">Almost there</p>
            <h1 class="font-display text-5xl md:text-6xl font-light mt-3">Checkout</h1>
        </div>
    </section>

    <section class="section-pad">
        <div class="container-ed">
            @if(empty($cart))
                <div class="alert alert-warn">
                    <div>
                        <p class="font-semibold">Your cart is empty.</p>
                        <p class="text-sm mt-1">Add some items before checking out.</p>
                        <a href="{{ route('menu.index') }}" class="btn btn-brand btn-sm mt-4">Browse menu</a>
                    </div>
                </div>
            @else
                <div id="checkout-form-container">
                    <div class="grid grid-cols-1 lg:grid-cols-[1fr_380px] gap-10">
                        <div>
                            <form id="checkout-form" method="POST" action="{{ route('checkout.process') }}" class="space-y-8">
                                @csrf

                                {{-- Order type --}}
                                <fieldset>
                                    <legend class="eyebrow mb-4">01 · How</legend>
                                    <label for="order_type" class="label">Order type</label>
                                    <select id="order_type" name="order_type" class="field">
                                        <option value="pickup">Pickup</option>
                                        <option value="delivery">Delivery</option>
                                    </select>
                                </fieldset>

                                <div id="table-number-container" class="hidden">
                                    <label for="table_number" class="label">Table number</label>
                                    <input type="text" name="table_number" id="table_number" class="field">
                                </div>

                                <div>
                                    <label for="scheduled_for" class="label">When</label>
                                    <select id="scheduled_time_option" class="field">
                                        <option value="now">As soon as possible</option>
                                        <option value="later">Schedule for later</option>
                                    </select>
                                </div>

                                <div id="scheduled-time-container" class="hidden">
                                    <label for="pickup_time" class="label">Schedule time</label>
                                    <input type="datetime-local" name="pickup_time" id="pickup_time" class="field">
                                </div>

                                {{-- Contact --}}
                                <fieldset class="pt-6 border-t border-line">
                                    <legend class="eyebrow mb-4">02 · You</legend>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                        <div>
                                            <label for="name" class="label">Full name <span class="label-req">*</span></label>
                                            <input type="text" name="name" id="name" autocomplete="name" required class="field">
                                        </div>
                                        <div>
                                            <label for="phone" class="label">Phone <span class="label-req">*</span></label>
                                            <input type="tel" name="phone" id="phone" autocomplete="tel" required class="field">
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label for="email" class="label">Email <span class="label-req">*</span></label>
                                            <input type="email" name="email" id="email" autocomplete="email" required class="field">
                                        </div>
                                    </div>
                                </fieldset>

                                {{-- Delivery address --}}
                                <div id="delivery-address-container" class="hidden pt-6 border-t border-line">
                                    <p class="eyebrow mb-4">03 · Where</p>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                        <div class="sm:col-span-2">
                                            <label for="address_street" class="label">Street address</label>
                                            <input type="text" name="address_street" id="address_street" class="field">
                                        </div>
                                        <div>
                                            <label for="address_city" class="label">City</label>
                                            <input type="text" name="address_city" id="address_city" class="field">
                                        </div>
                                        <div>
                                            <label for="address_postcode" class="label">Postcode</label>
                                            <input type="text" name="address_postcode" id="address_postcode" class="field">
                                        </div>
                                    </div>
                                </div>

                                {{-- Tip --}}
                                <div id="tip-container" class="hidden pt-6 border-t border-line">
                                    @php
                                        $tippingSettings = TippingSetting::getSettings();
                                        $tipPercentages = $tippingSettings->getAvailableTipPercentages();
                                        $tippingEnabled = $tippingSettings->isTippingEnabled();
                                    @endphp
                                    @if($tippingEnabled)
                                        <p class="eyebrow mb-3">Add a tip?</p>
                                        <div class="flex flex-wrap gap-2">
                                            <input type="hidden" name="tip_percentage" id="tip_percentage" value="0">
                                            <button type="button" class="tip-option" data-percentage="0">No tip</button>
                                            @foreach($tipPercentages as $percentage)
                                                @php
                                                    $percentValue = is_array($percentage) && isset($percentage['percentage'])
                                                        ? $percentage['percentage']
                                                        : (is_numeric($percentage) ? $percentage : null);
                                                @endphp
                                                @if($percentValue !== null)
                                                    <button type="button" class="tip-option" data-percentage="{{ $percentValue }}">{{ $percentValue }}%</button>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                {{-- Notes --}}
                                <div class="pt-6 border-t border-line">
                                    <label for="special_instructions" class="label">Special instructions</label>
                                    <textarea name="special_instructions" id="special_instructions" rows="3" class="field" placeholder="Allergies, preferences, delivery instructions…"></textarea>
                                </div>

                                {{-- Payment --}}
                                <fieldset class="pt-6 border-t border-line">
                                    <legend class="eyebrow mb-4">04 · Pay</legend>
                                    <label for="payment_method" class="label">Payment method</label>
                                    <select id="payment_method" name="payment_method" class="field">
                                        <option value="cash">Cash</option>
                                        <option value="credit_card">Credit card</option>
                                    </select>
                                </fieldset>

                                <div id="credit-card-container" class="hidden">
                                    @if(isset($stripeKey))
                                        <div class="bg-paper-warm rounded-xl p-5 border border-line">
                                            <div class="flex items-center justify-between mb-3">
                                                <span class="label mb-0">Card details</span>
                                                <div class="flex items-center gap-1.5">
                                                    <img src="https://cdn.jsdelivr.net/npm/payment-icons@1.1.0/min/flat/visa.svg" class="h-6" alt="Visa">
                                                    <img src="https://cdn.jsdelivr.net/npm/payment-icons@1.1.0/min/flat/mastercard.svg" class="h-6" alt="Mastercard">
                                                    <img src="https://cdn.jsdelivr.net/npm/payment-icons@1.1.0/min/flat/amex.svg" class="h-6" alt="Amex">
                                                </div>
                                            </div>
                                            <div id="card-element"></div>
                                            <div id="card-errors" class="field-error mt-2 min-h-[20px]" role="alert"></div>
                                            <div id="payment-status" class="hidden mt-3 text-sm text-ink-muted">Processing…</div>
                                        </div>
                                        <p class="mt-3 text-xs text-ink-muted flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                            Secured by Stripe · end-to-end encrypted
                                        </p>
                                        <input type="hidden" name="stripe_payment_id" id="stripe_payment_id">
                                    @else
                                        <div class="alert alert-warn">
                                            <div>
                                                <p class="font-semibold">Credit card processing unavailable</p>
                                                <p class="text-sm">Please choose cash, or try again later.</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </form>
                        </div>

                        {{-- Summary --}}
                        <aside>
                            <div class="bg-ink text-paper rounded-2xl p-8 lg:sticky lg:top-24">
                                <p class="eyebrow text-brand-light">Summary</p>
                                <h2 class="font-display text-2xl mt-2">Your order</h2>

                                <div id="cart-summary" class="mt-6">
                                    <div id="summary-items" class="space-y-3 mb-5 max-h-60 overflow-y-auto text-sm pr-2">
                                        @foreach($cart as $id => $item)
                                            <div class="flex justify-between gap-3">
                                                <div class="flex-1 min-w-0">
                                                    <span class="text-paper">{{ $item['quantity'] }} × {{ $item['name'] }}</span>
                                                    @if(isset($item['add_ons']) && count($item['add_ons']) > 0)
                                                        <div class="text-xs text-paper/50 mt-0.5">
                                                            + {{ implode(', ', array_map(fn($a) => \App\Models\AddOn::find($a)?->name, $item['add_ons'])) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <span class="numeral text-paper/80 shrink-0">${{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                                            </div>
                                        @endforeach
                                    </div>

                                    <dl class="border-t border-paper/15 pt-5 space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <dt class="text-paper/70">Subtotal</dt>
                                            <dd id="summary-subtotal" class="numeral">${{ number_format($subtotal, 2) }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-paper/70">Tax (13%)</dt>
                                            <dd id="summary-tax" class="numeral">${{ number_format($tax, 2) }}</dd>
                                        </div>
                                        <div id="delivery-fee-row" class="flex justify-between hidden">
                                            <dt class="text-paper/70">Delivery</dt>
                                            <dd id="summary-delivery-fee" class="numeral">$0.00</dd>
                                        </div>
                                        <div id="tip-amount-row" class="flex justify-between hidden">
                                            <dt class="text-paper/70">Tip</dt>
                                            <dd id="summary-tip-amount" class="numeral">$0.00</dd>
                                        </div>
                                    </dl>
                                    <div class="border-t border-paper/15 mt-5 pt-5 flex justify-between items-end">
                                        <span class="eyebrow text-paper/70">Total</span>
                                        <span id="summary-total" class="numeral text-2xl">${{ number_format($total, 2) }}</span>
                                    </div>
                                </div>

                                <button type="submit" form="checkout-form" id="checkout-button" class="btn btn-ember btn-block btn-lg mt-6">
                                    Place order
                                </button>

                                <a href="{{ route('cart.index') }}" class="arrow-link text-brand-light mt-5 justify-center w-full">
                                    ← Return to cart
                                </a>
                            </div>
                        </aside>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection

@if(isset($stripeKey))
@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const stripe = Stripe('{{ $stripeKey }}');
        const elements = stripe.elements();
        const cardElement = elements.create('card', {
            style: {
                base: {
                    fontSize: '15px',
                    fontFamily: '"Inter Tight", ui-sans-serif, system-ui, sans-serif',
                    color: '#0E0E0E',
                    backgroundColor: '#F7F3EC',
                    '::placeholder': { color: '#6B6258' },
                },
                invalid: { color: '#B8381F', iconColor: '#B8381F' }
            },
            hidePostalCode: true
        });
        cardElement.mount('#card-element');

        cardElement.on('change', (event) => {
            const displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
                displayError.style.color = '#B8381F';
            } else if (event.complete) {
                displayError.textContent = '✓ Card ready';
                displayError.style.color = '#10B981';
            } else {
                displayError.textContent = '';
            }
        });

        const orderTypeSelect = document.getElementById('order_type');
        const tableNumberContainer = document.getElementById('table-number-container');
        const deliveryAddressContainer = document.getElementById('delivery-address-container');
        const deliveryFeeRow = document.getElementById('delivery-fee-row');
        const deliveryFeeAmount = document.getElementById('summary-delivery-fee');
        const tipContainer = document.getElementById('tip-container');
        const tipAmountRow = document.getElementById('tip-amount-row');
        const tipAmountDisplay = document.getElementById('summary-tip-amount');
        const subtotalElement = document.getElementById('summary-subtotal');
        const taxElement = document.getElementById('summary-tax');
        const totalElement = document.getElementById('summary-total');
        const paymentMethodSelect = document.getElementById('payment_method');
        const creditCardContainer = document.getElementById('credit-card-container');
        const paymentStatus = document.getElementById('payment-status');
        const tipOptions = document.querySelectorAll('.tip-option');
        const tipPercentageInput = document.getElementById('tip_percentage');

        if (tableNumberContainer) tableNumberContainer.classList.add('hidden');

        if (orderTypeSelect) {
            orderTypeSelect.addEventListener('change', function() {
                if (this.value === 'delivery') {
                    deliveryAddressContainer?.classList.remove('hidden');
                    tipContainer?.classList.remove('hidden');
                    tipAmountRow?.classList.remove('hidden');
                    const deliveryFee = {{ DeliverySetting::getSettings()->delivery_fee ?? 0 }};
                    if (deliveryFeeRow) {
                        deliveryFeeAmount.textContent = '$' + deliveryFee.toFixed(2);
                        deliveryFeeRow.classList.remove('hidden');
                    }
                } else {
                    deliveryAddressContainer?.classList.add('hidden');
                    tipContainer?.classList.add('hidden');
                    tipAmountRow?.classList.add('hidden');
                    deliveryFeeRow?.classList.add('hidden');
                }
                updateTotal();
            });
            orderTypeSelect.dispatchEvent(new Event('change'));
        }

        const scheduledTimeOption = document.getElementById('scheduled_time_option');
        const scheduledTimeContainer = document.getElementById('scheduled-time-container');
        if (scheduledTimeOption && scheduledTimeContainer) {
            scheduledTimeOption.addEventListener('change', function() {
                scheduledTimeContainer.classList.toggle('hidden', this.value !== 'later');
            });
            scheduledTimeOption.dispatchEvent(new Event('change'));
        }

        if (tipOptions.length > 0 && tipPercentageInput) {
            tipOptions.forEach(option => {
                option.addEventListener('click', function() {
                    tipOptions.forEach(opt => opt.classList.remove('tip-selected'));
                    this.classList.add('tip-selected');
                    const percentage = parseInt(this.dataset.percentage);
                    tipPercentageInput.value = percentage;
                    const subtotal = parseFloat(subtotalElement.textContent.replace('$', ''));
                    const tipAmount = (subtotal * percentage / 100).toFixed(2);
                    if (tipAmountDisplay) tipAmountDisplay.textContent = '$' + tipAmount;
                    updateTotal();
                });
            });
            tipOptions[0]?.click();
        }

        function updateTotal() {
            if (!subtotalElement || !totalElement) return;
            const subtotal = parseFloat(subtotalElement.textContent.replace('$', ''));
            const tax = parseFloat(taxElement ? taxElement.textContent.replace('$', '') : 0);
            let total = subtotal + tax;
            if (deliveryFeeRow && !deliveryFeeRow.classList.contains('hidden') && deliveryFeeAmount) {
                total += parseFloat(deliveryFeeAmount.textContent.replace('$', ''));
            }
            if (tipAmountRow && !tipAmountRow.classList.contains('hidden') && tipAmountDisplay) {
                total += parseFloat(tipAmountDisplay.textContent.replace('$', ''));
            }
            totalElement.textContent = '$' + total.toFixed(2);
        }

        if (paymentMethodSelect) {
            paymentMethodSelect.addEventListener('change', function() {
                if (this.value === 'credit_card') {
                    creditCardContainer.classList.remove('hidden');
                    cardElement.update({ disabled: false });
                } else {
                    creditCardContainer.classList.add('hidden');
                    cardElement.update({ disabled: true });
                }
            });
            paymentMethodSelect.dispatchEvent(new Event('change'));
        }

        const checkoutForm = document.getElementById('checkout-form');
        const checkoutButton = document.getElementById('checkout-button');
        const stripe_payment_id = document.getElementById('stripe_payment_id');

        if (checkoutForm) {
            checkoutForm.addEventListener('submit', async function(event) {
                if (paymentMethodSelect.value === 'credit_card') {
                    event.preventDefault();
                    checkoutButton.disabled = true;
                    checkoutButton.textContent = 'Processing…';
                    paymentStatus.classList.remove('hidden');

                    try {
                        const response = await fetch('{{ route("checkout.payment-intent") }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({
                                amount: parseFloat(document.getElementById('summary-total').textContent.replace('$', '')),
                                tip_percentage: parseInt(document.getElementById('tip_percentage')?.value || 0)
                            })
                        });
                        const data = await response.json();
                        if (data.error) throw new Error(data.error);

                        const { error, paymentIntent } = await stripe.confirmCardPayment(data.clientSecret, {
                            payment_method: {
                                card: cardElement,
                                billing_details: {
                                    name: document.getElementById('name').value,
                                    email: document.getElementById('email').value,
                                    phone: document.getElementById('phone').value,
                                }
                            }
                        });
                        if (error) throw error;

                        stripe_payment_id.value = data.paymentIntentId;
                        paymentStatus.textContent = 'Payment confirmed — placing order…';
                        checkoutForm.submit();
                    } catch (error) {
                        console.error('Payment error:', error);
                        document.getElementById('card-errors').textContent = error.message;
                        document.getElementById('card-errors').style.color = '#B8381F';
                        paymentStatus.textContent = 'Payment failed. Please try again.';
                        checkoutButton.disabled = false;
                        checkoutButton.textContent = 'Place order';
                    }
                }
            });
        }
    });
</script>
@endpush
@endif
