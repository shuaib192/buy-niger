<?php

namespace App\Services\AI\Modules;

use App\Services\AI\AIService;
use App\Models\Vendor;
use App\Models\Order;

class CFOModule
{
    protected $ai;

    public function __construct(AIService $ai)
    {
        $this->ai = $ai;
    }

    /**
     * Analyze revenue trends and detect anomalies.
     */
    public function analyzeRevenue(Vendor $vendor)
    {
        // MVP: Compare this month vs last month
        // In a real app, feed daily data points
        $prompt = "You are the AI CFO for '{$vendor->store_name}'.
        Current Month Revenue: ₦1,200,000 (Up 15%)
        Last Month Revenue: ₦1,040,000
        
        Analyze this growth. Is it sustainable? Identify any potential financial risks.";

        return $this->ai->generateText($prompt, 'CFO', 'revenue_analysis');
    }

    /**
     * Detect potential fraud in an order.
     */
    public function detectFraud(Order $order)
    {
        // Gather signals
        $signals = [
            'amount' => $order->total,
            'user_created_at' => $order->user->created_at,
            'ip_address' => '192.168.1.1', // Mock
            'shipping_address' => $order->shipping_address,
        ];

        $prompt = "Analyze this order for fraud risk:
        - Amount: ₦{$signals['amount']}
        - User Account Age: " . $signals['user_created_at']->diffForHumans() . "
        - Address: {$signals['shipping_address']}
        
        Risk Level (Low/Medium/High)? Reason?";

        return $this->ai->generateText($prompt, 'CFO', 'fraud_detection');
    }

    /**
     * Forecast future cash flow.
     */
    public function forecastCashFlow(Vendor $vendor)
    {
        $prompt = "Based on the last 3 months of consistent sales data (avg ₦1M/month) and upcoming holiday season, forecast the cash flow for the next month.";

        return $this->ai->generateText($prompt, 'CFO', 'cash_flow_forecast');
    }
}
