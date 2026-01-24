<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Listener: ProcessPayment
 */

namespace App\Listeners;

use App\Events\PaymentCompleted;
use App\Jobs\SendEmailNotification;
use App\Jobs\SendPushNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class ProcessPayment implements ShouldQueue
{
    public function handle(PaymentCompleted $event): void
    {
        $order = $event->order;

        // Update order status
        $order->update([
            'status' => 'paid',
            'payment_status' => 'paid',
            'payment_method' => $event->paymentMethod,
            'payment_reference' => $event->transactionReference,
            'paid_at' => now(),
        ]);

        // Log status change
        DB::table('order_status_history')->insert([
            'order_id' => $order->id,
            'status' => 'paid',
            'notes' => "Payment received via {$event->paymentMethod}",
            'changed_by' => 'system',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Notify customer
        SendPushNotification::dispatch(
            $order->user_id,
            'payment_success',
            'Payment Successful',
            "Your payment of ₦" . number_format($event->amount, 2) . " for order #{$order->order_number} was successful.",
            '/orders/' . $order->id
        );

        // Calculate and record vendor commissions
        foreach ($order->items as $item) {
            $vendor = DB::table('vendors')->find($item->vendor_id);
            $commissionRate = $vendor->commission_rate ?? 10;
            $platformCommission = $item->subtotal * ($commissionRate / 100);
            $vendorAmount = $item->subtotal - $platformCommission;

            DB::table('vendor_commissions')->insert([
                'vendor_id' => $item->vendor_id,
                'order_id' => $order->id,
                'order_item_id' => $item->id,
                'order_amount' => $item->subtotal,
                'commission_rate' => $commissionRate,
                'platform_commission' => $platformCommission,
                'vendor_amount' => $vendorAmount,
                'status' => 'pending',
                'available_at' => now()->addDays(7), // 7 day hold period
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update item with commission info
            DB::table('order_items')
                ->where('id', $item->id)
                ->update([
                    'vendor_commission' => $vendorAmount,
                    'platform_commission' => $platformCommission,
                ]);
        }
    }
}
