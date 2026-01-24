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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    private $paystackSecretKey;
    private $paystackBaseUrl = 'https://api.paystack.co';

    public function __construct()
    {
        $this->middleware('auth')->except(['webhook']);
        $this->paystackSecretKey = config('services.paystack.secret_key');
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
            $response = Http::withToken($this->paystackSecretKey)
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
            $response = Http::withToken($this->paystackSecretKey)
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

                        // Update order items status and credit vendors
                        foreach ($order->items as $item) {
                            $item->update(['status' => 'processing']);
                            
                            // Credit vendor balance (minus commission)
                            $vendor = $item->vendor;
                            if ($vendor) {
                                $netAmount = $item->subtotal * (1 - ($vendor->commission_rate / 100));
                                $vendor->increment('balance', $netAmount);
                                $vendor->increment('total_sales', $item->subtotal);

                                if ($item->product) {
                                    $item->product->increment('order_count', $item->quantity);
                                }
                            }
                        }

                        return redirect()->route('checkout.confirmation', $order->order_number)
                            ->with('success', 'Payment successful!');
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
                    
                    // Credit vendor balance (minus commission)
                    $vendor = $item->vendor;
                    if ($vendor) {
                        $netAmount = $item->subtotal * (1 - ($vendor->commission_rate / 100));
                        $vendor->increment('balance', $netAmount);
                        $vendor->increment('total_sales', $item->subtotal);

                        if ($item->product) {
                            $item->product->increment('order_count', $item->quantity);
                        }
                    }
                }

                Log::info('Order ' . $order->order_number . ' marked as paid via webhook');
            }
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Show payment page for order.
     */
    public function paymentPage($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->with('items.product')
            ->firstOrFail();

        if ($order->payment_status !== 'pending') {
            return redirect()->route('orders.detail', $orderNumber)
                ->with('info', 'This order has already been paid.');
        }

        return view('shop.payment', compact('order'));
    }
}
