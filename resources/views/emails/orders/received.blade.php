@extends('emails.layout')

@section('title', 'New order · #' . $orderNumber)
@section('preheader', 'New order from ' . $customerName . ' — #' . $orderNumber)
@section('eyebrow', 'Kitchen · New ticket')
@section('heading', 'New order on the pass.')
@section('subheading', 'A fresh ticket just landed. Details below.')

@section('content')
    <div style="font-family:'Helvetica Neue',Arial,sans-serif; font-size:11px; letter-spacing:0.2em; text-transform:uppercase; color:#B8860B; font-weight:600; margin-bottom:10px;">Customer</div>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr><td style="padding:5px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase; width:35%;">Name</td><td style="padding:5px 0; color:#0E0E0E; font-weight:600;">{{ $customerName }}</td></tr>
        <tr><td style="padding:5px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase;">Email</td><td style="padding:5px 0;"><a href="mailto:{{ $customerEmail }}" style="color:#B8381F;">{{ $customerEmail }}</a></td></tr>
        <tr><td style="padding:5px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase;">Phone</td><td style="padding:5px 0;"><a href="tel:{{ $customerPhone }}" style="color:#0E0E0E; text-decoration:none; border-bottom:1px dotted #999;">{{ $customerPhone }}</a></td></tr>
        @if($orderType === 'delivery' && $deliveryAddress)
        <tr><td style="padding:5px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase;">Address</td><td style="padding:5px 0;">{{ $deliveryAddress }}</td></tr>
        @endif
    </table>

    <hr style="border:0; border-top:1px solid #E9E4DA; margin:24px 0;">

    <div style="font-family:'Helvetica Neue',Arial,sans-serif; font-size:11px; letter-spacing:0.2em; text-transform:uppercase; color:#B8860B; font-weight:600; margin-bottom:10px;">Order</div>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr><td style="padding:5px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase; width:35%;">Number</td><td style="padding:5px 0; font-weight:600;">#{{ $orderNumber }}</td></tr>
        <tr><td style="padding:5px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase;">Type</td><td style="padding:5px 0;">{{ ucfirst($orderType) }}</td></tr>
        <tr><td style="padding:5px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase;">Status</td><td style="padding:5px 0;">{{ ucfirst($orderStatus) }}</td></tr>
        <tr><td style="padding:5px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase;">Payment</td><td style="padding:5px 0;">{{ ucfirst(str_replace('_', ' ', $paymentMethod)) }} · {{ ucfirst($paymentStatus) }}</td></tr>
        <tr><td style="padding:5px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase;">Timing</td><td style="padding:5px 0;">{{ $pickupTime ? $pickupTime->format('F j, g:i A') : 'ASAP' }}</td></tr>
        @if($notes)
        <tr><td style="padding:5px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase;">Notes</td><td style="padding:5px 0; font-style:italic; color:#B8381F;">{{ $notes }}</td></tr>
        @endif
    </table>

    <hr style="border:0; border-top:1px solid #E9E4DA; margin:24px 0;">

    <div style="font-family:'Helvetica Neue',Arial,sans-serif; font-size:11px; letter-spacing:0.2em; text-transform:uppercase; color:#B8860B; font-weight:600; margin-bottom:14px;">Items</div>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        @foreach($items as $item)
        <tr>
            <td style="padding:12px 0; border-bottom:1px solid #F0EBDF; vertical-align:top;">
                <div style="font-family:Georgia,serif; font-size:17px; color:#0E0E0E;">{{ $item->name }}</div>
                @if($item->special_instructions)
                    <div style="margin-top:4px; font-size:12px; color:#B8381F; font-style:italic;">“{{ $item->special_instructions }}”</div>
                @endif
                @if($item->addOns->count() > 0)
                    <div style="margin-top:4px; font-size:12px; color:#888;">+ {{ $item->addOns->pluck('name')->join(', ') }}</div>
                @endif
            </td>
            <td style="padding:12px 0; border-bottom:1px solid #F0EBDF; text-align:right; vertical-align:top; white-space:nowrap;">
                <div style="font-size:13px; color:#777;">× {{ $item->quantity }}</div>
                <div style="margin-top:4px; font-weight:600;">${{ number_format($item->subtotal, 2) }}</div>
            </td>
        </tr>
        @endforeach
    </table>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:18px;">
        <tr><td style="padding:4px 0; color:#555;">Subtotal</td><td style="padding:4px 0; text-align:right;">${{ $subtotal }}</td></tr>
        <tr><td style="padding:4px 0; color:#555;">Tax</td><td style="padding:4px 0; text-align:right;">${{ $tax }}</td></tr>
        @if($deliveryFee > 0)
        <tr><td style="padding:4px 0; color:#555;">Delivery</td><td style="padding:4px 0; text-align:right;">${{ $deliveryFee }}</td></tr>
        @endif
        <tr><td style="padding:14px 0 4px 0; border-top:1px solid #0E0E0E; font-family:Georgia,serif; font-size:18px;">Total</td><td style="padding:14px 0 4px 0; border-top:1px solid #0E0E0E; text-align:right; font-family:Georgia,serif; font-size:18px; font-weight:600;">${{ $total }}</td></tr>
    </table>

    <div style="text-align:center; margin:36px 0 0 0;">
        <a href="{{ $orderUrl }}" style="display:inline-block; background:#0E0E0E; color:#FFFFFF; text-decoration:none; padding:14px 32px; border-radius:999px; font-family:'Helvetica Neue',Arial,sans-serif; font-size:12px; letter-spacing:0.16em; text-transform:uppercase; font-weight:600;">Manage order</a>
    </div>
@endsection
