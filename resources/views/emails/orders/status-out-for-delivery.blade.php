@extends('emails.layout')

@section('title', 'Out for delivery · #' . $order->order_number)
@section('preheader', 'Your order is on the way.')
@section('eyebrow', 'Status · En route')
@section('heading', 'On the way to you.')
@section('subheading', 'Hi ' . $order->customer_name . ' — order #' . $order->order_number . ' just left the restaurant.')

@section('content')
    <div style="text-align:center; margin-bottom:28px;">
        <span style="display:inline-block; padding:6px 16px; background:#E3F2FD; color:#1565C0; border-radius:999px; font-family:'Helvetica Neue',Arial,sans-serif; font-size:11px; letter-spacing:0.18em; text-transform:uppercase; font-weight:700;">Out for delivery</span>
    </div>

    <div style="background:#FAF7F1; border-left:3px solid #B8860B; padding:24px 26px; border-radius:6px;">
        <div style="font-family:'Helvetica Neue',Arial,sans-serif; font-size:11px; letter-spacing:0.2em; text-transform:uppercase; color:#B8860B; font-weight:600; margin-bottom:12px;">Headed to</div>
        <div style="font-family:Georgia,serif; font-size:20px; color:#0E0E0E; line-height:1.35;">{{ $order->delivery_address }}</div>
        <div style="margin-top:10px; color:#555; font-size:14px;">Estimated arrival · <strong style="color:#0E0E0E;">{{ $order->pickup_time->format('g:i A') }}</strong></div>
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:24px;">
        <tr><td style="padding:6px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase; width:40%;">Order</td><td style="padding:6px 0; font-weight:600;">#{{ $order->order_number }}</td></tr>
        <tr><td style="padding:6px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase;">Total</td><td style="padding:6px 0; font-family:Georgia,serif; font-size:18px; font-weight:600;">${{ number_format($order->total, 2) }}</td></tr>
        <tr><td style="padding:6px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase;">Payment</td><td style="padding:6px 0;">{{ ucfirst($order->payment_method) }}</td></tr>
    </table>

    <p style="margin-top:28px; font-size:13px; color:#777; text-align:center; font-style:italic;">Enjoy — and thanks for ordering with The District.</p>
@endsection
