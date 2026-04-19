<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt · #{{ $order->order_number }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Courier New', 'Menlo', monospace;
            margin: 0;
            padding: 0;
            font-size: 12px;
            line-height: 1.45;
            color: #000;
        }
        .receipt {
            max-width: 80mm;
            margin: 0 auto;
            padding: 6mm 5mm;
        }
        .brand {
            text-align: center;
            margin-bottom: 6mm;
        }
        .brand-mark {
            font-family: Georgia, 'Times New Roman', serif;
            font-style: italic;
            font-weight: 400;
            font-size: 20px;
            letter-spacing: -0.01em;
            margin-bottom: 2mm;
        }
        .brand-sub {
            font-size: 9px;
            letter-spacing: 0.22em;
            text-transform: uppercase;
        }
        .brand-meta {
            font-size: 10px;
            margin-top: 3mm;
            color: #333;
        }
        .rule {
            border: 0;
            border-top: 1px dashed #000;
            margin: 4mm 0;
        }
        .rule-solid {
            border: 0;
            border-top: 1px solid #000;
            margin: 3mm 0;
        }
        .eyebrow {
            font-size: 9px;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            margin-bottom: 2mm;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.6mm 0;
            font-size: 11px;
        }
        .info-row .k { color: #333; text-transform: uppercase; font-size: 9px; letter-spacing: 0.12em; padding-top: 1px; }
        .info-row .v { font-weight: bold; text-align: right; }
        table.items { width: 100%; border-collapse: collapse; }
        table.items th {
            font-size: 9px;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            text-align: left;
            padding: 1mm 0;
            border-bottom: 1px solid #000;
            font-weight: normal;
        }
        table.items th.r { text-align: right; }
        table.items td {
            padding: 1.6mm 0;
            vertical-align: top;
            font-size: 11px;
            border-bottom: 1px dotted #999;
        }
        table.items td.r { text-align: right; white-space: nowrap; }
        .item-sub { font-size: 10px; color: #333; margin-top: 0.6mm; font-style: italic; }
        .totals .row { display: flex; justify-content: space-between; padding: 0.8mm 0; font-size: 11px; }
        .totals .row.grand {
            border-top: 2px solid #000;
            padding-top: 2mm;
            margin-top: 1mm;
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }
        .footer {
            text-align: center;
            margin-top: 6mm;
            padding-top: 4mm;
            border-top: 1px dashed #000;
            font-size: 10px;
            line-height: 1.55;
        }
        .footer .thanks {
            font-family: Georgia, serif;
            font-style: italic;
            font-size: 13px;
            margin-bottom: 2mm;
        }
        .actions {
            margin-top: 12mm;
            text-align: center;
        }
        .actions button {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            font-size: 11px;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            padding: 8px 18px;
            margin: 0 4px;
            background: #0E0E0E;
            color: #F7F3EC;
            border: 1px solid #0E0E0E;
            border-radius: 999px;
            cursor: pointer;
        }
        .actions button.ghost { background: transparent; color: #0E0E0E; }
        @media print {
            @page { size: 80mm 297mm; margin: 0; }
            body { width: 80mm; margin: 0; padding: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="brand">
            <div class="brand-mark">The <span style="font-style:normal;">District</span></div>
            <div class="brand-sub">Tapas + Bar</div>
            <div class="brand-meta">
                61 Barton St E · Hamilton, ON<br>
                (905) 522-2580<br>
                {{ now()->format('M j, Y · h:i A') }}
            </div>
        </div>

        <hr class="rule">

        <div class="eyebrow">Order</div>
        <div class="info-row"><span class="k">Number</span><span class="v">#{{ $order->order_number }}</span></div>
        <div class="info-row"><span class="k">Type</span><span class="v">{{ ucfirst($order->order_type) }}</span></div>
        <div class="info-row"><span class="k">Customer</span><span class="v">{{ $order->customer_name }}</span></div>
        @if($order->order_type == 'delivery')
            <div class="info-row"><span class="k">Address</span><span class="v">{{ $order->delivery_address }}</span></div>
        @endif
        <div class="info-row"><span class="k">{{ $order->order_type == 'delivery' ? 'Delivery' : 'Pickup' }}</span><span class="v">{{ $order->pickup_time ? $order->pickup_time->format('M j · h:i A') : 'ASAP' }}</span></div>
        <div class="info-row"><span class="k">Payment</span><span class="v">{{ ucfirst($order->payment_method) }}</span></div>

        <hr class="rule">

        <table class="items">
            <thead>
                <tr><th>Item</th><th class="r" style="width:14mm;">Qty</th><th class="r" style="width:20mm;">Amount</th></tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>
                            {{ $item->name }}
                            @if($item->special_instructions)
                                <div class="item-sub">{{ $item->special_instructions }}</div>
                            @endif
                            @if(isset($item->options['add_ons']) && count($item->options['add_ons']) > 0)
                                <div class="item-sub">+ {{ collect($item->options['add_ons'])->pluck('name')->implode(', ') }}</div>
                            @endif
                        </td>
                        <td class="r">{{ $item->quantity }}</td>
                        <td class="r">${{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals" style="margin-top:3mm;">
            <div class="row"><span>Subtotal</span><span>${{ number_format($order->subtotal, 2) }}</span></div>
            <div class="row"><span>Tax (13%)</span><span>${{ number_format($order->tax, 2) }}</span></div>
            @if($order->order_type == 'delivery')
                <div class="row"><span>Delivery</span><span>${{ number_format($order->delivery_fee, 2) }}</span></div>
            @endif
            @if($order->tip_amount > 0)
                <div class="row"><span>Tip ({{ $order->tip_percentage }}%)</span><span>${{ number_format($order->tip_amount, 2) }}</span></div>
            @endif
            @if($order->gift_card_code_used)
                <div class="row"><span>Gift card</span><span>-${{ number_format($order->gift_card_amount ?? 0, 2) }}</span></div>
            @endif
            <div class="row grand"><span>Total</span><span>${{ number_format($order->total, 2) }}</span></div>
        </div>

        <div class="footer">
            <div class="thanks">Thank you — see you soon.</div>
            <div>Placed · {{ $order->created_at->format('M j, Y · h:i A') }}</div>
            @if($order->status == 'completed')
                <div>Completed · {{ $order->updated_at->format('M j, Y · h:i A') }}</div>
            @else
                <div>Status · {{ ucfirst($order->status) }}</div>
            @endif
        </div>

        <div class="actions no-print">
            <button onclick="window.print()">Print</button>
            <button class="ghost" onclick="window.close()">Close</button>
        </div>
    </div>
</body>
</html>
