{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    View: Order Detail
--}}
@extends('layouts.shop')

@section('title', 'Order ' . $order->order_number)

@section('content')
<div class="container py-5">
    <div class="order-detail-header">
        <a href="{{ route('orders.index') }}" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Orders
        </a>
        <h1>Order {{ $order->order_number }}</h1>
    </div>

    <div class="order-detail-layout">
        <div class="order-detail-main">
            <!-- Status Timeline -->
            <div class="detail-card">
                <h3>Order Status</h3>
                <div class="status-timeline">
                    <div class="timeline-item {{ in_array($order->status, ['pending','paid','processing','shipped','delivered']) ? 'active' : '' }}">
                        <div class="timeline-dot"></div>
                        <span>Order Placed</span>
                    </div>
                    <div class="timeline-item {{ in_array($order->status, ['paid','processing','shipped','delivered']) ? 'active' : '' }}">
                        <div class="timeline-dot"></div>
                        <span>Payment Confirmed</span>
                    </div>
                    <div class="timeline-item {{ in_array($order->status, ['processing','shipped','delivered']) ? 'active' : '' }}">
                        <div class="timeline-dot"></div>
                        <span>Processing</span>
                    </div>
                    <div class="timeline-item {{ in_array($order->status, ['shipped','delivered']) ? 'active' : '' }}">
                        <div class="timeline-dot"></div>
                        <span>Shipped</span>
                    </div>
                    <div class="timeline-item {{ $order->status == 'delivered' ? 'active' : '' }}">
                        <div class="timeline-dot"></div>
                        <span>Delivered</span>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="detail-card">
                <h3>Items ({{ $order->items->count() }})</h3>
                <div class="detail-items">
                    @foreach($order->items as $item)
                    <div class="detail-item">
                        <div class="item-image">
                            @if($item->product && $item->product->primary_image_url)
                                <img src="{{ $item->product->primary_image_url }}" alt="">
                            @else
                                <i class="fas fa-box"></i>
                            @endif
                        </div>
                        <div class="item-info">
                            <span class="item-name">{{ $item->product_name }}</span>
                            <span class="item-meta">Qty: {{ $item->quantity }} × ₦{{ number_format($item->price) }}</span>
                            @if($order->status == 'delivered')
                                <a href="{{ route('shop.product', $item->product->slug ?? '#') }}?tab=reviews" class="rate-btn">
                                    <i class="fas fa-star"></i> Rate Product
                                </a>
                            @endif
                        </div>
                        <div class="item-total">₦{{ number_format($item->subtotal) }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="order-detail-sidebar">
            <!-- Summary -->
            <div class="detail-card">
                <h3>Summary</h3>
                <div class="summary-rows">
                    <div class="row"><span>Subtotal</span><span>₦{{ number_format($order->subtotal) }}</span></div>
                    <div class="row"><span>Shipping</span><span>₦{{ number_format($order->shipping_cost) }}</span></div>
                    @if($order->discount > 0)
                    <div class="row"><span>Discount</span><span class="text-success">-₦{{ number_format($order->discount) }}</span></div>
                    @endif
                    <div class="row total"><span>Total</span><span>₦{{ number_format($order->total) }}</span></div>
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="detail-card">
                <h3>Delivery Address</h3>
                @php $addr = $order->shipping_address ?? []; @endphp
                <p>
                    <strong>{{ $addr['name'] ?? 'N/A' }}</strong><br>
                    {{ $addr['address'] ?? '' }}<br>
                    {{ $addr['city'] ?? '' }}, {{ $addr['state'] ?? '' }}<br>
                    <i class="fas fa-phone"></i> {{ $addr['phone'] ?? '' }}
                </p>
            </div>

            <!-- Order Info -->
            <div class="detail-card">
                <h3>Order Info</h3>
                <div class="info-rows">
                    <div><span>Order Date</span><strong>{{ $order->created_at->format('M d, Y h:i A') }}</strong></div>
                    @if(isset($order->shipping_address['tracking_id']))
                    <div><span>Tracking ID</span><strong>{{ $order->shipping_address['tracking_id'] }}</strong></div>
                    @endif
                    <div><span>Payment Status</span><span class="status-badge {{ $order->payment_status }}">{{ ucfirst($order->payment_status) }}</span></div>
                </div>
            </div>
        </div>

        <!-- Dispute Section -->
        @if(!in_array($order->status, ['pending', 'cancelled']))
            <div class="detail-card mt-3">
                <h3>Need Help?</h3>
                <p class="text-sm mb-3">If you have an issue with this order, you can open a dispute.</p>
                <button class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#disputeModal">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Open Dispute
                </button>
            </div>

            <!-- Dispute Modal -->
            <div class="modal fade" id="disputeModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('customer.dispute.store', $order->id) }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title">Open Dispute</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Subject</label>
                                    <input type="text" name="subject" class="form-control" required placeholder="e.g. Item not received">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Priority</label>
                                    <select name="priority" class="form-select" required>
                                        <option value="low">Low - Minor issue</option>
                                        <option value="medium" selected>Medium - Normal issue</option>
                                        <option value="high">High - Urgent issue</option>
                                        <option value="critical">Critical - Major problem</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="4" required placeholder="Describe your issue in detail..."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-danger">Submit Dispute</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    .order-detail-header {
        margin-bottom: 32px;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--secondary-500);
        font-size: 14px;
        margin-bottom: 8px;
    }

    .back-link:hover {
        color: var(--primary-600);
    }

    .order-detail-header h1 {
        font-size: 1.75rem;
        font-weight: 800;
    }

    .order-detail-layout {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 24px;
        align-items: start;
    }

    .detail-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 16px;
        border: 1px solid var(--secondary-100);
    }

    .detail-card h3 {
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--secondary-500);
        margin-bottom: 20px;
    }

    .status-timeline {
        display: flex;
        justify-content: space-between;
        position: relative;
    }

    .status-timeline::before {
        content: '';
        position: absolute;
        top: 12px;
        left: 20px;
        right: 20px;
        height: 3px;
        background: var(--secondary-100);
    }

    .timeline-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        position: relative;
        z-index: 1;
    }

    .timeline-dot {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: var(--secondary-100);
        border: 3px solid white;
        box-shadow: 0 0 0 2px var(--secondary-200);
    }

    .timeline-item.active .timeline-dot {
        background: var(--primary-500);
        box-shadow: 0 0 0 2px var(--primary-200);
    }

    .timeline-item span {
        font-size: 11px;
        color: var(--secondary-400);
        text-align: center;
    }

    .timeline-item.active span {
        color: var(--secondary-700);
        font-weight: 600;
    }

    .detail-items {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .detail-item {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .detail-item .item-image {
        width: 64px;
        height: 64px;
        border-radius: 12px;
        background: var(--secondary-50);
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--secondary-300);
    }

    .detail-item .item-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .detail-item .item-info {
        flex: 1;
    }

    .detail-item .item-name {
        display: block;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .detail-item .item-meta {
        font-size: 13px;
        color: var(--secondary-500);
    }

    .detail-item .item-total {
        font-weight: 700;
        font-size: 16px;
    }

    .rate-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 700;
        color: #fbbf24;
        margin-top: 8px;
        padding: 4px 10px;
        background: #fffbeb;
        border-radius: 6px;
        border: 1px solid #fde68a;
    }

    .rate-btn:hover {
        background: #fef3c7;
        color: #d97706;
    }

    .summary-rows .row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        font-size: 14px;
        color: var(--secondary-600);
    }

    .summary-rows .row.total {
        font-size: 18px;
        font-weight: 800;
        color: var(--secondary-900);
        border-top: 1px solid var(--secondary-100);
        padding-top: 16px;
        margin-top: 8px;
    }

    .detail-card p {
        line-height: 1.7;
        color: var(--secondary-700);
    }

    .info-rows > div {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
    }

    .info-rows span:first-child {
        font-size: 13px;
        color: var(--secondary-500);
    }

    .status-badge.pending { background: #fef3c7; color: #d97706; }
    .status-badge.paid { background: #d1fae5; color: #059669; }

    @media (max-width: 900px) {
        .order-detail-layout {
            grid-template-columns: 1fr;
        }
        
        .status-timeline {
            flex-direction: column;
            gap: 12px;
        }
        
        .status-timeline::before {
            display: none;
        }
        
        .timeline-item {
            flex-direction: row;
            gap: 12px;
        }
    }
</style>
@endsection
