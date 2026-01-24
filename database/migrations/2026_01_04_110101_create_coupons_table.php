<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Migration: Create Coupons Table
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('coupons')) {
            Schema::create('coupons', function (Blueprint $table) {
                $table->id();
                $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
                $table->string('code')->unique();
                $table->string('type'); // fixed, percent
                $table->decimal('value', 10, 2);
                $table->decimal('min_spend', 10, 2)->nullable();
                $table->date('expires_at')->nullable();
                $table->integer('usage_limit')->nullable();
                $table->integer('used_count')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
