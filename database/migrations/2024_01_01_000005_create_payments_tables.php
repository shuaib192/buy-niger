<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Migration: Create Payment Tables
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Payment gateways configuration
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // paystack, flutterwave, stripe
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->json('credentials')->nullable(); // encrypted API keys
            $table->json('webhook_secret')->nullable();
            $table->boolean('supports_split')->default(false);
            $table->boolean('is_active')->default(false);
            $table->boolean('is_test_mode')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Payment transactions
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('payment_gateway_id')->nullable()->constrained()->onDelete('set null');
            $table->string('gateway_reference')->nullable();
            $table->decimal('amount', 15, 2);
            $table->decimal('gateway_fee', 15, 2)->default(0);
            $table->string('currency')->default('NGN');
            $table->enum('type', ['payment', 'refund', 'wallet_credit', 'wallet_debit', 'payout'])->default('payment');
            $table->enum('status', ['pending', 'success', 'failed', 'cancelled'])->default('pending');
            $table->json('gateway_response')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['reference']);
        });

        // Customer wallets
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('balance', 15, 2)->default(0);
            $table->decimal('pending_balance', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['user_id']);
        });

        // Wallet transactions
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('reference')->unique();
            $table->enum('type', ['credit', 'debit'])->default('credit');
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_before', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->string('source'); // payment, refund, order, transfer, bonus
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['wallet_id', 'created_at']);
        });

        // Vendor split payment tracking
        Schema::create('vendor_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_item_id')->constrained()->onDelete('cascade');
            $table->decimal('order_amount', 15, 2);
            $table->decimal('commission_rate', 5, 2);
            $table->decimal('platform_commission', 15, 2);
            $table->decimal('vendor_amount', 15, 2);
            $table->enum('status', ['pending', 'available', 'paid'])->default('pending');
            $table->timestamp('available_at')->nullable();
            $table->foreignId('payout_id')->nullable();
            $table->timestamps();

            $table->index(['vendor_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_commissions');
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('wallets');
        Schema::dropIfExists('payment_transactions');
        Schema::dropIfExists('payment_gateways');
    }
};
