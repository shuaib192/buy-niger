<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Job: AggregateAnalytics
 * Handles analytics aggregation in queue (async)
 */

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AggregateAnalytics implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $date,
        public ?int $vendorId = null
    ) {
        $this->onQueue('analytics');
    }

    public function handle(): void
    {
        try {
            $query = DB::table('analytics_events')
                ->whereDate('created_at', $this->date);

            // If vendor-specific, filter by vendor products
            if ($this->vendorId) {
                // Get vendor products for filtering
                $productIds = DB::table('products')
                    ->where('vendor_id', $this->vendorId)
                    ->pluck('id');

                $query->where(function ($q) use ($productIds) {
                    $q->whereJsonContains('event_data->vendor_id', $this->vendorId)
                      ->orWhereIn('event_data->product_id', $productIds);
                });
            }

            $events = $query->get();

            // Aggregate metrics
            $pageViews = $events->where('event_type', 'page_view')->count();
            $uniqueVisitors = $events->pluck('user_id')->unique()->count();
            $productViews = $events->where('event_type', 'product_view')->count();
            $addToCarts = $events->where('event_type', 'add_to_cart')->count();

            // Get orders and revenue
            $ordersQuery = DB::table('orders')
                ->whereDate('created_at', $this->date)
                ->where('payment_status', 'paid');

            if ($this->vendorId) {
                $orderIds = DB::table('order_items')
                    ->where('vendor_id', $this->vendorId)
                    ->pluck('order_id');
                $ordersQuery->whereIn('id', $orderIds);
            }

            $ordersCount = $ordersQuery->count();
            $revenue = $ordersQuery->sum('total');

            // Calculate conversion rate
            $conversionRate = $uniqueVisitors > 0 
                ? round(($ordersCount / $uniqueVisitors) * 100, 2) 
                : 0;

            // Upsert daily analytics
            DB::table('analytics_daily')->updateOrInsert(
                [
                    'date' => $this->date,
                    'vendor_id' => $this->vendorId,
                ],
                [
                    'page_views' => $pageViews,
                    'unique_visitors' => $uniqueVisitors,
                    'product_views' => $productViews,
                    'add_to_carts' => $addToCarts,
                    'orders' => $ordersCount,
                    'revenue' => $revenue,
                    'conversion_rate' => $conversionRate,
                    'updated_at' => now(),
                ]
            );

            Log::info("Analytics aggregated for {$this->date}" . ($this->vendorId ? " (vendor: {$this->vendorId})" : ''));

        } catch (\Exception $e) {
            Log::error("Analytics aggregation failed: " . $e->getMessage());
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Analytics aggregation job failed: " . $exception->getMessage());
        \App\Services\MetricsService::recordJobFailure(self::class, 'analytics');
    }
}
