<?php

namespace App\Services\AI\Modules;

use App\Services\AI\AIService;
use App\Models\Vendor;
use App\Models\User;
use App\Models\Dispute;

class CROModule
{
    protected $ai;

    public function __construct(AIService $ai)
    {
        $this->ai = $ai;
    }

    /**
     * Generate an automated response for customer chat.
     */
    public function handleCustomerInquiry($message, Vendor $vendor)
    {
        $prompt = "You are the Customer Support AI for '{$vendor->store_name}'.
        A customer asked: '$message'
        
        Provide a helpful, polite, and professional response. Do not promise refunds without approval. Keep it under 50 words.";

        return $this->ai->generateText($prompt, 'CRO', 'customer_chat_response');
    }

    /**
     * Suggest resolution for a dispute.
     */
    public function suggestDisputeResolution(Dispute $dispute)
    {
        $prompt = "You are the AI CRO handling a dispute.
        Complaint: {$dispute->description}
        Reason: {$dispute->reason}
        
        Suggest a fair resolution for both the customer and the vendor, based on standard e-commerce policies.";

        return $this->ai->generateText($prompt, 'CRO', 'dispute_resolution_suggestion');
    }

    /**
     * Analyze churn risk for a customer.
     */
    public function analyzeChurnRisk(User $user)
    {
        // MVP: Analyze last login or order date
        $lastOrder = $user->orders()->latest()->first();
        $daysSinceLastOrder = $lastOrder ? $lastOrder->created_at->diffInDays(now()) : 999;
        
        $prompt = "Analyze churn risk for customer '{$user->name}'.
        Last order was $daysSinceLastOrder days ago.
        
        Is this customer at risk of churning? If so, suggest a specific re-engagement strategy.";

        return $this->ai->generateText($prompt, 'CRO', 'churn_analysis');
    }
}
