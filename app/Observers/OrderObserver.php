<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\OrderNotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        // Defer until after the DB transaction commits so order items are available
        DB::afterCommit(function () use ($order) {
            try {
                $notificationService = new OrderNotificationService();
                $notificationService->sendAdminNewOrderNotification($order);
            } catch (\Exception $e) {
                Log::error('Failed to send admin new order notification: ' . $e->getMessage());
            }
        });
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // Check if the status was changed
        if ($order->isDirty('status')) {
            $oldStatus = $order->getOriginal('status');
            $newStatus = $order->status;
            
            $this->handleStatusChange($order, $oldStatus, $newStatus);
        }
    }
    
    /**
     * Handle order status changes and send appropriate notifications
     */
    private function handleStatusChange(Order $order, string $oldStatus, string $newStatus): void
    {
        $notificationService = new OrderNotificationService();
        
        try {
            // Status change: pending -> preparing/confirmed
            if ($oldStatus === 'pending' && $newStatus === 'preparing') {
                $notificationService->sendOrderConfirmedNotification($order);
            }
            
            // Status change: preparing -> ready (for pickup orders)
            // or preparing -> out for delivery (for delivery orders)
            if ($oldStatus === 'preparing') {
                if ($newStatus === 'ready' && $order->isPickup()) {
                    $notificationService->sendOrderReadyForPickupNotification($order);
                } elseif ($newStatus === 'out_for_delivery' && $order->isDelivery()) {
                    $notificationService->sendOrderOutForDeliveryNotification($order);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to send order status notification: ' . $e->getMessage());
        }
    }
} 