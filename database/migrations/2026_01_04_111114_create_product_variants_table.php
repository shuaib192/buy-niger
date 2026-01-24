<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Migration: Create Product Variants
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('sku')->nullable();
            $table->string('size')->nullable(); // e.g., S, M, L, XL
            $table->string('color')->nullable(); // e.g., Red, Blue, #FF0000
            $table->decimal('price', 10, 2)->nullable(); // Overrides product price if set
            $table->integer('stock_quantity')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
