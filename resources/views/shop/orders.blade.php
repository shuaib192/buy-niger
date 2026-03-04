@extends('layouts.app')

@section('title', 'My Orders')
@section('page_title', 'My Orders')

@section('sidebar')
    @include('customer.partials.sidebar')
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3>Order History</h3>
            </div>
            <div class="dashboard-card-body">
                @if($orders->count() > 0)
                    <div class="orders-list">
                        @foreach($orders as $order)
                        <div class="order-card-wrap">
                            <a href="{{ route('orders.detail', $order->order_number) }}" class="order-card">
                                <div class="order-header">
                                    <div class="order-id">
                                        <span>Order</span>
                                        <strong>{{ $order->order_number }}</strong>
                                    </div>
                                    <div class="order-badges">
                                        @if($order->payment_status === 'pending' && $order->status !== 'cancelled')
                                            <span class="pay-indicator unpaid"><i class="fas fa-exclamation-circle"></i> Unpaid</span>
                                        @elseif($order->payment_status === 'paid')
                                            <span class="pay-indicator paid"><i class="fas fa-check-circle"></i> Paid</span>
                                        @endif
                                        <span class="status-badge {{ $order->status }}">{{ ucfirst($order->status) }}</span>
                                    </div>
                                </div>
                                <div class="order-body">
                                    <div class="order-items-preview">
                                        @foreach($order->items->take(3) as $item)
                                        <div class="item-thumb" title="{{ $item->product_name }}">
                                            @if($item->product && $item->product->primary_image_url)
                                                <img src="{{ $item->product->primary_image_url }}" alt="">
                                            @else
                                                <i class="fas fa-box"></i>
                                            @endif
                                        </div>
                                        @endforeach
                                        @if($order->items->count() > 3)
                                            <div class="item-thumb more">+{{ $order->items->count() - 3 }}</div>
                                        @endif
                                    </div>
                                    <div class="order-info">
                                        <span class="order-date">{{ $order->created_at->format('M d, Y') }}</span>
                                        <span class="order-total">₦{{ number_format($order->total) }}</span>
                                    </div>
                                </div>
                            </a>
                            @if($order->payment_status === 'pending' && $order->status !== 'cancelled')
                                <a href="{{ route('payment.page', $order->id) }}" class="complete-pay-btn">
                                    <i class="fas fa-credit-card"></i> Complete Payment — ₦{{ number_format($order->total) }}
                                </a>
                            @endif
                        </div>
                        @endforeach
                    </div>

                    <div class="pagination-wrapper">
                        {{ $orders->links() }}
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-shopping-bag"></i>
                        <h2>No orders yet</h2>
                        <p>You haven't placed any orders. Start shopping now!</p>
                        <a href="{{ route('catalog') }}" class="btn btn-primary btn-lg">Browse Products</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    /* ===== MOBILE FIRST ===== */
    .orders-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .order-card-wrap {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid var(--secondary-100);
        transition: all 0.2s;
    }

    .order-card-wrap:hover {
        border-color: var(--primary-200);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .order-card {
        display: block;
        background: white;
        padding: 14px;
        transition: all 0.2s;
        text-decoration: none;
    }

    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        border-bottom: 1px solid var(--secondary-50);
        padding-bottom: 10px;
    }

    .order-id span {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--secondary-400);
        display: block;
        margin-bottom: 1px;
    }

    .order-id strong {
        display: block;
        font-size: 13px;
        color: var(--secondary-900);
    }

    .order-badges {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .pay-indicator {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 8px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 700;
    }
    .pay-indicator i { font-size: 9px; }
    .pay-indicator.unpaid { background: #fef2f2; color: #ef4444; }
    .pay-indicator.paid   { background: #ecfdf5; color: #059669; }

    .status-badge {
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        flex-shrink: 0;
    }

    .status-badge.pending { background: #fff7ed; color: #c2410c; border: 1px solid #ffedd5; }
    .status-badge.paid { background: #ecfdf5; color: #047857; border: 1px solid #d1fae5; }
    .status-badge.processing { background: #eff6ff; color: #1d4ed8; border: 1px solid #dbeafe; }
    .status-badge.shipped { background: #eef2ff; color: #4338ca; border: 1px solid #e0e7ff; }
    .status-badge.delivered { background: #f0fdf4; color: #15803d; border: 1px solid #dcfce7; }
    .status-badge.cancelled { background: #fef2f2; color: #b91c1c; border: 1px solid #fee2e2; }

    /* Mobile: stack order body */
    .order-body {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .order-items-preview {
        display: flex;
        gap: 6px;
    }

    .item-thumb {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background: var(--secondary-50);
        border: 1px solid var(--secondary-100);
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--secondary-400);
        flex-shrink: 0;
        font-size: 0.75rem;
    }

    .item-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .item-thumb.more {
        font-size: 11px;
        font-weight: 600;
        color: var(--secondary-500);
    }

    .order-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid var(--secondary-50);
        padding-top: 8px;
    }

    .order-date {
        font-size: 12px;
        color: var(--secondary-500);
    }

    .order-total {
        font-size: 14px;
        font-weight: 700;
        color: var(--secondary-900);
    }

    /* Complete Payment Button */
    .complete-pay-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 11px 16px;
        background: linear-gradient(135deg, #0066FF, #0052cc);
        color: white;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s;
        border-top: 1px solid rgba(0,102,255,0.2);
    }
    .complete-pay-btn i { font-size: 12px; }
    .complete-pay-btn:hover {
        background: linear-gradient(135deg, #0052cc, #003d99);
        color: white;
    }

    .empty-state {
        text-align: center;
        padding: 40px 16px;
    }

    .empty-state i {
        font-size: 2.5rem;
        color: var(--secondary-200);
        margin-bottom: 12px;
    }

    .empty-state h2 {
        font-size: 1.125rem;
        margin-bottom: 6px;
        color: var(--secondary-900);
    }

    .empty-state p {
        color: var(--secondary-500);
        margin-bottom: 20px;
        font-size: 0.875rem;
    }

    .pagination-wrapper {
        margin-top: 16px;
        display: flex;
        justify-content: center;
    }

    /* ===== TABLET+ (≥600px) ===== */
    @media (min-width: 600px) {
        .order-card {
            padding: 20px;
        }

        .order-header {
            margin-bottom: 16px;
            padding-bottom: 12px;
        }

        .order-id strong { font-size: 15px; }
        .status-badge { font-size: 11px; padding: 4px 10px; }
        .pay-indicator { font-size: 11px; padding: 3px 10px; }

        .order-body {
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
        }

        .item-thumb {
            width: 48px;
            height: 48px;
        }

        .order-info {
            text-align: right;
            flex-direction: column;
            border-top: none;
            padding-top: 0;
            gap: 4px;
        }

        .order-date { font-size: 13px; }
        .order-total { font-size: 16px; }

        .complete-pay-btn { font-size: 14px; padding: 12px 20px; }

        .empty-state { padding: 60px 20px; }
    }
</style>
@endsection
