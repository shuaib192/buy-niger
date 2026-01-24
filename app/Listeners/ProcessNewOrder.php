<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Listener: ProcessNewOrder
 * CRITICAL: This is the main order processing listener
 */

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Jobs\SendEmailNotification;
use App\Jobs\SendPushNotification;
use App\Jobs\ProcessAIAnalysis;
use App\Jobs\AggregateAnalytics;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class ProcessNewOrder implements ShouldQueue
{
    public function handle(OrderPlaced $event): void
    {
        $order = $event->order;

        // 1. Send order confirmation email to customer
        SendEmailNotification::dispatch(
            $order->user->email,
            $order->user->name,
            'order_confirmation',
            [
                'customer_name' => $order->user->name,
                'order_number' => $order->order_number,
                'order_total' => '₦' . number_format($order->total, 2),
                'order_items' => $this->formatOrderItems($order),
            ],
            $order->user_id
        );

        // 2. Notify each vendor of their portion of the order
        $vendorIds = $order->items->pluck('vendor_id')->unique();
        foreach ($vendorIds as $vendorId) {
            $vendor = DB::table('vendors')->find($vendorId);
            if ($vendor) {
                $vendorItems = $order->items->where('vendor_id', $vendorId);
                $vendorTotal = $vendorItems->sum('subtotal');

                SendPushNotification::dispatch(
                    $vendor->user_id,
                    'new_order',
                    'New Order Received!',
                    "You have a new order #{$order->order_number} worth ₦" . number_format($vendorTotal, 2),
                    '/vendor/orders/' . $order->id
                );
            }
        }

        // 3. Record analytics event
        DB::table('analytics_events')->insert([
            'user_id' => $order->user_id,
            'event_type' => 'purchase',
            'event_name' => 'order_placed',
            'event_data' => json_encode([
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'total' => $order->total,
                'items_count' => $order->items->count(),
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Trigger AI analysis for the order
        ProcessAIAnalysis::dispatch(
            'COO',
            'order_analysis',
            [
                'order_id' => $order->id,
                'customer_id' => $order->user_id,
                'total' => $order->total,
                'items' => $order->items->toArray(),
            ]
        );

        // 5. Queue analytics aggregation
        AggregateAnalytics::dispatch(now()->toDateString());
    }

    protected function formatOrderItems($order): string
    {
        $items = [];
        foreach ($order->items as $item) {
            $items[] = "{$item->product_name} x {$item->quantity} - ₦" . number_format($item->subtotal, 2);
        }
        return implode('<br>', $items);
    }
}
