<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Listener: NotifyAdminNewVendor
 */

namespace App\Listeners;

use App\Events\VendorRegistered;
use App\Jobs\SendPushNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class NotifyAdminNewVendor implements ShouldQueue
{
    public function handle(VendorRegistered $event): void
    {
        // Notify all admins
        $admins = DB::table('users')
            ->whereIn('role_id', [1, 2]) // Super Admin and Admin
            ->where('is_active', true)
            ->get();

        foreach ($admins as $admin) {
            SendPushNotification::dispatch(
                $admin->id,
                'vendor_pending',
                'New Vendor Application',
                "A new vendor '{$event->vendor->store_name}' has applied and is awaiting approval.",
                '/admin/vendors/pending'
            );
        }
    }
}
