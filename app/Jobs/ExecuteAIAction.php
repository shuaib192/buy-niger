<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Job: ExecuteAIAction
 * Executes approved AI actions from simulation
 */

namespace App\Jobs;

use App\Events\AIActionExecuted;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExecuteAIAction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1; // No retries for AI actions - safety first

    public function __construct(
        public int $simulationId
    ) {
        $this->onQueue('ai');
    }

    public function handle(): void
    {
        $simulation = DB::table('ai_simulations')->find($this->simulationId);

        if (!$simulation) {
            Log::warning("AI simulation not found: {$this->simulationId}");
            return;
        }

        if ($simulation->executed) {
            Log::info("AI simulation already executed: {$this->simulationId}");
            return;
        }

        // Double-check AI mode before execution
        $aiMode = DB::table('system_modes')
            ->where('mode_type', 'ai_mode')
            ->value('mode_value');

        if ($aiMode !== 'live') {
            Log::info("AI not in live mode. Skipping execution for simulation: {$this->simulationId}");
            return;
        }

        // Check kill switch
        $killSwitch = DB::table('ai_emergency_status')
            ->where('kill_switch_enabled', true)
            ->exists();

        if ($killSwitch) {
            Log::warning("AI kill switch is enabled. Blocking execution.");
            return;
        }

        try {
            // Store original state for rollback
            $originalState = $this->captureState($simulation);

            // Execute the action
            $result = $this->executeAction($simulation);

            // Log the action
            $actionId = DB::table('ai_actions')->insertGetId([
                'vendor_id' => $simulation->vendor_id,
                'ai_provider_id' => null,
                'action_type' => $simulation->proposed_action,
                'module' => $simulation->ai_role,
                'description' => $simulation->action_description,
                'input_data' => $simulation->action_parameters,
                'output_data' => json_encode($result),
                'status' => $result['success'] ? 'executed' : 'failed',
                'reasoning' => $simulation->action_description,
                'was_auto_executed' => $simulation->auto_executable,
                'requires_approval' => !$simulation->auto_executable,
                'approved_by' => $simulation->approved_by,
                'approved_at' => $simulation->approved_at,
                'executed_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update simulation
            DB::table('ai_simulations')
                ->where('id', $this->simulationId)
                ->update([
                    'executed' => true,
                    'executed_at' => now(),
                    'execution_result' => json_encode($result),
                    'updated_at' => now(),
                ]);

            // Create rollback record
            DB::table('rollback_records')->insert([
                'rollback_type' => 'ai_action',
                'entity_type' => $simulation->proposed_action,
                'entity_id' => $actionId,
                'original_state' => json_encode($originalState),
                'changed_state' => json_encode($result),
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Increment rate limit counter
            $this->incrementRateLimit($simulation->proposed_action, $simulation->vendor_id);

            // Fire event
            event(new AIActionExecuted(
                $this->simulationId,
                $actionId,
                $simulation->ai_role,
                $simulation->proposed_action,
                $result,
                $result['success']
            ));

            Log::info("AI action executed: {$simulation->proposed_action} (simulation: {$this->simulationId})");

        } catch (\Exception $e) {
            Log::error("AI action execution failed: " . $e->getMessage());

            DB::table('ai_simulations')
                ->where('id', $this->simulationId)
                ->update([
                    'execution_result' => json_encode(['error' => $e->getMessage()]),
                    'updated_at' => now(),
                ]);

            throw $e;
        }
    }

    protected function captureState($simulation): array
    {
        $params = json_decode($simulation->action_parameters, true) ?? [];
        
        // Capture state based on action type
        return match ($simulation->proposed_action) {
            'price_change' => $this->captureProductState($params['product_id'] ?? null),
            'pause_product' => $this->captureProductState($params['product_id'] ?? null),
            'refund' => $this->captureOrderState($params['order_id'] ?? null),
            default => [],
        };
    }

    protected function captureProductState(?int $productId): array
    {
        if (!$productId) return [];
        
        $product = DB::table('products')->find($productId);
        return $product ? (array) $product : [];
    }

    protected function captureOrderState(?int $orderId): array
    {
        if (!$orderId) return [];
        
        $order = DB::table('orders')->find($orderId);
        return $order ? (array) $order : [];
    }

    protected function executeAction($simulation): array
    {
        $params = json_decode($simulation->action_parameters, true) ?? [];

        return match ($simulation->proposed_action) {
            'price_change' => $this->executePriceChange($params),
            'pause_product' => $this->executePauseProduct($params),
            'send_notification' => $this->executeSendNotification($params),
            'create_promotion' => $this->executeCreatePromotion($params),
            default => ['success' => false, 'error' => 'Unknown action'],
        };
    }

    protected function executePriceChange(array $params): array
    {
        $productId = $params['product_id'] ?? null;
        $newPrice = $params['new_price'] ?? null;

        if (!$productId || !$newPrice) {
            return ['success' => false, 'error' => 'Missing parameters'];
        }

        $oldPrice = DB::table('products')->where('id', $productId)->value('price');

        DB::table('products')
            ->where('id', $productId)
            ->update(['price' => $newPrice, 'updated_at' => now()]);

        // Record price history
        DB::table('price_history')->insert([
            'product_id' => $productId,
            'old_price' => $oldPrice,
            'new_price' => $newPrice,
            'changed_by' => 'AI',
            'reason' => $params['reason'] ?? 'AI price optimization',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return ['success' => true, 'old_price' => $oldPrice, 'new_price' => $newPrice];
    }

    protected function executePauseProduct(array $params): array
    {
        $productId = $params['product_id'] ?? null;

        if (!$productId) {
            return ['success' => false, 'error' => 'Missing product_id'];
        }

        DB::table('products')
            ->where('id', $productId)
            ->update(['status' => 'inactive', 'updated_at' => now()]);

        return ['success' => true, 'product_id' => $productId];
    }

    protected function executeSendNotification(array $params): array
    {
        $userId = $params['user_id'] ?? null;
        $message = $params['message'] ?? null;

        if (!$userId || !$message) {
            return ['success' => false, 'error' => 'Missing parameters'];
        }

        SendPushNotification::dispatch(
            $userId,
            'ai_notification',
            $params['title'] ?? 'BuyNiger AI',
            $message
        );

        return ['success' => true];
    }

    protected function executeCreatePromotion(array $params): array
    {
        // Create coupon
        $code = $params['code'] ?? 'AI' . strtoupper(substr(uniqid(), -6));
        
        DB::table('coupons')->insert([
            'vendor_id' => $params['vendor_id'] ?? null,
            'code' => $code,
            'name' => $params['name'] ?? 'AI Generated Promotion',
            'type' => $params['type'] ?? 'percentage',
            'value' => $params['value'] ?? 10,
            'is_active' => true,
            'starts_at' => now(),
            'expires_at' => now()->addDays($params['days'] ?? 7),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return ['success' => true, 'code' => $code];
    }

    protected function incrementRateLimit(string $action, ?int $vendorId): void
    {
        DB::table('ai_action_limits')
            ->where('action', $action)
            ->where(function ($q) use ($vendorId) {
                $q->whereNull('vendor_id')
                  ->orWhere('vendor_id', $vendorId);
            })
            ->increment('current_daily_count');
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("AI action execution job failed: " . $exception->getMessage());
        \App\Services\MetricsService::recordJobFailure(self::class, 'ai');
    }
}
