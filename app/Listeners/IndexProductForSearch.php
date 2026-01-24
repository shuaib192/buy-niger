<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Listener: IndexProductForSearch
 */

namespace App\Listeners;

use App\Events\ProductCreated;
use App\Jobs\IndexProductForSearch as IndexJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class IndexProductForSearch implements ShouldQueue
{
    public function handle(ProductCreated $event): void
    {
        // Add to search queue
        DB::table('search_index_queue')->insert([
            'indexable_type' => 'product',
            'indexable_id' => $event->product->id,
            'action' => 'index',
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Dispatch indexing job
        IndexJob::dispatch($event->product->id, 'index');
    }
}
