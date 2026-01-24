<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Job: ProcessAIAnalysis
 * Handles AI analysis and decision-making in queue (async)
 * CRITICAL: All AI decisions go through simulation first (Shadow Mode)
 */

namespace App\Jobs;

use App\Events\AIActionProposed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessAIAnalysis implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $timeout = 120; // AI calls can take time

    public function __construct(
        public string $aiRole, // COO, CMO, CRO, CFO, SUPPLY_CHAIN
        public string $analysisType, // inventory_check, pricing_optimize, customer_sentiment
        public array $context,
        public ?int $vendorId = null
    ) {
        $this->onQueue('ai');
    }

    public function handle(): void
    {
        try {
            // Check if AI is enabled
            $aiMode = $this->getAIMode();
            if ($aiMode === 'off') {
                Log::info("AI is disabled. Skipping analysis.");
                return;
            }

            // Get AI service
            $aiService = app(\App\Services\AI\AIProviderService::class);

            // Run analysis
            $analysis = $aiService->analyze(
                $this->analysisType,
                $this->context,
                $this->aiRole
            );

            if (!$analysis['success']) {
                Log::warning("AI analysis failed: " . ($analysis['error'] ?? 'Unknown error'));
                return;
            }

            // If AI proposes an action, create a simulation record
            if (!empty($analysis['proposed_action'])) {
                $this->createSimulation($analysis);
            }

            Log::info("AI analysis completed for {$this->aiRole}: {$this->analysisType}");

        } catch (\Exception $e) {
            Log::error("AI analysis error: " . $e->getMessage());
            throw $e;
        }
    }

    protected function createSimulation(array $analysis): void
    {
        // Determine risk level based on action type and values
        $riskLevel = $this->assessRiskLevel($analysis);
        
        // Check if action is auto-executable based on permissions
        $autoExecutable = $this->checkAutoExecutable($analysis, $riskLevel);

        // Create simulation record (Shadow Mode)
        $simulationId = DB::table('ai_simulations')->insertGetId([
            'vendor_id' => $this->vendorId,
            'ai_role' => $this->aiRole,
            'proposed_action' => $analysis['proposed_action'],
            'action_description' => $analysis['description'] ?? '',
            'action_parameters' => json_encode($analysis['parameters'] ?? []),
            'impact_estimate' => json_encode($analysis['impact'] ?? []),
            'risk_level' => $riskLevel,
            'auto_executable' => $autoExecutable,
            'approved' => false,
            'executed' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Fire event
        event(new AIActionProposed(
            $simulationId,
            $this->aiRole,
            $analysis['proposed_action'],
            $analysis['parameters'] ?? [],
            $riskLevel,
            $autoExecutable
        ));

        // If low risk and auto-executable, queue for execution
        $aiMode = $this->getAIMode();
        if ($autoExecutable && $riskLevel === 'low' && $aiMode === 'live') {
            ExecuteAIAction::dispatch($simulationId)->onQueue('ai');
        }
    }

    protected function assessRiskLevel(array $analysis): string
    {
        $action = $analysis['proposed_action'] ?? '';
        $params = $analysis['parameters'] ?? [];

        // Critical actions
        if (in_array($action, ['refund', 'cancel_order', 'suspend_vendor', 'delete_product'])) {
            $amount = $params['amount'] ?? 0;
            if ($amount > 50000) return 'critical';
            if ($amount > 10000) return 'high';
            return 'medium';
        }

        // Price changes
        if ($action === 'price_change') {
            $percentChange = abs($params['percentage'] ?? 0);
            if ($percentChange > 20) return 'high';
            if ($percentChange > 10) return 'medium';
            return 'low';
        }

        // Notifications and messages
        if (in_array($action, ['send_notification', 'send_email', 'create_promotion'])) {
            return 'low';
        }

        return 'medium';
    }

    protected function checkAutoExecutable(array $analysis, string $riskLevel): bool
    {
        // Check system mode
        $aiMode = $this->getAIMode();
        if ($aiMode !== 'live') {
            return false;
        }

        // Check global auto-execute setting
        $autoExecuteEnabled = DB::table('system_settings')
            ->where('key', 'ai_auto_execute_enabled')
            ->value('value');

        if (!$autoExecuteEnabled || $autoExecuteEnabled === '0') {
            return false;
        }

        // Check specific permission for this action
        $permission = DB::table('ai_permissions')
            ->where('ai_role', $this->aiRole)
            ->where('action', $analysis['proposed_action'])
            ->where('is_enabled', true)
            ->first();

        if (!$permission) {
            return false;
        }

        // If permission requires human approval, not auto-executable
        if ($permission->requires_human_approval) {
            return false;
        }

        // Check value limits
        $actionValue = $analysis['parameters']['amount'] ?? $analysis['parameters']['value'] ?? 0;
        if ($permission->max_value && $actionValue > $permission->max_value) {
            return false;
        }

        // Check rate limits
        if (!$this->checkRateLimits($analysis['proposed_action'])) {
            return false;
        }

        return $riskLevel === 'low';
    }

    protected function checkRateLimits(string $action): bool
    {
        $limit = DB::table('ai_action_limits')
            ->where('action', $action)
            ->where(function ($q) {
                $q->whereNull('vendor_id')
                  ->orWhere('vendor_id', $this->vendorId);
            })
            ->first();

        if (!$limit) {
            return true; // No limit defined
        }

        // Check daily limit
        if ($limit->daily_limit && $limit->current_daily_count >= $limit->daily_limit) {
            return false;
        }

        return true;
    }

    protected function getAIMode(): string
    {
        $mode = DB::table('system_modes')
            ->where('mode_type', 'ai_mode')
            ->value('mode_value');

        return $mode ?? 'shadow'; // Default to shadow mode for safety
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("AI analysis job failed: " . $exception->getMessage());
        \App\Services\MetricsService::recordJobFailure(self::class, 'ai');
    }
}
