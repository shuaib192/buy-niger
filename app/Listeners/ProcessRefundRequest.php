<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Listener: ProcessRefundRequest
 */

namespace App\Listeners;

use App\Events\RefundRequested;
use App\Jobs\SendPushNotification;
use App\Jobs\ProcessAIAnalysis;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class ProcessRefundRequest implements ShouldQueue
{
    public function handle(RefundRequested $event): void
    {
        $order = $event->order;

        // Notify admins
        $admins = DB::table('users')
            ->whereIn('role_id', [1, 2])
            ->where('is_active', true)
            ->get();

        foreach ($admins as $admin) {
            SendPushNotification::dispatch(
                $admin->id,
                'refund_request',
                'New Refund Request',
                "Refund request for order #{$order->order_number} - ₦" . number_format($event->amount, 2),
                '/admin/refunds'
            );
        }

        // Trigger AI CRO analysis for auto-processing (if within limits)
        ProcessAIAnalysis::dispatch(
            'CRO',
            'refund_evaluation',
            [
                'order_id' => $order->id,
                'amount' => $event->amount,
                'reason' => $event->reason,
                'customer_id' => $order->user_id,
                'order_date' => $order->created_at->toDateString(),
            ]
        );
    }
}
