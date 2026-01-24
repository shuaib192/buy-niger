<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Listener: NotifyVendorLowStock
 */

namespace App\Listeners;

use App\Events\InventoryLow;
use App\Jobs\SendPushNotification;
use App\Jobs\ProcessAIAnalysis;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyVendorLowStock implements ShouldQueue
{
    public function handle(InventoryLow $event): void
    {
        // Notify vendor
        SendPushNotification::dispatch(
            $event->product->vendor->user_id,
            'inventory_low',
            'Low Stock Alert',
            "Your product '{$event->product->name}' has only {$event->currentQuantity} units left (threshold: {$event->threshold}).",
            '/vendor/products/' . $event->product->id
        );

        // Trigger AI Supply Chain analysis
        ProcessAIAnalysis::dispatch(
            'SUPPLY_CHAIN',
            'restock_recommendation',
            [
                'product_id' => $event->product->id,
                'current_quantity' => $event->currentQuantity,
                'threshold' => $event->threshold,
                'order_velocity' => $event->product->order_count,
            ],
            $event->product->vendor_id
        );
    }
}
