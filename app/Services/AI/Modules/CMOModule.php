<?php

namespace App\Services\AI\Modules;

use App\Services\AI\AIService;
use App\Models\Vendor;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CMOModule
{
    protected $ai;

    public function __construct(AIService $ai)
    {
        $this->ai = $ai;
    }

    /**
     * Suggest marketing promotions based on inventory behavior.
     */
    public function suggestPromotions(Vendor $vendor)
    {
        // Find products with high stock but low recent sales (stagnant inventory)
        // MVP: Just grab products with high stock for now
        $stagnantProducts = Product::where('vendor_id', $vendor->id)
                                   ->where('stock_quantity', '>', 50)
                                   ->take(5)
                                   ->get();
        
        $productList = $stagnantProducts->map(fn($p) => "- {$p->name} (Stock: {$p->stock_quantity}, Price: ₦{$p->price})")->implode("\n");

        if (empty($productList)) {
            $productList = "Inventory is moving well. No specific stagnant products identified.";
        }

        $prompt = "You are the AI Chief Marketing Officer (CMO) for '{$vendor->store_name}'.
        We have the following products that are moving slowly or have high stock:
        $productList
        
        Propose 3 creative marketing campaigns or discount strategies to clear this inventory. Include catchy slogans.";

        return $this->ai->generateText($prompt, 'CMO', 'promotion_strategy');
    }

    /**
     * Draft a promotional campaign message (Email/Social).
     */
    public function draftCampaign(Vendor $vendor, $productName, $discountPercentage)
    {
        $prompt = "Write a high-converting promotional email for '{$vendor->store_name}' offering a {$discountPercentage}% discount on '{$productName}'.
        Tone: Exciting, Urgent, Professional.
        Include a subject line.";

        return $this->ai->generateText($prompt, 'CMO', 'campaign_content_generation');
    }

    /**
     * Analyze pricing strategy.
     */
    public function analyzePricing(Vendor $vendor)
    {
        // MVP: General pricing advice based on store category
        $prompt = "As the AI CMO, provide a pricing strategy analysis for a store in the 'General Retail' sector in Nigeria.
        Consider current inflation trends and competitor behavior. Suggest psychological pricing tactics.";

        return $this->ai->generateText($prompt, 'CMO', 'pricing_analysis');
    }
}
