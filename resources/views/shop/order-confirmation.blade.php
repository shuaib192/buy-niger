{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    View: Order Confirmation
--}}
@extends('layouts.shop')

@section('title', 'Order Confirmed')

@section('content')
<div class="container py-5">
    <div class="confirmation-wrapper">
        <div class="confirmation-header">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>
            <h1>Thank You!</h1>
            <p>Your order has been placed successfully</p>
        </div>

        <div class="order-info-card">
            <div class="order-number">
                <span>Order Number</span>
                <strong>{{ $order->order_number }}</strong>
            </div>
            @php $addr = $order->shipping_address ?? []; @endphp
            @if(isset($addr['tracking_id']))
            <div class="tracking-number">
                <span>Tracking ID</span>
                <strong>{{ $addr['tracking_id'] }}</strong>
            </div>
            @endif
            <div class="order-meta">
                <div class="meta-item">
                    <span>Date</span>
                    <strong>{{ $order->created_at->format('M d, Y') }}</strong>
                </div>
                <div class="meta-item">
                    <span>Total</span>
                    <strong>₦{{ number_format($order->total) }}</strong>
                </div>
                <div class="meta-item">
                    <span>Status</span>
                    <span class="status-badge pending">{{ ucfirst($order->status) }}</span>
                </div>
            </div>
        </div>

        <div class="confirmation-sections">
            <div class="conf-section">
                <h3>Order Items</h3>
                <div class="order-items">
                    @foreach($order->items as $item)
                    <div class="order-item">
                        <div class="item-qty">×{{ $item->quantity }}</div>
                        <div class="item-name">{{ $item->product_name }}</div>
                        <div class="item-price">₦{{ number_format($item->subtotal) }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="conf-section">
                <h3>Delivery Address</h3>
                @php $addr = $order->shipping_address; @endphp
                <p>
                    <strong>{{ $addr['name'] ?? 'N/A' }}</strong><br>
                    {{ $addr['address'] ?? '' }}<br>
                    {{ $addr['city'] ?? '' }}, {{ $addr['state'] ?? '' }}<br>
                    <i class="fas fa-phone"></i> {{ $addr['phone'] ?? '' }}
                </p>
            </div>
        </div>

        <div class="confirmation-actions">
            @if($order->payment_status === 'pending')
                <a href="{{ route('payment.page', $order->order_number) }}" class="btn btn-success">
                    <i class="fas fa-credit-card mr-2"></i> Pay Now ₦{{ number_format($order->total) }}
                </a>
            @endif
            <a href="{{ route('orders.detail', $order->order_number) }}" class="btn btn-primary">
                <i class="fas fa-eye mr-2"></i> View Order Details
            </a>
            <a href="{{ route('catalog') }}" class="btn btn-outline">Continue Shopping</a>
        </div>
    </div>
</div>

<style>
    .confirmation-wrapper {
        max-width: 700px;
        margin: 0 auto;
    }

    .confirmation-header {
        text-align: center;
        margin-bottom: 32px;
    }

    .success-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 24px;
        color: white;
        font-size: 2rem;
        box-shadow: 0 10px 40px rgba(16, 185, 129, 0.4);
    }

    .confirmation-header h1 {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .confirmation-header p {
        color: var(--secondary-500);
        font-size: 16px;
    }

    .order-info-card {
        background: white;
        border-radius: 20px;
        padding: 28px;
        margin-bottom: 24px;
        border: 1px solid var(--secondary-100);
    }

    .order-number {
        text-align: center;
        padding-bottom: 20px;
        border-bottom: 1px dashed var(--secondary-200);
        margin-bottom: 20px;
    }

    .order-number span {
        display: block;
        font-size: 13px;
        color: var(--secondary-400);
        margin-bottom: 4px;
    }

    .order-number strong {
        font-size: 24px;
        font-weight: 800;
        color: var(--primary-600);
        letter-spacing: 2px;
    }

    .order-meta {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        text-align: center;
    }

    .meta-item span {
        display: block;
        font-size: 12px;
        color: var(--secondary-400);
        margin-bottom: 4px;
    }

    .meta-item strong {
        font-size: 15px;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
    }

    .status-badge.pending {
        background: #fef3c7;
        color: #d97706;
    }

    .confirmation-sections {
        display: grid;
        gap: 24px;
        margin-bottom: 32px;
    }

    .conf-section {
        background: white;
        border-radius: 16px;
        padding: 24px;
        border: 1px solid var(--secondary-100);
    }

    .conf-section h3 {
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--secondary-500);
        margin-bottom: 16px;
    }

    .order-items {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .order-item {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .item-qty {
        background: var(--secondary-100);
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 700;
        color: var(--secondary-600);
    }

    .item-name {
        flex: 1;
        font-weight: 600;
    }

    .item-price {
        font-weight: 700;
        color: var(--secondary-900);
    }

    .conf-section p {
        line-height: 1.7;
        color: var(--secondary-700);
    }

    .confirmation-actions {
        display: flex;
        gap: 16px;
        justify-content: center;
    }

    .btn-outline {
        border: 2px solid var(--secondary-200);
        color: var(--secondary-700);
        background: transparent;
    }

    @media (max-width: 600px) {
        .order-meta {
            grid-template-columns: 1fr;
        }
        
        .confirmation-actions {
            flex-direction: column;
        }
        
        .confirmation-actions .btn {
            width: 100%;
        }
    }
</style>
@endsection
