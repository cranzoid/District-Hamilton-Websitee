@extends('emails.layout')

@section('title', 'Order confirmed · #' . $order->order_number)
@section('preheader', 'Your order #' . $order->order_number . ' is confirmed and being prepared.')
@section('eyebrow', 'Status · Confirmed')
@section('heading', 'We\'ve got you, ' . $order->customer_name . '.')
@section('subheading', 'Order #' . $order->order_number . ' is confirmed — the kitchen is on it.')

@section('content')
    <div style="text-align:center; margin-bottom:28px;">
        <span style="display:inline-block; padding:6px 16px; background:#E8F5E9; color:#2E7D32; border-radius:999px; font-family:'Helvetica Neue',Arial,sans-serif; font-size:11px; letter-spacing:0.18em; text-transform:uppercase; font-weight:700;">Confirmed</span>
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
        <tr><td style="padding:6px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase; width:40%;">Order type</td><td style="padding:6px 0;">{{ ucfirst($order->order_type) }}</td></tr>
        @if($order->isPickup())
        <tr><td style="padding:6px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase;">Pickup time</td><td style="padding:6px 0; font-weight:600;">{{ $order->pickup_time->format('F j, g:i A') }}</td></tr>
        @else
        <tr><td style="padding:6px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase;">Address</td><td style="padding:6px 0;">{{ $order->delivery_address }}</td></tr>
        <tr><td style="padding:6px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase;">Est. arrival</td><td style="padding:6px 0; font-weight:600;">{{ $order->pickup_time->format('F j, g:i A') }}</td></tr>
        @endif
    </table>

    <hr style="border:0; border-top:1px solid #E9E4DA; margin:0 0 20px 0;">

    <div style="font-family:'Helvetica Neue',Arial,sans-serif; font-size:11px; letter-spacing:0.2em; text-transform:uppercase; color:#B8860B; font-weight:600; margin-bottom:14px;">Your order</div>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        @foreach($order->items as $item)
        <tr>
            <td style="padding:10px 0; border-bottom:1px solid #F0EBDF; font-family:Georgia,serif; font-size:16px;">{{ $item->name }}</td>
            <td style="padding:10px 0; border-bottom:1px solid #F0EBDF; text-align:right; color:#777; white-space:nowrap;">× {{ $item->quantity }}</td>
            <td style="padding:10px 0; border-bottom:1px solid #F0EBDF; text-align:right; font-weight:600; white-space:nowrap; padding-left:16px;">${{ number_format($item->price, 2) }}</td>
        </tr>
        @endforeach
    </table>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:18px;">
        <tr><td style="padding:4px 0; color:#555;">Subtotal</td><td style="padding:4px 0; text-align:right;">${{ number_format($order->subtotal, 2) }}</td></tr>
        <tr><td style="padding:4px 0; color:#555;">Tax</td><td style="padding:4px 0; text-align:right;">${{ number_format($order->tax, 2) }}</td></tr>
        @if($order->isDelivery())
            <tr><td style="padding:4px 0; color:#555;">Delivery</td><td style="padding:4px 0; text-align:right;">${{ number_format($order->delivery_fee, 2) }}</td></tr>
            @if($order->tip_amount > 0)
            <tr><td style="padding:4px 0; color:#555;">Tip</td><td style="padding:4px 0; text-align:right;">${{ number_format($order->tip_amount, 2) }}</td></tr>
            @endif
        @endif
        <tr><td style="padding:14px 0 4px 0; border-top:1px solid #0E0E0E; font-family:Georgia,serif; font-size:18px;">Total</td><td style="padding:14px 0 4px 0; border-top:1px solid #0E0E0E; text-align:right; font-family:Georgia,serif; font-size:18px; font-weight:600;">${{ number_format($order->total, 2) }}</td></tr>
    </table>

    <p style="margin-top:28px; font-size:13px; color:#777; text-align:center;">We'll reach out again the moment it's {{ $order->isPickup() ? 'ready for pickup' : 'on its way' }}.</p>
@endsection
