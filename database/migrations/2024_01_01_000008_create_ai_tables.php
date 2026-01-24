<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Migration: Create AI System Tables
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // AI providers configuration
        Schema::create('ai_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // grok, openai, gemini
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->json('credentials')->nullable(); // Encrypted API keys
            $table->string('base_url')->nullable();
            $table->string('model')->nullable(); // gpt-4, gemini-pro, etc.
            $table->boolean('is_active')->default(false);
            $table->boolean('is_default')->default(false);
            $table->json('capabilities')->nullable(); // chat, vision, embeddings
            $table->decimal('cost_per_1k_tokens', 10, 6)->default(0);
            $table->integer('rate_limit_per_minute')->default(60);
            $table->integer('priority')->default(0); // For failover
            $table->timestamps();
        });

        // AI actions log (audit trail)
        Schema::create('ai_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('ai_provider_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action_type'); // price_change, promotion_create, email_sent, etc.
            $table->string('module'); // coo, cmo, cro, cfo, supply_chain
            $table->text('description');
            $table->json('input_data')->nullable();
            $table->json('output_data')->nullable();
            $table->enum('status', ['pending', 'executed', 'rolled_back', 'failed'])->default('pending');
            $table->text('reasoning')->nullable(); // AI explanation
            $table->boolean('was_auto_executed')->default(false);
            $table->boolean('requires_approval')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('executed_at')->nullable();
            $table->timestamp('rolled_back_at')->nullable();
            $table->text('rollback_reason')->nullable();
            $table->integer('tokens_used')->default(0);
            $table->decimal('cost', 10, 6)->default(0);
            $table->timestamps();

            $table->index(['vendor_id', 'action_type']);
            $table->index(['module', 'created_at']);
        });

        // AI policies (rules and limits)
        Schema::create('ai_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->nullable()->constrained()->onDelete('cascade'); // null = global
            $table->string('policy_type'); // price_limit, auto_response, promotion
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('rules'); // The actual policy rules
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0);
            $table->timestamps();
        });

        // AI memory (persistent knowledge)
        Schema::create('ai_memory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('memory_type'); // customer_preference, product_insight, behavior_pattern
            $table->string('entity_type')->nullable(); // user, product, order
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('key');
            $table->longText('value');
            $table->json('metadata')->nullable();
            $table->decimal('confidence', 5, 2)->default(1.00);
            $table->integer('usage_count')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['vendor_id', 'memory_type']);
            $table->index(['entity_type', 'entity_id']);
        });

        // AI chat sessions
        Schema::create('ai_chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('vendor_id')->nullable()->constrained()->onDelete('set null');
            $table->string('session_type'); // customer_support, vendor_assistant
            $table->enum('status', ['active', 'closed'])->default('active');
            $table->json('context')->nullable();
            $table->integer('message_count')->default(0);
            $table->timestamps();
        });

        // AI chat messages
        Schema::create('ai_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('ai_chat_sessions')->onDelete('cascade');
            $table->enum('role', ['user', 'assistant', 'system']);
            $table->longText('content');
            $table->json('metadata')->nullable();
            $table->integer('tokens')->default(0);
            $table->timestamps();
        });

        // Emergency kill switch status
        Schema::create('ai_emergency_status', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(true);
            $table->boolean('kill_switch_enabled')->default(false);
            $table->text('kill_switch_reason')->nullable();
            $table->foreignId('triggered_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('triggered_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_emergency_status');
        Schema::dropIfExists('ai_chat_messages');
        Schema::dropIfExists('ai_chat_sessions');
        Schema::dropIfExists('ai_memory');
        Schema::dropIfExists('ai_policies');
        Schema::dropIfExists('ai_actions');
        Schema::dropIfExists('ai_providers');
    }
};
