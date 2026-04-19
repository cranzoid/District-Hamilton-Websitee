@extends('emails.layout')

@section('title', 'Order prepared · #' . $order->order_number)
@section('preheader', 'Order #' . $order->order_number . ' has been prepared.')
@section('eyebrow', 'Status · Prepared')
@section('heading', 'Off the pass.')
@section('subheading', 'Hi ' . $order->customer_name . ' — order #' . $order->order_number . ' is plated and ready for the next step.')

@section('content')
    <div style="text-align:center; margin-bottom:28px;">
        <span style="display:inline-block; padding:6px 16px; background:#FFF4E5; color:#B8860B; border-radius:999px; font-family:'Helvetica Neue',Arial,sans-serif; font-size:11px; letter-spacing:0.18em; text-transform:uppercase; font-weight:700;">Prepared</span>
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
        <tr><td style="padding:6px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase; width:40%;">Order</td><td style="padding:6px 0; font-weight:600;">#{{ $order->order_number }}</td></tr>
        <tr><td style="padding:6px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase;">Type</td><td style="padding:6px 0;">{{ ucfirst($order->order_type) }}</td></tr>
    </table>

    <div style="background:#FAF7F1; border-left:3px solid #B8860B; padding:20px 22px; border-radius:6px;">
        <div style="font-family:'Helvetica Neue',Arial,sans-serif; font-size:11px; letter-spacing:0.2em; text-transform:uppercase; color:#B8860B; font-weight:600; margin-bottom:8px;">What's next</div>
        @if($order->isPickup())
            <p style="margin:0; color:#2A2A2A; line-height:1.6;">We'll ping you the moment your order is ready for pickup. Please have your order number handy when you arrive.</p>
        @else
            <p style="margin:0; color:#2A2A2A; line-height:1.6;">Your order is about to head out the door. We'll send another note the second it's on the way.</p>
        @endif
    </div>

    <p style="margin-top:28px; font-size:13px; color:#777; text-align:center;">Thanks for choosing The District.</p>
@endsection
