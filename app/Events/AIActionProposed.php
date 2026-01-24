<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Event: AIActionProposed
 */

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AIActionProposed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $simulationId,
        public string $aiRole,
        public string $action,
        public array $parameters,
        public string $riskLevel,
        public bool $autoExecutable
    ) {}
}
