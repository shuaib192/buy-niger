<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Listener: HandleAIProposal
 */

namespace App\Listeners;

use App\Events\AIActionProposed;
use App\Jobs\SendPushNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class HandleAIProposal implements ShouldQueue
{
    public function handle(AIActionProposed $event): void
    {
        // If high risk or requires approval, notify admins
        if (in_array($event->riskLevel, ['high', 'critical']) || !$event->autoExecutable) {
            $admins = DB::table('users')
                ->whereIn('role_id', [1, 2])
                ->where('is_active', true)
                ->get();

            foreach ($admins as $admin) {
                SendPushNotification::dispatch(
                    $admin->id,
                    'ai_proposal',
                    "AI {$event->aiRole} Proposal",
                    "AI proposes: {$event->action} [Risk: {$event->riskLevel}]",
                    '/admin/ai/simulations/' . $event->simulationId
                );
            }
        }

        // Log liability if action affects money or users
        if (in_array($event->action, ['refund', 'price_change', 'suspend_vendor', 'cancel_order'])) {
            DB::table('ai_liability_logs')->insert([
                'ai_simulation_id' => $event->simulationId,
                'affected_entity_type' => $this->getEntityType($event->action),
                'affected_entity_id' => $event->parameters['entity_id'] ?? 0,
                'consent_status' => 'pending',
                'legal_context' => "AI {$event->aiRole} proposed {$event->action}",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    protected function getEntityType(string $action): string
    {
        return match ($action) {
            'refund', 'cancel_order' => 'order',
            'price_change', 'pause_product' => 'product',
            'suspend_vendor' => 'vendor',
            default => 'unknown',
        };
    }
}
