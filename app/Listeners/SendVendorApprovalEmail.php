<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Listener: SendVendorApprovalEmail
 */

namespace App\Listeners;

use App\Events\VendorApproved;
use App\Jobs\SendEmailNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendVendorApprovalEmail implements ShouldQueue
{
    public function handle(VendorApproved $event): void
    {
        SendEmailNotification::dispatch(
            $event->vendor->user->email,
            $event->vendor->user->name,
            'vendor_approved',
            [
                'vendor_name' => $event->vendor->user->name,
                'store_name' => $event->vendor->store_name,
            ],
            $event->vendor->user_id
        );
    }
}
