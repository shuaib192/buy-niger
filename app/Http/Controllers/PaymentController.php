<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Controller: PaymentController - Paystack Integration
 */

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Transaction;
use App\Models\VendorBankDetail;
use App\Services\PaystackTransferService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    private $paystackSecretKey;
    private $paystackBaseUrl = 'https://api.paystack.co';
    private $transferService;

    public function __construct(PaystackTransferService $transferService)
    {
        $this->middleware('auth')->except(['webhook']);
        $this->paystackSecretKey = config('services.paystack.secret_key');
        $this->transferService = $transferService;
    }

    /**
     * Initialize payment for an order.
     */
    public function initializePayment(Request $request, $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->where('payment_status', 'pending')
            ->firstOrFail();

        $reference = 'BN_' . time() . '_' . $order->id;

        // Store reference in order
        $order->update(['payment_reference' => $reference]);

        $data = [
            'email' => Auth::user()->email,
            'amount' => (int)($order->total * 100), // Paystack uses kobo
            'reference' => $reference,
            'callback_url' => route('payment.callback'),
            'metadata' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'customer_id' => Auth::id(),
            ]
        ];

        // Debug: Log the request
        Log::info('Paystack Init Request', [
            'order' => $orderNumber,
            'amount' => $data['amount'],
            'email' => $data['email'],
            'has_key' => !empty($this->paystackSecretKey),
        ]);

        try {
            $response = Http::withoutVerifying()->withToken($this->paystackSecretKey)
                ->post($this->paystackBaseUrl . '/transaction/initialize', $data);

            // Debug: Log the response
            Log::info('Paystack Init Response', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            if ($response->successful() && $response->json('status')) {
                $authUrl = $response->json('data.authorization_url');
                if ($authUrl) {
                    return redirect()->away($authUrl);
                }
            }

            $errorMsg = $response->json('message') ?? 'Unknown error';
            return back()->with('error', 'Payment Error: ' . $errorMsg);
        } catch (\Exception $e) {
            Log::error('Paystack init error: ' . $e->getMessage());
            return back()->with('error', 'Payment service unavailable: ' . $e->getMessage());
        }
    }

    /**
     * Handle payment callback.
     */
    public function callback(Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return redirect()->route('orders.index')->with('error', 'Invalid payment reference.');
        }

        // Verify transaction
        try {
            $response = Http::withoutVerifying()->withToken($this->paystackSecretKey)
                ->get($this->paystackBaseUrl . '/transaction/verify/' . $reference);

            if ($response->successful() && $response->json('status')) {
                $data = $response->json('data');

                if ($data['status'] === 'success') {
                    // Find order by reference
                    $order = Order::where('payment_reference', $reference)->first();

                    if ($order) {
                        $order->update([
                            'payment_status' => 'paid',
                            'status' => 'paid',
                            'paid_at' => now(),
                        ]);

                        // Update order items status
                        foreach ($order->items as $item) {
                            $item->update(['status' => 'processing']);
                            
                            $vendor = $item->vendor;
                            if ($vendor) {
                                $vendor->increment('total_sales', $item->subtotal);

                                if ($item->product) {
                                    $item->product->increment('order_count', $item->quantity);
                                }
                            }
                        }

                        // Trigger Automatic Transfers to Vendors
                        $this->processVendorPayouts($order);

                        return redirect()->route('checkout.confirmation', $order->order_number)
                            ->with('success', 'Payment successful and payout initiated!');
                    }
                }
            }

            return redirect()->route('orders.index')->with('error', 'Payment verification failed.');
        } catch (\Exception $e) {
            Log::error('Paystack verify error: ' . $e->getMessage());
            return redirect()->route('orders.index')->with('error', 'Unable to verify payment.');
        }
    }

    /**
     * Paystack webhook handler.
     */
    public function webhook(Request $request)
    {
        // Verify webhook signature
        $signature = $request->header('x-paystack-signature');
        $calculatedSignature = hash_hmac('sha512', $request->getContent(), $this->paystackSecretKey);

        if ($signature !== $calculatedSignature) {
            Log::warning('Paystack webhook: Invalid signature');
            return response()->json(['status' => 'invalid signature'], 400);
        }

        $payload = $request->all();
        $event = $payload['event'] ?? '';

        Log::info('Paystack webhook received: ' . $event);

        if ($event === 'charge.success') {
            $data = $payload['data'] ?? [];
            $reference = $data['reference'] ?? '';

            $order = Order::where('payment_reference', $reference)->first();

            if ($order && $order->payment_status === 'pending') {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);

                foreach ($order->items as $item) {
                    $item->update(['status' => 'processing']);
                    
                    $vendor = $item->vendor;
                    if ($vendor) {
                        $vendor->increment('total_sales', $item->subtotal);

                        if ($item->product) {
                            $item->product->increment('order_count', $item->quantity);
                        }
                    }
                }

                // Trigger Automatic Transfers to Vendors
                $this->processVendorPayouts($order);

                Log::info('Order ' . $order->order_number . ' marked as paid and payouts processed via webhook');
            }
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Automatically transfer funds to vendors for an order.
     */
    private function processVendorPayouts(Order $order)
    {
        if (!$this->transferService->isConfigured()) {
            Log::error('Payout error: Paystack Transfer Service not configured.');
            return;
        }

        // Group items by vendor to handle multiple items from same vendor
        $vendorTotals = [];
        foreach ($order->items as $item) {
            if (!$item->vendor_id) continue;
            
            $commissionRate = $item->vendor->commission_rate ?? 5.00; // Default 5%
            $netAmount = $item->subtotal * (1 - ($commissionRate / 100));
            
            if (!isset($vendorTotals[$item->vendor_id])) {
                $vendorTotals[$item->vendor_id] = 0;
            }
            $vendorTotals[$item->vendor_id] += $netAmount;
        }

        foreach ($vendorTotals as $vendorId => $amount) {
            try {
                $vendor = \App\Models\Vendor::find($vendorId);
                $bankDetail = $vendor->bankDetails()->where('is_primary', true)->first();

                if (!$bankDetail || !$bankDetail->account_number || !$bankDetail->bank_name) {
                    Log::warning("Payout skipped for vendor {$vendor->store_name}: No primary bank details found.");
                    continue;
                }

                // 1. Create/Get Recipient
                $recipient = $this->transferService->createRecipient(
                    $bankDetail->account_name ?? $vendor->store_name,
                    $bankDetail->account_number,
                    $bankDetail->bank_code ?? $this->transferService->resolveBankCodeByName($bankDetail->bank_name)
                );

                if (!$recipient['success']) {
                    Log::error("Payout error for vendor {$vendor->store_name}: " . $recipient['message']);
                    continue;
                }

                $recipientCode = $recipient['data']['recipient_code'];

                // 2. Initiate Transfer
                $transfer = $this->transferService->initiateTransfer(
                    $amount,
                    $recipientCode,
                    'PAYOUT_' . $order->order_number . '_' . $vendorId . '_' . time(),
                    "Payment for Order #{$order->order_number} on BuyNiger"
                );

                if ($transfer['success']) {
                    Log::info("Automatic payout successful for vendor {$vendor->store_name}: ₦{$amount}");
                } else {
                    Log::error("Payout transfer failed for vendor {$vendor->store_name}: " . $transfer['message']);
                }

            } catch (\Exception $e) {
                Log::error("Payout exception for vendor ID {$vendorId}: " . $e->getMessage());
            }
        }
    }

    /**
     * Show payment page for order.
     */
    public function paymentPage($orderId)
    {
        $order = Order::where('id', $orderId)
            ->where('user_id', Auth::id())
            ->with('items.product')
            ->firstOrFail();

        if ($order->payment_status !== 'pending') {
            return redirect()->route('orders.detail', $order->order_number)
                ->with('info', 'This order has already been paid.');
        }

        return view('shop.payment', compact('order'));
    }
}
