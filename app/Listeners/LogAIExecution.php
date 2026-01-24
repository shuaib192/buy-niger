<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Listener: LogAIExecution
 */

namespace App\Listeners;

use App\Events\AIActionExecuted;
use App\Services\MetricsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LogAIExecution implements ShouldQueue
{
    public function handle(AIActionExecuted $event): void
    {
        // Log to audit
        DB::table('audit_logs')->insert([
            'user_id' => null, // AI action
            'action' => 'ai_execution',
            'model_type' => 'ai_action',
            'model_id' => $event->actionId,
            'old_values' => null,
            'new_values' => json_encode([
                'simulation_id' => $event->simulationId,
                'ai_role' => $event->aiRole,
                'action' => $event->action,
                'result' => $event->result,
                'successful' => $event->successful,
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update liability log
        DB::table('ai_liability_logs')
            ->where('ai_simulation_id', $event->simulationId)
            ->update([
                'ai_action_id' => $event->actionId,
                'consent_status' => $event->successful ? 'granted' : 'denied',
                'updated_at' => now(),
            ]);

        // Log metrics
        if ($event->successful) {
            Log::info("AI action executed: {$event->action} by {$event->aiRole}");
        } else {
            Log::warning("AI action failed: {$event->action} by {$event->aiRole}");
            MetricsService::recordJobFailure('AIAction:' . $event->action, 'ai');
        }
    }
}
