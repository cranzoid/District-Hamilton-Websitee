@extends('emails.layout')

@section('title', 'Ready for pickup · #' . $order->order_number)
@section('preheader', 'Your order is ready for pickup at The District.')
@section('eyebrow', 'Status · Ready')
@section('heading', 'Come and get it.')
@section('subheading', 'Hi ' . $order->customer_name . ' — order #' . $order->order_number . ' is ready and waiting.')

@section('content')
    <div style="text-align:center; margin-bottom:28px;">
        <span style="display:inline-block; padding:6px 16px; background:#E8F5E9; color:#2E7D32; border-radius:999px; font-family:'Helvetica Neue',Arial,sans-serif; font-size:11px; letter-spacing:0.18em; text-transform:uppercase; font-weight:700;">Ready for pickup</span>
    </div>

    <div style="background:#FAF7F1; border-left:3px solid #B8860B; padding:24px 26px; border-radius:6px;">
        <div style="font-family:'Helvetica Neue',Arial,sans-serif; font-size:11px; letter-spacing:0.2em; text-transform:uppercase; color:#B8860B; font-weight:600; margin-bottom:12px;">Pickup</div>
        <div style="font-family:Georgia,serif; font-size:22px; color:#0E0E0E; margin-bottom:8px;">{{ config('restaurant.address', '61 Barton St E, Hamilton, ON') }}</div>
        <div style="color:#555; font-size:14px; line-height:1.6;">Please have order <strong style="color:#0E0E0E;">#{{ $order->order_number }}</strong> ready when you arrive.</div>
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:24px;">
        <tr><td style="padding:6px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase; width:40%;">Order total</td><td style="padding:6px 0; font-family:Georgia,serif; font-size:18px; font-weight:600;">${{ number_format($order->total, 2) }}</td></tr>
        <tr><td style="padding:6px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase;">Payment</td><td style="padding:6px 0;">{{ ucfirst($order->payment_method) }}</td></tr>
    </table>

    <p style="margin-top:28px; font-size:13px; color:#777; text-align:center; font-style:italic;">We'll keep it warm — but sooner is always better.</p>
@endsection
