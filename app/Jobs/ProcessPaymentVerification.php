<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Job: ProcessPaymentVerification
 * Handles payment verification in queue (async)
 */

namespace App\Jobs;

use App\Models\Order;
use App\Events\PaymentCompleted;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessPaymentVerification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [30, 60, 120]; // Retry delays in seconds

    public function __construct(
        public Order $order,
        public string $paymentMethod,
        public string $reference
    ) {
        $this->onQueue('payments');
    }

    public function handle(): void
    {
        try {
            // Get the appropriate payment service based on method
            $paymentService = $this->getPaymentService();
            
            // Verify the payment
            $result = $paymentService->verify($this->reference);
            
            if ($result['success']) {
                // Update order
                $this->order->update([
                    'payment_status' => 'paid',
                    'payment_reference' => $this->reference,
                    'paid_at' => now(),
                    'status' => 'paid',
                ]);

                // Fire payment completed event
                event(new PaymentCompleted(
                    $this->order,
                    $this->paymentMethod,
                    $this->reference,
                    $result['amount']
                ));

                Log::info("Payment verified for order {$this->order->order_number}");
            } else {
                $this->order->update([
                    'payment_status' => 'failed',
                ]);
                
                Log::warning("Payment failed for order {$this->order->order_number}: " . ($result['message'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            Log::error("Payment verification error: " . $e->getMessage());
            throw $e; // Re-throw to trigger retry
        }
    }

    protected function getPaymentService()
    {
        return match ($this->paymentMethod) {
            'paystack' => app(\App\Services\Payment\PaystackService::class),
            'flutterwave' => app(\App\Services\Payment\FlutterwaveService::class),
            'stripe' => app(\App\Services\Payment\StripeService::class),
            default => throw new \Exception("Unknown payment method: {$this->paymentMethod}"),
        };
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Payment verification job failed for order {$this->order->order_number}: " . $exception->getMessage());
        
        // Update order status
        $this->order->update([
            'payment_status' => 'failed',
        ]);

        // Record job failure metrics
        \App\Services\MetricsService::recordJobFailure(self::class, 'payments');
    }
}
