<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * EventServiceProvider - Maps Events to Listeners
 * CRITICAL: Event-driven architecture configuration
 */

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

// Events
use App\Events\UserRegistered;
use App\Events\VendorRegistered;
use App\Events\VendorApproved;
use App\Events\ProductCreated;
use App\Events\InventoryLow;
use App\Events\OrderPlaced;
use App\Events\PaymentCompleted;
use App\Events\OrderStatusUpdated;
use App\Events\RefundRequested;
use App\Events\AIActionProposed;
use App\Events\AIActionExecuted;
use App\Events\CampaignLaunched;

// Listeners
use App\Listeners\SendWelcomeEmail;
use App\Listeners\NotifyAdminNewVendor;
use App\Listeners\SendVendorApprovalEmail;
use App\Listeners\IndexProductForSearch;
use App\Listeners\NotifyVendorLowStock;
use App\Listeners\ProcessNewOrder;
use App\Listeners\ProcessPayment;
use App\Listeners\SendOrderStatusEmail;
use App\Listeners\ProcessRefundRequest;
use App\Listeners\HandleAIProposal;
use App\Listeners\LogAIExecution;
use App\Listeners\SendCampaignEmails;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        // User Events
        UserRegistered::class => [
            SendWelcomeEmail::class,
        ],

        // Vendor Events
        VendorRegistered::class => [
            NotifyAdminNewVendor::class,
        ],

        VendorApproved::class => [
            SendVendorApprovalEmail::class,
        ],

        // Product Events
        ProductCreated::class => [
            IndexProductForSearch::class,
        ],

        InventoryLow::class => [
            NotifyVendorLowStock::class,
        ],

        // Order Events
        OrderPlaced::class => [
            ProcessNewOrder::class,
        ],

        PaymentCompleted::class => [
            ProcessPayment::class,
        ],

        OrderStatusUpdated::class => [
            SendOrderStatusEmail::class,
        ],

        RefundRequested::class => [
            ProcessRefundRequest::class,
        ],

        // AI Events
        AIActionProposed::class => [
            HandleAIProposal::class,
        ],

        AIActionExecuted::class => [
            LogAIExecution::class,
        ],

        // Campaign Events
        CampaignLaunched::class => [
            SendCampaignEmails::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
