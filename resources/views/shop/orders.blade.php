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
                        <a href="{{ route('orders.detail', $order->order_number) }}" class="order-card">
                            <div class="order-header">
                                <div class="order-id">
                                    <span>Order</span>
                                    <strong>{{ $order->order_number }}</strong>
                                </div>
                                <span class="status-badge {{ $order->status }}">{{ ucfirst($order->status) }}</span>
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
    .orders-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .order-card {
        display: block;
        background: white;
        border-radius: 12px;
        padding: 20px;
        border: 1px solid var(--secondary-100);
        transition: all 0.2s;
        text-decoration: none;
    }

    .order-card:hover {
        border-color: var(--primary-200);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transform: translateY(-2px);
    }

    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        border-bottom: 1px solid var(--secondary-50);
        padding-bottom: 12px;
    }

    .order-id span {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--secondary-400);
        display: block;
        margin-bottom: 2px;
    }

    .order-id strong {
        display: block;
        font-size: 15px;
        color: var(--secondary-900);
    }

    .status-badge {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-badge.pending { background: #fff7ed; color: #c2410c; border: 1px solid #ffedd5; }
    .status-badge.paid { background: #ecfdf5; color: #047857; border: 1px solid #d1fae5; }
    .status-badge.processing { background: #eff6ff; color: #1d4ed8; border: 1px solid #dbeafe; }
    .status-badge.shipped { background: #eef2ff; color: #4338ca; border: 1px solid #e0e7ff; }
    .status-badge.delivered { background: #f0fdf4; color: #15803d; border: 1px solid #dcfce7; }
    .status-badge.cancelled { background: #fef2f2; color: #b91c1c; border: 1px solid #fee2e2; }

    .order-body {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .order-items-preview {
        display: flex;
        gap: 8px;
    }

    .item-thumb {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        background: var(--secondary-50);
        border: 1px solid var(--secondary-100);
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--secondary-400);
    }

    .item-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .item-thumb.more {
        font-size: 12px;
        font-weight: 600;
        color: var(--secondary-500);
        background: var(--secondary-50);
    }

    .order-info {
        text-align: right;
    }

    .order-date {
        display: block;
        font-size: 13px;
        color: var(--secondary-500);
        margin-bottom: 4px;
    }

    .order-total {
        font-size: 16px;
        font-weight: 700;
        color: var(--secondary-900);
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-state i {
        font-size: 3rem;
        color: var(--secondary-200);
        margin-bottom: 16px;
    }

    .empty-state h2 {
        font-size: 1.25rem;
        margin-bottom: 8px;
        color: var(--secondary-900);
    }

    .empty-state p {
        color: var(--secondary-500);
        margin-bottom: 24px;
    }

    .pagination-wrapper {
        margin-top: 24px;
        display: flex;
        justify-content: center;
    }

    @media (max-width: 600px) {
        .order-body {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }
        
        .order-info {
            text-align: left;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid var(--secondary-50);
            padding-top: 12px;
        }
    }
</style>
@endsection
