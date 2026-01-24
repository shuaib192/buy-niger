{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    View: Payment Page
--}}
@extends('layouts.shop')

@section('title', 'Pay for Order ' . $order->order_number)

@section('content')
<div class="container py-5">
    <div class="payment-wrapper">
        <div class="payment-header">
            <i class="fas fa-credit-card"></i>
            <h1>Complete Your Payment</h1>
            <p>Order {{ $order->order_number }}</p>
        </div>

        <div class="payment-content">
            <div class="payment-summary">
                <h3>Order Summary</h3>
                <div class="summary-items">
                    @foreach($order->items as $item)
                    <div class="summary-item">
                        <span class="item-name">{{ Str::limit($item->product_name, 30) }} × {{ $item->quantity }}</span>
                        <span class="item-price">₦{{ number_format($item->subtotal) }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="summary-total">
                    <span>Total to Pay</span>
                    <span class="total-amount">₦{{ number_format($order->total) }}</span>
                </div>
            </div>

            <div class="payment-methods">
                <h3>Payment Method</h3>
                <div class="method-option active">
                    <input type="radio" name="method" value="paystack" checked>
                    <div class="method-info">
                        <img src="https://website-v3-assets.s3.amazonaws.com/assets/img/hero/Paystack-mark-white-twitter.png" alt="Paystack" style="background:#00c3f7; padding:6px; border-radius:6px; width:40px;">
                        <div>
                            <strong>Pay with Paystack</strong>
                            <p>Card, Bank Transfer, USSD</p>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('payment.initialize', $order->order_number) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary btn-lg btn-full pay-btn">
                    <i class="fas fa-lock mr-2"></i> Pay ₦{{ number_format($order->total) }}
                </button>
            </form>

            <div class="payment-security">
                <i class="fas fa-shield-alt"></i>
                <span>Your payment is secured by Paystack</span>
            </div>
        </div>

        <a href="{{ route('orders.detail', $order->order_number) }}" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Order
        </a>
    </div>
</div>

<style>
    .payment-wrapper {
        max-width: 500px;
        margin: 0 auto;
    }

    .payment-header {
        text-align: center;
        margin-bottom: 32px;
    }

    .payment-header i {
        font-size: 3rem;
        color: var(--primary-500);
        margin-bottom: 16px;
    }

    .payment-header h1 {
        font-size: 1.75rem;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .payment-header p {
        color: var(--secondary-500);
    }

    .payment-content {
        background: white;
        border-radius: 24px;
        padding: 32px;
        border: 1px solid var(--secondary-100);
        margin-bottom: 24px;
    }

    .payment-content h3 {
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--secondary-500);
        margin-bottom: 16px;
    }

    .payment-summary {
        padding-bottom: 24px;
        border-bottom: 1px solid var(--secondary-100);
        margin-bottom: 24px;
    }

    .summary-items {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-bottom: 16px;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        font-size: 14px;
        color: var(--secondary-600);
    }

    .summary-total {
        display: flex;
        justify-content: space-between;
        font-size: 16px;
        font-weight: 700;
        padding-top: 16px;
        border-top: 1px dashed var(--secondary-200);
    }

    .total-amount {
        font-size: 24px;
        color: var(--primary-600);
    }

    .payment-methods {
        margin-bottom: 24px;
    }

    .method-option {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 16px;
        border: 2px solid var(--secondary-100);
        border-radius: 14px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .method-option.active {
        border-color: var(--primary-500);
        background: var(--primary-50);
    }

    .method-option input {
        width: 20px;
        height: 20px;
        accent-color: var(--primary-500);
    }

    .method-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .method-info strong {
        display: block;
        font-size: 15px;
    }

    .method-info p {
        font-size: 13px;
        color: var(--secondary-500);
        margin: 0;
    }

    .pay-btn {
        font-size: 16px;
        padding: 16px 24px;
    }

    .payment-security {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 20px;
        font-size: 13px;
        color: var(--secondary-400);
    }

    .payment-security i {
        color: var(--success);
    }

    .back-link {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        color: var(--secondary-500);
        font-size: 14px;
    }

    .back-link:hover {
        color: var(--primary-600);
    }
</style>
@endsection
