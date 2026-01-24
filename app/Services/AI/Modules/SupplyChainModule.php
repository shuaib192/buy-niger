<?php

namespace App\Services\AI\Modules;

use App\Services\AI\AIService;
use App\Models\Vendor;
use App\Models\Product;

class SupplyChainModule
{
    protected $ai;

    public function __construct(AIService $ai)
    {
        $this->ai = $ai;
    }

    /**
     * Predict stock needs.
     */
    public function predictStockNeeds(Vendor $vendor)
    {
        $prompt = "You are the AI Supply Chain Manager.
        Analyze sales velocity for the top 5 products.
        Suggest restocking dates to avoid stockouts.";

        return $this->ai->generateText($prompt, 'SupplyChain', 'stock_prediction');
    }

    /**
     * Optimize delivery routes (Simulated for MVP).
     */
    public function optimizeLogistics(Vendor $vendor)
    {
        $prompt = "We have 50 pending deliveries in Lagos.
        Suggest a zoning strategy to optimize delivery routes and reduce fuel costs.";

        return $this->ai->generateText($prompt, 'SupplyChain', 'logistics_optimization');
    }
}
