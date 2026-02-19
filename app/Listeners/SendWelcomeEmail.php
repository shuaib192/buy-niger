<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Listener: SendWelcomeEmail
 */

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Jobs\SendEmailNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendWelcomeEmail implements ShouldQueue
{
    public function handle(UserRegistered $event): void
    {
        try {
            SendEmailNotification::dispatch(
                $event->user->email,
                $event->user->name,
                'welcome',
                [
                    'customer_name' => $event->user->name,
                    'email' => $event->user->email,
                ],
                $event->user->id
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Welcome email dispatch failed: ' . $e->getMessage());
        }
    }
}
