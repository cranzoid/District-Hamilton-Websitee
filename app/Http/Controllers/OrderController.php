<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    public function print(Order $order)
    {
        return view('orders.print', compact('order'));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="orders-' . now()->format('Y-m-d') . '.csv"',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Order #', 'Date', 'Customer', 'Email', 'Phone',
                'Type', 'Status', 'Payment Status', 'Payment Method',
                'Subtotal', 'Tax', 'Delivery Fee', 'Tip', 'Total',
                'Pickup Time', 'Delivery Address', 'Notes',
            ]);

            Order::with('items')->orderByDesc('created_at')->chunk(200, function ($orders) use ($handle) {
                foreach ($orders as $order) {
                    fputcsv($handle, [
                        $order->order_number,
                        $order->created_at->format('Y-m-d H:i'),
                        $order->customer_name,
                        $order->customer_email,
                        $order->customer_phone,
                        $order->order_type,
                        $order->status,
                        $order->payment_status,
                        $order->payment_method,
                        number_format($order->subtotal, 2),
                        number_format($order->tax, 2),
                        number_format($order->delivery_fee, 2),
                        number_format($order->tip_amount, 2),
                        number_format($order->total, 2),
                        $order->pickup_time?->format('Y-m-d H:i'),
                        $order->delivery_address,
                        $order->notes,
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }
}

