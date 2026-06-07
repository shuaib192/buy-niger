<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Helper: check if an index exists on a table
        $hasIndex = function (string $table, string $indexName): bool {
            try {
                $indexes = Schema::getIndexes($table);
                foreach ($indexes as $index) {
                    if (($index['name'] ?? '') === $indexName) {
                        return true;
                    }
                }
            } catch (\Throwable $e) {
                // If we can't retrieve indexes, assume it doesn't exist
            }

            return false;
        };

        Schema::table('products', function (Blueprint $table) use ($hasIndex) {
            if (! $hasIndex('products', 'products_status_index')) {
                $table->index('status');
            }
            if (! $hasIndex('products', 'products_is_featured_index')) {
                $table->index('is_featured');
            }
        });

        Schema::table('order_items', function (Blueprint $table) use ($hasIndex) {
            if (! $hasIndex('order_items', 'order_items_created_at_index')) {
                $table->index('created_at');
            }
            if (! $hasIndex('order_items', 'order_items_order_id_index')) {
                $table->index('order_id');
            }
            if (! $hasIndex('order_items', 'order_items_vendor_id_index')) {
                $table->index('vendor_id');
            }
        });

        Schema::table('vendors', function (Blueprint $table) use ($hasIndex) {
            if (! $hasIndex('vendors', 'vendors_status_index')) {
                $table->index('status');
            }
        });

        Schema::table('notifications', function (Blueprint $table) use ($hasIndex) {
            if (! $hasIndex('notifications', 'notifications_user_id_read_at_index')) {
                $table->index(['user_id', 'read_at']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndexIfExists('products_status_index');
            $table->dropIndexIfExists('products_is_featured_index');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndexIfExists('order_items_created_at_index');
            $table->dropIndexIfExists('order_items_order_id_index');
            $table->dropIndexIfExists('order_items_vendor_id_index');
        });

        Schema::table('vendors', function (Blueprint $table) {
            $table->dropIndexIfExists('vendors_status_index');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndexIfExists('notifications_user_id_read_at_index');
        });
    }
};
