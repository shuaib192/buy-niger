<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Migration: Add SEO and Ordering
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('meta_title')->nullable()->after('status');
            $table->text('meta_description')->nullable()->after('meta_title');
        });

        Schema::table('product_images', function (Blueprint $table) {
            $table->integer('display_order')->default(0)->after('is_primary');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description']);
        });

        Schema::table('product_images', function (Blueprint $table) {
            $table->dropColumn('display_order');
        });
    }
};
