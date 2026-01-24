<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Migration: AI Simulation, Permissions, and Enhanced Safety Tables
 * CRITICAL: These tables enable AI Shadow Mode and Permission Matrix
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // AI Simulations (Shadow Mode) - All AI actions go here first
        Schema::create('ai_simulations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('ai_role'); // COO, CMO, CRO, CFO, SUPPLY_CHAIN
            $table->string('proposed_action');
            $table->text('action_description');
            $table->json('action_parameters')->nullable();
            $table->json('impact_estimate')->nullable(); // Estimated financial/operational impact
            $table->enum('risk_level', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->boolean('auto_executable')->default(false);
            $table->boolean('approved')->default(false);
            $table->boolean('executed')->default(false);
            $table->boolean('rolled_back')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('executed_at')->nullable();
            $table->timestamp('rolled_back_at')->nullable();
            $table->text('rollback_reason')->nullable();
            $table->json('execution_result')->nullable();
            $table->timestamps();

            $table->index(['vendor_id', 'ai_role', 'executed']);
            $table->index(['risk_level', 'approved']);
        });

        // AI Permissions Matrix - Hard limits per action
        Schema::create('ai_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('ai_role'); // COO, CMO, CRO, CFO, SUPPLY_CHAIN
            $table->string('action'); // refund, price_change, pause_product, etc.
            $table->string('resource')->nullable(); // product, order, vendor, etc.
            $table->decimal('max_value', 15, 2)->nullable(); // Max monetary value
            $table->decimal('max_percentage', 5, 2)->nullable(); // Max percentage change
            $table->boolean('requires_human_approval')->default(true);
            $table->boolean('is_enabled')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['ai_role', 'action', 'resource']);
        });

        // AI Action Limits - Daily/Monthly throttling
        Schema::create('ai_action_limits', function (Blueprint $table) {
            $table->id();
            $table->string('action');
            $table->foreignId('vendor_id')->nullable()->constrained()->onDelete('cascade'); // null = global
            $table->integer('hourly_limit')->nullable();
            $table->integer('daily_limit')->nullable();
            $table->integer('monthly_limit')->nullable();
            $table->integer('current_hourly_count')->default(0);
            $table->integer('current_daily_count')->default(0);
            $table->integer('current_monthly_count')->default(0);
            $table->timestamp('hourly_reset_at')->nullable();
            $table->timestamp('daily_reset_at')->nullable();
            $table->timestamp('monthly_reset_at')->nullable();
            $table->timestamps();

            $table->unique(['action', 'vendor_id']);
        });

        // AI Decision Liability Logs - Legal trail
        Schema::create('ai_liability_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_simulation_id')->nullable()->constrained('ai_simulations')->onDelete('set null');
            $table->foreignId('ai_action_id')->nullable()->constrained('ai_actions')->onDelete('set null');
            $table->string('affected_entity_type'); // user, vendor, order, product
            $table->unsignedBigInteger('affected_entity_id');
            $table->foreignId('affected_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('consent_status', ['granted', 'pending', 'denied', 'not_required'])->default('not_required');
            $table->text('legal_context')->nullable();
            $table->text('disclosure_text')->nullable();
            $table->timestamp('consent_given_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['affected_entity_type', 'affected_entity_id']);
        });

        // System Modes - Controls AI behavior globally
        Schema::create('system_modes', function (Blueprint $table) {
            $table->id();
            $table->string('mode_type'); // ai_mode, maintenance_mode, etc.
            $table->string('mode_value'); // live, shadow, simulation_only, off
            $table->text('description')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('changed_at')->nullable();
            $table->timestamps();

            $table->unique('mode_type');
        });

        // System Health Metrics - Observability
        Schema::create('system_health_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('metric_type'); // queue_health, ai_latency, payment_failure, etc.
            $table->string('metric_name');
            $table->decimal('value', 15, 4);
            $table->string('unit')->nullable(); // ms, count, percentage
            $table->enum('status', ['normal', 'warning', 'critical'])->default('normal');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['metric_type', 'created_at']);
        });

        // Failed Jobs Dashboard Data
        Schema::create('job_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('job_class');
            $table->string('queue')->default('default');
            $table->integer('processed_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->integer('pending_count')->default(0);
            $table->decimal('avg_processing_time', 10, 2)->default(0); // ms
            $table->timestamp('last_processed_at')->nullable();
            $table->timestamp('last_failed_at')->nullable();
            $table->date('metric_date');
            $table->timestamps();

            $table->unique(['job_class', 'queue', 'metric_date']);
        });

        // Rollback Records - For system-level rollbacks
        Schema::create('rollback_records', function (Blueprint $table) {
            $table->id();
            $table->string('rollback_type'); // ai_action, order, payment, etc.
            $table->string('entity_type');
            $table->unsignedBigInteger('entity_id');
            $table->json('original_state');
            $table->json('changed_state');
            $table->json('rollback_state')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->text('reason')->nullable();
            $table->foreignId('initiated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
        });

        // Search Index Queue - For search engine sync
        Schema::create('search_index_queue', function (Blueprint $table) {
            $table->id();
            $table->string('indexable_type'); // product, vendor, category
            $table->unsignedBigInteger('indexable_id');
            $table->enum('action', ['index', 'update', 'delete'])->default('index');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->integer('attempts')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });

        // Temp Files Tracking - For cleanup jobs
        Schema::create('temp_files', function (Blueprint $table) {
            $table->id();
            $table->string('file_path');
            $table->string('file_type'); // image, document, ai_processing
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('purpose')->nullable();
            $table->boolean('processed')->default(false);
            $table->boolean('moved_to_permanent')->default(false);
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index(['expires_at', 'processed']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('temp_files');
        Schema::dropIfExists('search_index_queue');
        Schema::dropIfExists('rollback_records');
        Schema::dropIfExists('job_metrics');
        Schema::dropIfExists('system_health_metrics');
        Schema::dropIfExists('system_modes');
        Schema::dropIfExists('ai_liability_logs');
        Schema::dropIfExists('ai_action_limits');
        Schema::dropIfExists('ai_permissions');
        Schema::dropIfExists('ai_simulations');
    }
};
