<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Listener: SendOrderStatusEmail
 */

namespace App\Listeners;

use App\Events\OrderStatusUpdated;
use App\Jobs\SendEmailNotification;
use App\Jobs\SendPushNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOrderStatusEmail implements ShouldQueue
{
    protected $templates = [
        'paid' => 'order_confirmation',
        'processing' => null, // No email, just notification
        'shipped' => 'order_shipped',
        'delivered' => 'order_delivered',
        'cancelled' => null, // Custom handling
        'refunded' => null, // Custom handling
    ];

    public function handle(OrderStatusUpdated $event): void
    {
        $order = $event->order;
        $template = $this->templates[$event->newStatus] ?? null;

        // Send email if template exists
        if ($template) {
            SendEmailNotification::dispatch(
                $order->user->email,
                $order->user->name,
                $template,
                [
                    'customer_name' => $order->user->name,
                    'order_number' => $order->order_number,
                    'tracking_number' => $order->items->first()?->tracking_number ?? '',
                    'tracking_url' => url('/track/' . $order->order_number),
                ],
                $order->user_id
            );
        }

        // Always send push notification
        $messages = [
            'paid' => 'Payment confirmed! Your order is being prepared.',
            'processing' => 'The vendor is preparing your order.',
            'shipped' => 'Your order has been shipped! Track it now.',
            'delivered' => 'Your order has been delivered. Enjoy!',
            'cancelled' => 'Your order has been cancelled.',
            'refunded' => 'Your order has been refunded.',
        ];

        SendPushNotification::dispatch(
            $order->user_id,
            'order_status',
            'Order Update - #' . $order->order_number,
            $messages[$event->newStatus] ?? 'Your order status has been updated.',
            '/orders/' . $order->id
        );
    }
}
