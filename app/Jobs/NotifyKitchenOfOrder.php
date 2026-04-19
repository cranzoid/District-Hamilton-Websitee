<?php

namespace App\Jobs;

use App\Mail\OrderReceived;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NotifyKitchenOfOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public Order $order) {}

    public function handle(): void
    {
        $kitchenEmail = config('restaurant.kitchen_email', config('mail.from.address'));
        Mail::to($kitchenEmail)->send(new OrderReceived($this->order));
    }
}
