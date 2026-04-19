@extends('emails.layout')

@section('title', 'Order confirmation · #' . $orderNumber)
@section('preheader', 'Your order is in — #' . $orderNumber . '. We\'ll have it ready shortly.')
@section('eyebrow', 'Order received')
@section('heading', 'Thank you, ' . $customerName . '.')
@section('subheading', 'Your order is in our hands. Here\'s everything on the ticket.')

@section('content')
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
        <tr><td style="padding:6px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase; width:40%;">Order</td><td style="padding:6px 0; font-family:'Helvetica Neue',Arial,sans-serif; font-weight:600; letter-spacing:0.02em;">#{{ $orderNumber }}</td></tr>
        <tr><td style="padding:6px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase;">Type</td><td style="padding:6px 0;">{{ ucfirst($orderType) }}</td></tr>
        <tr><td style="padding:6px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase;">Status</td><td style="padding:6px 0;"><span style="display:inline-block; padding:4px 12px; background:#FAF0E1; color:#B8860B; border-radius:999px; font-size:11px; letter-spacing:0.14em; text-transform:uppercase; font-weight:600;">{{ ucfirst($orderStatus) }}</span></td></tr>
        <tr><td style="padding:6px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase;">Payment</td><td style="padding:6px 0;">{{ ucfirst(str_replace('_', ' ', $paymentMethod)) }} · {{ ucfirst($paymentStatus) }}</td></tr>
        <tr><td style="padding:6px 0; color:#777; font-size:11px; letter-spacing:0.18em; text-transform:uppercase;">{{ strtolower($orderType) === 'delivery' ? 'Delivery' : 'Pickup' }}</td><td style="padding:6px 0;">{{ $pickupTime ? $pickupTime->format('F j, g:i A') : 'As soon as possible' }}</td></tr>
    </table>

    <hr style="border:0; border-top:1px solid #E9E4DA; margin:0 0 20px 0;">

    <div style="font-family:'Helvetica Neue',Arial,sans-serif; font-size:11px; letter-spacing:0.2em; text-transform:uppercase; color:#B8860B; font-weight:600; margin-bottom:14px;">The order</div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        @foreach($items as $item)
        <tr>
            <td style="padding:12px 0; border-bottom:1px solid #F0EBDF; vertical-align:top;">
                <div style="font-family:Georgia,serif; font-size:17px; color:#0E0E0E;">{{ $item->menuItem->name ?? 'Item' }}</div>
                @if($item->special_instructions)
                    <div style="margin-top:4px; font-size:12px; color:#888; font-style:italic;">“{{ $item->special_instructions }}”</div>
                @endif
                @if($item->addOns->count() > 0)
                    <div style="margin-top:4px; font-size:12px; color:#888;">+ {{ $item->addOns->pluck('name')->join(', ') }}</div>
                @endif
            </td>
            <td style="padding:12px 0; border-bottom:1px solid #F0EBDF; text-align:right; vertical-align:top; white-space:nowrap;">
                <div style="font-size:13px; color:#777;">× {{ $item->quantity }}</div>
                <div style="margin-top:4px; font-family:'Helvetica Neue',Arial,sans-serif; font-weight:600;">${{ number_format($item->subtotal, 2) }}</div>
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
        <tr><td style="padding:14px 0 4px 0; border-top:1px solid #0E0E0E; font-family:Georgia,serif; font-size:18px; color:#0E0E0E;">Total</td><td style="padding:14px 0 4px 0; border-top:1px solid #0E0E0E; text-align:right; font-family:Georgia,serif; font-size:18px; color:#0E0E0E; font-weight:600;">${{ $total }}</td></tr>
    </table>

    <div style="text-align:center; margin:36px 0 12px 0;">
        <a href="{{ route('orders.show', $order->id) }}" style="display:inline-block; background:#B8381F; color:#FFFFFF; text-decoration:none; padding:14px 32px; border-radius:999px; font-family:'Helvetica Neue',Arial,sans-serif; font-size:12px; letter-spacing:0.16em; text-transform:uppercase; font-weight:600;">View order</a>
    </div>

    <p style="margin-top:28px; font-size:13px; color:#777; text-align:center;">Questions? Call us at {{ $restaurantPhone }}.</p>
@endsection
