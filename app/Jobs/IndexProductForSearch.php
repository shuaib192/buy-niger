<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Job: IndexProductForSearch
 * Handles search indexing in queue (async)
 */

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IndexProductForSearch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public function __construct(
        public int $productId,
        public string $action = 'index' // index, update, delete
    ) {
        $this->onQueue('search');
    }

    public function handle(): void
    {
        try {
            // Update queue status
            DB::table('search_index_queue')
                ->where('indexable_type', 'product')
                ->where('indexable_id', $this->productId)
                ->where('status', 'pending')
                ->update(['status' => 'processing', 'updated_at' => now()]);

            // Get search service (Meilisearch or custom implementation)
            $searchService = app(\App\Services\SearchService::class);

            if ($this->action === 'delete') {
                $searchService->deleteProduct($this->productId);
            } else {
                $product = Product::with(['vendor', 'category', 'images', 'tags'])->find($this->productId);
                
                if ($product) {
                    $searchService->indexProduct($product);
                }
            }

            // Mark as completed
            DB::table('search_index_queue')
                ->where('indexable_type', 'product')
                ->where('indexable_id', $this->productId)
                ->update(['status' => 'completed', 'updated_at' => now()]);

            Log::info("Product indexed for search: {$this->productId} ({$this->action})");

        } catch (\Exception $e) {
            DB::table('search_index_queue')
                ->where('indexable_type', 'product')
                ->where('indexable_id', $this->productId)
                ->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'attempts' => DB::raw('attempts + 1'),
                    'updated_at' => now(),
                ]);

            Log::error("Product indexing failed: " . $e->getMessage());
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Search indexing job failed: " . $exception->getMessage());
        \App\Services\MetricsService::recordJobFailure(self::class, 'search');
    }
}
