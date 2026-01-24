<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Event: CampaignLaunched
 */

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CampaignLaunched
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $campaignId,
        public string $campaignName,
        public int $targetRecipients
    ) {}
}
