<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AddOn;
use App\Models\DeliverySetting;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Jobs\SendOrderConfirmation;
use App\Jobs\NotifyKitchenOfOrder;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    /**
     * Display the checkout page.
     */
    public function index()
    {
        // Get the cart from the session
        $cart = session()->get('cart', []);
        
        // If cart is empty, redirect to cart page
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }
        
        // Calculate totals
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
            // Add add-on prices if any
            if (isset($item['add_ons']) && is_array($item['add_ons']) && !empty($item['add_ons'])) {
                $addOnIds = $item['add_ons'];
                $addOns = AddOn::whereIn('id', $addOnIds)->get();
                $addOnTotal = $addOns->sum('price') * $item['quantity'];
                $subtotal += $addOnTotal;
            }
        }
        
        // Calculate tax and total
        $taxRate = config('app.tax_rate', 0.13); // Default to 13%
        $tax = $subtotal * $taxRate;
        $total = $subtotal + $tax;
        
        // Set up Stripe if needed
        $stripeKey = config('services.stripe.key');
        
        return view('checkout.index', compact('cart', 'subtotal', 'tax', 'total', 'stripeKey'));
    }
    
    /**
     * Process the checkout form submission.
     */
    public function process(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'order_type' => 'required|in:pickup,takeout,delivery',
            'address_street' => 'nullable|string|max:255',
            'address_city' => 'nullable|string|max:100',
            'address_postcode' => 'nullable|string|max:20',
            'payment_method' => 'required|in:credit_card,cash',
            'pickup_time' => 'nullable|date_format:Y-m-d H:i:s',
            'special_instructions' => 'nullable|string|max:500',
            'stripe_payment_id' => 'nullable|string',
            'tip_percentage' => 'nullable|integer|min:0|max:100',
        ]);

        // Idempotency: if this PaymentIntent already produced an order, return it
        if ($request->filled('stripe_payment_id')) {
            $existingOrder = Order::where('stripe_payment_id', $request->stripe_payment_id)->first();
            if ($existingOrder) {
                session()->put('last_order_id', $existingOrder->id);
                return redirect()->route('checkout.success');
            }
        }

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Server-side: load current DB prices and check availability
        $cartItemIds = array_column($cart, 'item_id');
        $menuItems = MenuItem::whereIn('id', $cartItemIds)->get()->keyBy('id');

        foreach ($cart as $cartItem) {
            $dbItem = $menuItems->get($cartItem['item_id']);
            if (!$dbItem || !$dbItem->is_available || !$dbItem->is_visible) {
                $itemName = $dbItem ? $dbItem->name : 'Unknown item';
                return redirect()->route('cart.index')
                    ->with('error', "\"$itemName\" is no longer available. Please remove it from your cart.");
            }
        }

        // Recompute totals server-side — never trust request values
        $subtotal = 0;
        foreach ($cart as $item) {
            $dbItem = $menuItems->get($item['item_id']);
            $itemPrice = $dbItem ? (float) $dbItem->price : (float) $item['price'];
            $subtotal += $itemPrice * $item['quantity'];

            if (isset($item['add_ons']) && is_array($item['add_ons']) && !empty($item['add_ons'])) {
                $addOns = AddOn::whereIn('id', $item['add_ons'])->get();
                $subtotal += $addOns->sum('price') * $item['quantity'];
            }
        }

        $taxRate = config('app.tax_rate', 0.13);
        $tax = round($subtotal * $taxRate, 2);
        $deliveryFee = 0;
        $tipPercentage = 0;
        $tipAmount = 0;

        if ($request->order_type === 'delivery') {
            $deliverySettings = DeliverySetting::getSettings();
            $deliveryFee = $deliverySettings->delivery_fee;

            if ($request->filled('tip_percentage') && $request->tip_percentage > 0) {
                $tipPercentage = (int) $request->tip_percentage;
                $tippingSettings = \App\Models\TippingSetting::getSettings();
                if ($tippingSettings->isTippingEnabled()) {
                    try {
                        $availablePercentages = $tippingSettings->getAvailableTipPercentages();
                        if (in_array($tipPercentage, $availablePercentages)) {
                            $tipAmount = $tippingSettings->calculateTipAmount($subtotal, $tipPercentage);
                        } else {
                            $tipPercentage = 0;
                        }
                    } catch (\Exception $e) {
                        Log::error('Tip calculation error: ' . $e->getMessage());
                        $tipPercentage = 0;
                    }
                }
            }
        }

        $total = $subtotal + $tax + $deliveryFee + $tipAmount;

        $address = null;
        if ($request->order_type === 'delivery' && $request->address_street) {
            $address = $request->address_street . ', ' .
                       $request->address_city . ', ' .
                       $request->address_postcode;
        }

        // Confirm Stripe payment before touching the DB
        $paymentStatus = 'pending';
        $stripePaymentId = null;

        if ($request->payment_method === 'credit_card') {
            if (!$request->filled('stripe_payment_id')) {
                return redirect()->back()
                    ->with('error', 'Payment processing failed. Please try again.')
                    ->withInput();
            }

            try {
                Stripe::setApiKey(config('services.stripe.secret'));
                $paymentIntent = PaymentIntent::retrieve($request->stripe_payment_id);

                if ($paymentIntent->status !== 'succeeded') {
                    $paymentIntent->confirm();
                }

                if ($paymentIntent->status === 'succeeded') {
                    $paymentStatus = 'paid';
                    $stripePaymentId = $paymentIntent->id;
                } else {
                    return redirect()->back()
                        ->with('error', 'Payment could not be confirmed. Please check your card details and try again.')
                        ->withInput();
                }
            } catch (ApiErrorException $e) {
                Log::error('Stripe payment error: ' . $e->getMessage());
                return redirect()->back()
                    ->with('error', 'Payment processing failed. Please check your card details and try again.')
                    ->withInput();
            }
        }

        $orderNumber = Order::generateOrderNumber();
        $orderType = $request->order_type === 'takeout' ? 'pickup' : $request->order_type;

        try {
            $order = DB::transaction(function () use (
                $request, $orderNumber, $orderType, $address, $subtotal, $tax,
                $deliveryFee, $tipPercentage, $tipAmount, $total,
                $paymentStatus, $stripePaymentId, $cart, $menuItems
            ) {
                $order = Order::create([
                    'order_number' => $orderNumber,
                    'customer_name' => $request->name,
                    'customer_email' => $request->email,
                    'customer_phone' => $request->phone,
                    'order_type' => $orderType,
                    'delivery_address' => $address,
                    'tip_percentage' => $tipPercentage,
                    'tip_amount' => $tipAmount,
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'delivery_fee' => $deliveryFee,
                    'total' => $total,
                    'payment_method' => $request->payment_method,
                    'payment_status' => $paymentStatus,
                    'status' => 'pending',
                    'stripe_payment_id' => $stripePaymentId,
                    'pickup_time' => $request->pickup_time,
                    'notes' => $request->special_instructions,
                ]);

                foreach ($cart as $cartItem) {
                    $dbItem = $menuItems->get($cartItem['item_id']);
                    $itemPrice = $dbItem ? (float) $dbItem->price : (float) $cartItem['price'];

                    $orderItem = new OrderItem([
                        'menu_item_id' => $cartItem['item_id'],
                        'name' => $cartItem['name'],
                        'quantity' => $cartItem['quantity'],
                        'price' => $itemPrice,
                        'subtotal' => $itemPrice * $cartItem['quantity'],
                        'special_instructions' => $cartItem['special_instructions'] ?? null,
                    ]);
                    $order->items()->save($orderItem);

                    if (isset($cartItem['add_ons']) && !empty($cartItem['add_ons'])) {
                        $orderItem->addOns()->attach($cartItem['add_ons']);
                    }
                }

                return $order;
            });
        } catch (\Exception $e) {
            Log::error('Order creation failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'There was a problem placing your order. Please try again.')
                ->withInput();
        }

        session()->forget('cart');
        session()->put('last_order_id', $order->id);

        SendOrderConfirmation::dispatch($order);
        NotifyKitchenOfOrder::dispatch($order);

        return redirect()->route('checkout.success');
    }
    
    /**
     * Handle Stripe payment intent creation.
     */
    public function createPaymentIntent(Request $request)
    {
        // Validate request
        $request->validate([
            'amount' => 'required|numeric|min:0.50',
            'tip_percentage' => 'nullable|integer|min:0|max:100',
        ]);
        
        try {
            // Set Stripe API key
            Stripe::setApiKey(config('services.stripe.secret'));
            
            // Create a payment intent
            $paymentIntent = PaymentIntent::create([
                'amount' => round($request->amount * 100), // Convert to cents
                'currency' => 'cad',
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
                'metadata' => [
                    'tip_percentage' => $request->tip_percentage ?? 0,
                ],
            ]);
            
            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
                'paymentIntentId' => $paymentIntent->id
            ]);
        } catch (ApiErrorException $e) {
            Log::error('Stripe payment intent error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Display the order success page.
     */
    public function success()
    {
        // Get the last order ID from the session
        $orderId = session()->get('last_order_id');
        
        // If no order ID is found, redirect to home
        if (!$orderId) {
            return redirect()->route('home');
        }
        
        // Find the order with its items
        $order = Order::with('items')->find($orderId);
        
        // If order not found, redirect to home
        if (!$order) {
            return redirect()->route('home');
        }
        
        return view('checkout.success', compact('order'));
    }
} 