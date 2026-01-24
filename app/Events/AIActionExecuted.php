<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Event: AIActionExecuted
 */

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AIActionExecuted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $simulationId,
        public int $actionId,
        public string $aiRole,
        public string $action,
        public array $result,
        public bool $successful
    ) {}
}
