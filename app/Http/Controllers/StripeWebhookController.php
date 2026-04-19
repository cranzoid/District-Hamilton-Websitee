<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        if (!$secret) {
            Log::warning('STRIPE_WEBHOOK_SECRET not configured — skipping signature check');
        } else {
            try {
                Webhook::constructEvent($payload, $sigHeader, $secret);
            } catch (SignatureVerificationException $e) {
                Log::warning('Stripe webhook signature verification failed: ' . $e->getMessage());
                return response()->json(['error' => 'Invalid signature'], 400);
            }
        }

        $event = json_decode($payload);

        match ($event->type ?? '') {
            'payment_intent.succeeded' => $this->handlePaymentIntentSucceeded($event->data->object),
            'payment_intent.payment_failed' => $this->handlePaymentIntentFailed($event->data->object),
            'charge.refunded' => $this->handleChargeRefunded($event->data->object),
            default => null,
        };

        return response()->json(['status' => 'ok']);
    }

    private function handlePaymentIntentSucceeded(object $paymentIntent): void
    {
        $order = Order::where('stripe_payment_id', $paymentIntent->id)->first();
        if ($order && $order->payment_status !== 'paid') {
            $order->update(['payment_status' => 'paid']);
            Log::info("Order {$order->order_number} marked paid via webhook");
        }
    }

    private function handlePaymentIntentFailed(object $paymentIntent): void
    {
        $order = Order::where('stripe_payment_id', $paymentIntent->id)->first();
        if ($order && $order->payment_status !== 'paid') {
            $order->update(['payment_status' => 'failed']);
            Log::info("Order {$order->order_number} marked failed via webhook");
        }
    }

    private function handleChargeRefunded(object $charge): void
    {
        $paymentIntentId = $charge->payment_intent ?? null;
        if (!$paymentIntentId) {
            return;
        }

        $order = Order::where('stripe_payment_id', $paymentIntentId)->first();
        if ($order) {
            $order->update(['payment_status' => 'refunded']);
            Log::info("Order {$order->order_number} marked refunded via webhook");
        }
    }
}
