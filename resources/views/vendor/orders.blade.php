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

<!-- Styles are in dashboard.css (Orders Page section) -->
@endsection
