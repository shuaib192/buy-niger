{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    View: Vendor Order List (Premium Mobile-First)
--}}
@extends('layouts.app')

@section('title', 'Manage Orders')
@section('page_title', 'Orders')

@section('sidebar')
    @include('vendor.partials.sidebar')
@endsection

@section('content')
<div class="orders-page">
    {{-- Page Header --}}
    <div class="page-header-premium">
        <div>
            <h1 class="page-title">Orders</h1>
            <p class="page-subtitle">Track, manage and fulfill customer orders</p>
        </div>
        <a href="{{ route('vendor.orders.export') }}" class="btn-export">
            <i class="fas fa-download"></i> Export
        </a>
    </div>

    {{-- Stats Row --}}
    <div class="stats-row">
        <div class="stat-chip">
            <div class="stat-chip-icon bg-slate"><i class="fas fa-layer-group"></i></div>
            <div class="stat-chip-body">
                <span class="stat-chip-value">{{ $counts['all'] }}</span>
                <span class="stat-chip-label">All</span>
            </div>
        </div>
        <div class="stat-chip">
            <div class="stat-chip-icon bg-amber"><i class="fas fa-clock"></i></div>
            <div class="stat-chip-body">
                <span class="stat-chip-value">{{ $counts['pending'] }}</span>
                <span class="stat-chip-label">Pending</span>
            </div>
        </div>
        <div class="stat-chip">
            <div class="stat-chip-icon bg-blue"><i class="fas fa-cog"></i></div>
            <div class="stat-chip-body">
                <span class="stat-chip-value">{{ $counts['processing'] }}</span>
                <span class="stat-chip-label">Active</span>
            </div>
        </div>
        <div class="stat-chip">
            <div class="stat-chip-icon bg-emerald"><i class="fas fa-check-circle"></i></div>
            <div class="stat-chip-body">
                <span class="stat-chip-value">{{ $counts['delivered'] }}</span>
                <span class="stat-chip-label">Done</span>
            </div>
        </div>
    </div>

    {{-- Filter Tabs --}}
    <div class="filter-tabs-scroll">
        @php
            $tabs = [
                'all'        => ['label' => 'All',        'icon' => 'fas fa-th-list',       'count' => $counts['all']],
                'pending'    => ['label' => 'Pending',    'icon' => 'fas fa-clock',         'count' => $counts['pending']],
                'processing' => ['label' => 'Processing', 'icon' => 'fas fa-spinner',       'count' => $counts['processing']],
                'shipped'    => ['label' => 'Shipped',    'icon' => 'fas fa-truck',         'count' => $counts['shipped'] ?? 0],
                'delivered'  => ['label' => 'Delivered',  'icon' => 'fas fa-check-double',  'count' => $counts['delivered']],
                'cancelled'  => ['label' => 'Cancelled',  'icon' => 'fas fa-times-circle',  'count' => $counts['cancelled'] ?? 0],
            ];
        @endphp
        @foreach($tabs as $key => $tab)
            <a href="{{ route('vendor.orders', ['status' => $key]) }}" 
               class="filter-chip {{ $status == $key ? 'active' : '' }}">
                <i class="{{ $tab['icon'] }}"></i>
                {{ $tab['label'] }}
                @if($tab['count'] > 0)
                    <span class="chip-count">{{ $tab['count'] }}</span>
                @endif
            </a>
        @endforeach
    </div>

    {{-- Order Cards (Mobile-First) --}}
    <div class="order-cards">
        @forelse($orderItems as $item)
        <a href="{{ route('vendor.orders.show', $item->id) }}" class="order-card">
            {{-- Card Top Row: Order # + Date --}}
            <div class="card-top">
                <span class="order-num">#{{ $item->order->order_number ?? 'N/A' }}</span>
                <span class="order-date">{{ $item->created_at->format('M d, Y') }}</span>
            </div>

            {{-- Card Body: Product + Customer --}}
            <div class="card-body-main">
                <div class="card-product">
                    @if($item->product && $item->product->primary_image_url)
                        <img src="{{ $item->product->primary_image_url }}" alt="" class="product-img">
                    @else
                        <div class="product-img-placeholder"><i class="fas fa-box"></i></div>
                    @endif
                    <div class="product-details">
                        <span class="product-name">{{ Str::limit($item->product_name ?? ($item->product->name ?? 'N/A'), 28) }}</span>
                        <span class="product-qty">Qty: {{ $item->quantity }}</span>
                    </div>
                </div>
                <div class="card-customer">
                    <div class="customer-avatar">{{ substr($item->order->user->name ?? 'G', 0, 1) }}</div>
                    <span class="customer-name">{{ Str::limit($item->order->user->name ?? 'Guest', 14) }}</span>
                </div>
            </div>

            {{-- Card Footer: Amount + Status + Payment --}}
            <div class="card-footer-main">
                <span class="order-amount">₦{{ number_format($item->subtotal) }}</span>
                <div class="card-badges">
                    {{-- Payment badge --}}
                    @if(($item->order->payment_status ?? 'pending') === 'paid')
                        <span class="pay-badge pay-paid"><i class="fas fa-check-circle"></i> Paid</span>
                    @else
                        <span class="pay-badge pay-unpaid"><i class="fas fa-exclamation-circle"></i> Unpaid</span>
                    @endif
                    {{-- Status badge --}}
                    @php
                        $badgeMap = [
                            'delivered'  => 'status-emerald',
                            'cancelled'  => 'status-red',
                            'processing' => 'status-blue',
                            'shipped'    => 'status-indigo',
                            'pending'    => 'status-amber',
                        ];
                    @endphp
                    <span class="status-pill {{ $badgeMap[$item->status] ?? 'status-gray' }}">
                        {{ ucfirst($item->status) }}
                    </span>
                </div>
            </div>

            <div class="card-arrow"><i class="fas fa-chevron-right"></i></div>
        </a>
        @empty
        <div class="empty-state">
            <div class="empty-icon-wrap">
                <i class="fas fa-inbox"></i>
            </div>
            <h3>No orders yet</h3>
            <p>When customers order your products, they'll appear here.</p>
        </div>
        @endforelse
    </div>

    @if($orderItems->hasPages())
    <div class="pagination-wrap">
        {{ $orderItems->links() }}
    </div>
    @endif
</div>

<style>
/* =============================================
   VENDOR ORDERS — MOBILE-FIRST PREMIUM
   ============================================= */
.orders-page { 
    animation: fadeInUp 0.35s ease; 
    padding-bottom: 20px;
    overflow: hidden;
    max-width: 100%;
}
@keyframes fadeInUp { 
    from { opacity: 0; transform: translateY(10px); } 
    to { opacity: 1; transform: translateY(0); } 
}

/* — Page Header — */
.page-header-premium {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    gap: 12px;
    max-width: 100%;
    overflow: hidden;
}
.page-title {
    font-size: 22px;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 2px;
    letter-spacing: -0.03em;
}
.page-subtitle {
    color: #94a3b8;
    font-size: 13px;
    margin: 0;
    font-weight: 500;
}
.btn-export {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 700;
    color: #475569;
    background: white;
    border: 1px solid #e2e8f0;
    text-decoration: none;
    transition: all 0.2s;
    white-space: nowrap;
    box-shadow: 0 1px 2px rgba(0,0,0,0.04);
}
.btn-export:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #1e293b;
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(0,0,0,0.06);
}

/* — Stats Row — */
.stats-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
    margin-bottom: 16px;
    max-width: 100%;
}
.stat-chip {
    background: white;
    border: 1px solid #f1f5f9;
    border-radius: 14px;
    padding: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    overflow: hidden;
}
.stat-chip:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}
.stat-chip-icon {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    flex-shrink: 0;
}
.stat-chip-body {
    display: flex;
    flex-direction: column;
    min-width: 0;
}
.stat-chip-value {
    font-size: 18px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.1;
}
.stat-chip-label {
    font-size: 10px;
    color: #94a3b8;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.bg-slate   { background: #f1f5f9; color: #475569; }
.bg-amber   { background: #fffbeb; color: #d97706; }
.bg-blue    { background: #eff6ff; color: #2563eb; }
.bg-emerald { background: #ecfdf5; color: #059669; }

/* — Filter Tabs — */
.filter-tabs-scroll {
    display: flex;
    gap: 6px;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    padding-bottom: 4px;
    margin-bottom: 16px;
    scrollbar-width: none;
}
.filter-tabs-scroll::-webkit-scrollbar { display: none; }
.filter-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    background: white;
    border: 1px solid #e2e8f0;
    text-decoration: none;
    white-space: nowrap;
    transition: all 0.2s;
}
.filter-chip i { font-size: 11px; }
.filter-chip:hover {
    background: #f8fafc;
    color: #1e293b;
    border-color: #cbd5e1;
}
.filter-chip.active {
    background: #0066FF;
    color: white;
    border-color: #0066FF;
    box-shadow: 0 2px 8px rgba(0,102,255,0.3);
}
.filter-chip.active .chip-count {
    background: rgba(255,255,255,0.25);
    color: white;
}
.chip-count {
    font-size: 10px;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 10px;
    background: #f1f5f9;
    color: #64748b;
    min-width: 18px;
    text-align: center;
}

/* — Order Cards — */
.order-cards {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.order-card {
    display: block;
    background: white;
    border: 1px solid #f1f5f9;
    border-radius: 16px;
    padding: 14px;
    text-decoration: none;
    color: inherit;
    transition: all 0.2s;
    position: relative;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    overflow: hidden;
}
.order-card:hover {
    border-color: #dbeafe;
    box-shadow: 0 4px 16px rgba(0,102,255,0.08);
    transform: translateY(-1px);
}
.order-card:hover .card-arrow {
    opacity: 1;
    transform: translateX(0);
}

/* Card Top */
.card-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    padding-bottom: 10px;
    border-bottom: 1px solid #f8fafc;
}
.order-num {
    font-family: 'JetBrains Mono', 'Fira Code', 'SF Mono', monospace;
    font-size: 12px;
    font-weight: 700;
    color: #0f172a;
    background: #f1f5f9;
    padding: 3px 8px;
    border-radius: 6px;
}
.order-date {
    font-size: 11px;
    color: #94a3b8;
    font-weight: 500;
}

/* Card Body */
.card-body-main {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    margin-bottom: 12px;
}
.card-product {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
    min-width: 0;
}
.product-img {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    object-fit: cover;
    border: 1px solid #f1f5f9;
    flex-shrink: 0;
}
.product-img-placeholder {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #cbd5e1;
    font-size: 16px;
    flex-shrink: 0;
}
.product-details {
    display: flex;
    flex-direction: column;
    min-width: 0;
}
.product-name {
    font-size: 13px;
    font-weight: 600;
    color: #1e293b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.product-qty {
    font-size: 11px;
    color: #94a3b8;
    font-weight: 500;
}
.card-customer {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
}
.customer-avatar {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 11px;
    flex-shrink: 0;
}
.customer-name {
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
}

/* Card Footer */
.card-footer-main {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 10px;
    border-top: 1px solid #f8fafc;
}
.order-amount {
    font-size: 16px;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -0.02em;
}
.card-badges {
    display: flex;
    align-items: center;
    gap: 6px;
}

/* Payment Badge */
.pay-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.02em;
}
.pay-paid {
    background: #ecfdf5;
    color: #059669;
}
.pay-unpaid {
    background: #fef2f2;
    color: #ef4444;
}
.pay-badge i { font-size: 9px; }

/* Status Pill */
.status-pill {
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    white-space: nowrap;
}
.status-emerald { background: #ecfdf5; color: #059669; }
.status-red     { background: #fef2f2; color: #dc2626; }
.status-blue    { background: #eff6ff; color: #2563eb; }
.status-indigo  { background: #eef2ff; color: #4f46e5; }
.status-amber   { background: #fffbeb; color: #d97706; }
.status-gray    { background: #f1f5f9; color: #475569; }

/* Card Arrow */
.card-arrow {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateX(-4px) translateY(-50%);
    opacity: 0;
    color: #cbd5e1;
    font-size: 12px;
    transition: all 0.2s;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border: 1px solid #f1f5f9;
    border-radius: 20px;
}
.empty-icon-wrap {
    width: 72px;
    height: 72px;
    background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
    font-size: 28px;
    color: #94a3b8;
}
.empty-state h3 {
    font-size: 16px;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 4px;
}
.empty-state p {
    font-size: 13px;
    color: #94a3b8;
    margin: 0;
}

/* Pagination */
.pagination-wrap {
    margin-top: 16px;
    display: flex;
    justify-content: center;
}

/* ========== RESPONSIVE ========== */

/* Small phones */
@media (max-width: 374px) {
    .stat-chip-icon { display: none; }
    .stat-chip { padding: 8px; justify-content: center; text-align: center; }
    .stat-chip-body { align-items: center; }
    .page-title { font-size: 18px; }
    .page-subtitle { font-size: 11px; }
    .customer-name { display: none; }
    .card-badges { gap: 4px; }
    .pay-badge { font-size: 9px; padding: 2px 5px; }
    .status-pill { font-size: 9px; padding: 2px 6px; }
}

/* Mobile (up to 768px) */
@media (max-width: 768px) {
    .card-arrow { display: none; }
    .card-body-main { flex-direction: column; align-items: flex-start; gap: 8px; }
    .card-customer { order: -1; }
    .card-footer-main { flex-wrap: wrap; gap: 8px; }
    .card-badges { flex-wrap: wrap; }
}

/* ========== TABLET + DESKTOP ========== */
@media (min-width: 769px) {
    .page-header-premium { margin-bottom: 24px; }
    .page-title { font-size: 26px; }
    .stats-row { grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 20px; }
    .stat-chip { padding: 16px; gap: 12px; }
    .stat-chip-value { font-size: 22px; }
    .stat-chip-icon { width: 40px; height: 40px; font-size: 15px; }
    .filter-tabs-scroll { margin-bottom: 20px; }
    .filter-chip { padding: 9px 18px; font-size: 13px; }
    .order-cards { gap: 10px; }
    .order-card { padding: 16px 20px; border-radius: 18px; }
    .order-card:hover { padding-right: 40px; }
    .product-img, .product-img-placeholder { width: 48px; height: 48px; }
    .order-amount { font-size: 17px; }
    .customer-avatar { width: 30px; height: 30px; font-size: 12px; }
}

/* Large Desktop */
@media (min-width: 1200px) {
    .stats-row { gap: 16px; }
    .stat-chip { padding: 20px; border-radius: 16px; }
    .stat-chip-value { font-size: 26px; }
    .order-card { padding: 18px 24px; }
}
</style>
@endsection
