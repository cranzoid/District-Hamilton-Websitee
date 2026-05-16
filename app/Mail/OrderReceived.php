<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class OrderReceived extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The order instance.
     *
     * @var \App\Models\Order
     */
    public $order;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: 'New Order Received - #' . $this->order->order_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.received',
            with: [
                'order' => $this->order,
                'customerName' => $this->order->customer_name,
                'customerEmail' => $this->order->customer_email,
                'customerPhone' => $this->order->customer_phone,
                'orderNumber' => $this->order->order_number,
                'orderType' => $this->order->order_type,
                'deliveryAddress' => $this->order->delivery_address,
                'orderStatus' => $this->order->status,
                'paymentMethod' => $this->order->payment_method,
                'paymentStatus' => $this->order->payment_status,
                'subtotal' => number_format($this->order->subtotal, 2),
                'tax' => number_format($this->order->tax, 2),
                'deliveryFee' => number_format($this->order->delivery_fee, 2),
                'total' => number_format($this->order->total, 2),
                'items' => $this->order->items()->with(['menuItem', 'addOns'])->get(),
                'pickupTime' => $this->order->pickup_time,
                'notes' => $this->order->notes,
                'orderUrl' => route('filament.admin.resources.orders.edit', $this->order->id),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
