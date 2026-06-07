{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin
    View: Super Admin / Admin Dashboard — Premium v2.0
--}}
@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@push('styles')
<style>
    .dash-layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: 18px;
    }
    @media (min-width: 900px) {
        .dash-layout { grid-template-columns: 1fr 340px; }
    }
    .dash-main, .dash-side {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }
    .track-bar {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
        border-radius: 16px;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }
    .track-bar-info h3 {
        font-family: 'Outfit', sans-serif;
        color: white;
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 2px;
    }
    .track-bar-info p {
        color: rgba(255,255,255,.6);
        font-size: 0.8125rem;
        margin: 0;
    }
    .track-form {
        display: flex;
        gap: 8px;
        flex: 1;
        max-width: 420px;
        min-width: 260px;
    }
    .track-form .form-control {
        background: rgba(255,255,255,.1);
        border-color: rgba(255,255,255,.15);
        color: white;
        flex: 1;
    }
    .track-form .form-control::placeholder { color: rgba(255,255,255,.45); }
    .track-form .form-control:focus {
        background: rgba(255,255,255,.15);
        border-color: rgba(255,255,255,.3);
        box-shadow: none;
    }

    .ai-status-dot {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.8125rem;
        font-weight: 600;
        color: #10b981;
    }
    .ai-status-dot::before {
        content: '';
        width: 8px; height: 8px;
        border-radius: 50%;
        background: #10b981;
        box-shadow: 0 0 0 3px rgba(16,185,129,.2);
        animation: pulse-ai 2s ease infinite;
    }
    @keyframes pulse-ai {
        0%,100% { box-shadow: 0 0 0 3px rgba(16,185,129,.2); }
        50%      { box-shadow: 0 0 0 6px rgba(16,185,129,.1); }
    }

    .vendor-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .vendor-item:last-child { border-bottom: none; }
    .vendor-avatar {
        width: 38px; height: 38px;
        border-radius: 10px;
        background: linear-gradient(135deg, #4f46e5, #8b5cf6);
        display: flex; align-items: center; justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 0.875rem;
        flex-shrink: 0;
        overflow: hidden;
    }
    .vendor-avatar img { width:100%;height:100%;object-fit:cover; }
    .vendor-item-info { flex: 1; min-width: 0; }
    .vendor-item-name {
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .vendor-item-date {
        font-size: 0.6875rem;
        color: var(--text-muted);
        margin-top: 1px;
    }
    .section-badge {
        background: #fef3c7;
        color: #92400e;
        font-size: 0.6875rem;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 999px;
    }

    .admin-ai-banner {
        background: linear-gradient(135deg, #312e81 0%, #4c1d95 100%);
        border-radius: 14px;
        padding: 18px;
        color: white;
        position: relative;
        overflow: hidden;
    }
    .admin-ai-banner::before {
        content: '';
        position: absolute;
        top: -30px; right: -20px;
        width: 100px; height: 100px;
        background: rgba(255,255,255,.07);
        border-radius: 50%;
    }
    .admin-ai-banner h4 {
        font-family: 'Outfit', sans-serif;
        color: white;
        font-size: 0.9375rem;
        font-weight: 700;
        margin-bottom: 4px;
        position: relative;
        z-index: 1;
    }
    .admin-ai-banner p {
        color: rgba(255,255,255,.7);
        font-size: 0.8rem;
        margin-bottom: 12px;
        position: relative;
        z-index: 1;
    }
    .admin-ai-banner .btn-white-ghost {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        background: rgba(255,255,255,.15);
        border: 1px solid rgba(255,255,255,.2);
        color: white;
        border-radius: 8px;
        font-size: 0.8125rem;
        font-weight: 600;
        text-decoration: none;
        transition: all .2s;
        position: relative;
        z-index: 1;
    }
    .admin-ai-banner .btn-white-ghost:hover {
        background: rgba(255,255,255,.25);
        color: white;
    }

    .stat-mini-row {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .stat-mini {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        background: var(--surface);
        border-radius: 10px;
    }
    .stat-mini-label { font-size: 0.8125rem; color: var(--text-secondary); font-weight: 500; }
    .stat-mini-val   { font-size: 0.9rem; font-weight: 700; color: var(--text-primary); }
</style>
@endpush

@section('content')
@php $prefix = request()->is('admin*') ? 'admin.' : 'superadmin.'; @endphp

{{-- ═══ STAT CARDS ═══ --}}
<div class="stats-grid">
    <div class="stat-card blue">
        <div class="stat-card-inner">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-info">
                <h3>{{ number_format($stats['total_users']) }}</h3>
                <p>Total Users</p>
                <span class="stat-change"><i class="fas fa-arrow-trend-up"></i> +12% this month</span>
            </div>
        </div>
    </div>
    <div class="stat-card purple">
        <div class="stat-card-inner">
            <div class="stat-icon"><i class="fas fa-store"></i></div>
            <div class="stat-info">
                <h3>{{ number_format($stats['active_vendors']) }}</h3>
                <p>Active Vendors</p>
                <span class="stat-change"><i class="fas fa-arrow-trend-up"></i> +8 new</span>
            </div>
        </div>
    </div>
    <div class="stat-card orange">
        <div class="stat-card-inner">
            <div class="stat-icon"><i class="fas fa-bag-shopping"></i></div>
            <div class="stat-info">
                <h3>{{ number_format($stats['total_orders']) }}</h3>
                <p>Total Orders</p>
                <span class="stat-change"><i class="fas fa-arrow-trend-up"></i> +23% growth</span>
            </div>
        </div>
    </div>
    <div class="stat-card indigo">
        <div class="stat-card-inner">
            <div class="stat-icon"><i class="fas fa-naira-sign"></i></div>
            <div class="stat-info">
                <h3>₦{{ number_format($stats['total_revenue'] ?? 0) }}</h3>
                <p>Total Revenue</p>
                <span class="stat-change"><i class="fas fa-arrow-trend-up"></i> +18% this month</span>
            </div>
        </div>
    </div>
</div>

{{-- ═══ TRACK ORDER BAR ═══ --}}
<div class="track-bar">
    <div class="track-bar-info">
        <h3><i class="fas fa-magnifying-glass" style="margin-right:6px;"></i>Track Order</h3>
        <p>Enter an Order Number (e.g., BN-…) or Tracking ID</p>
    </div>
    <form action="{{ route($prefix.'track') }}" method="POST" class="track-form">
        @csrf
        <input type="text" name="order_number" class="form-control"
               placeholder="e.g. BN-20240001" required>
        <button type="submit" class="btn btn-primary" style="white-space:nowrap;">
            <i class="fas fa-search"></i> Track
        </button>
    </form>
</div>

{{-- ═══ TWO-COLUMN LAYOUT ═══ --}}
<div class="dash-layout">

    {{-- LEFT COLUMN --}}
    <div class="dash-main">

        {{-- Recent Orders --}}
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3><i class="fas fa-receipt" style="color:#4f46e5;margin-right:8px;"></i>Recent Orders</h3>
                <a href="{{ route($prefix.'orders') }}" class="btn btn-sm btn-secondary">
                    View All <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="dashboard-card-body" style="padding:0;">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                                <tr>
                                    <td><strong>#{{ $order->order_number }}</strong></td>
                                    <td>
                                        <div style="display:flex;align-items:center;gap:8px;">
                                            <div class="vendor-avatar" style="width:28px;height:28px;font-size:.7rem;border-radius:7px;">
                                                {{ strtoupper(substr($order->user->name ?? 'U', 0, 1)) }}
                                            </div>
                                            {{ $order->user->name ?? '—' }}
                                        </div>
                                    </td>
                                    <td><strong>₦{{ number_format($order->total) }}</strong></td>
                                    <td>
                                        @php
                                            $badgeMap = ['pending'=>'warning','paid'=>'info','processing'=>'primary','shipped'=>'info','delivered'=>'success','cancelled'=>'danger'];
                                        @endphp
                                        <span class="badge badge-{{ $badgeMap[$order->status] ?? 'secondary' }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('d M Y') }}</td>
                                    <td>
                                        <a href="{{ route($prefix.'orders.show', $order) }}"
                                           class="btn btn-sm btn-secondary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="empty-state">
                                            <i class="fas fa-inbox"></i>
                                            <p>No orders yet</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3><i class="fas fa-bolt" style="color:#f59e0b;margin-right:8px;"></i>Quick Actions</h3>
            </div>
            <div class="dashboard-card-body">
                <div class="quick-actions">
                    <a href="{{ route($prefix.'users') }}" class="quick-action-btn">
                        <i class="fas fa-user-plus"></i><span>Users</span>
                    </a>
                    <a href="{{ route($prefix.'vendors') }}" class="quick-action-btn">
                        <i class="fas fa-store"></i><span>Vendors</span>
                    </a>
                    <a href="{{ route($prefix.'orders') }}" class="quick-action-btn">
                        <i class="fas fa-bag-shopping"></i><span>Orders</span>
                    </a>
                    <a href="{{ route($prefix.'transactions') }}" class="quick-action-btn">
                        <i class="fas fa-chart-bar"></i><span>Reports</span>
                    </a>
                    @if($prefix === 'superadmin.')
                    <a href="{{ route($prefix.'settings') }}" class="quick-action-btn">
                        <i class="fas fa-gear"></i><span>Settings</span>
                    </a>
                    @endif
                    <a href="{{ route($prefix.'disputes') }}" class="quick-action-btn">
                        <i class="fas fa-scale-balanced"></i><span>Disputes</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT COLUMN --}}
    <div class="dash-side">

        @if($prefix === 'superadmin.')
        {{-- AI System Status --}}
        <div class="admin-ai-banner">
            <h4><i class="fas fa-robot" style="margin-right:6px;"></i>AI System</h4>
            <p>Shadow mode active — monitoring transactions in real time</p>
            <div class="ai-status-dot" style="margin-bottom:12px;">System Online</div>
            <div class="stat-mini-row" style="margin-bottom:12px;">
                <div class="stat-mini" style="background:rgba(255,255,255,.1);color:white;">
                    <span class="stat-mini-label" style="color:rgba(255,255,255,.7);">Pending Proposals</span>
                    <span class="stat-mini-val" style="color:white;">3</span>
                </div>
                <div class="stat-mini" style="background:rgba(255,255,255,.1);color:white;">
                    <span class="stat-mini-label" style="color:rgba(255,255,255,.7);">Fraud Alerts</span>
                    <span class="stat-mini-val" style="color:#fbbf24;">0</span>
                </div>
            </div>
            <a href="{{ route($prefix.'ai') }}" class="btn-white-ghost">
                <i class="fas fa-sliders"></i> Manage AI
            </a>
        </div>
        @endif

        {{-- Pending Vendors --}}
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3><i class="fas fa-clock" style="color:#f59e0b;margin-right:8px;"></i>Pending Vendors</h3>
                @if(($stats['pending_vendors'] ?? 0) > 0)
                    <span class="section-badge">{{ $stats['pending_vendors'] }} waiting</span>
                @endif
            </div>
            <div class="dashboard-card-body">
                @forelse($pendingVendors as $vendor)
                    <div class="vendor-item">
                        <div class="vendor-avatar">
                            @if($vendor->user->avatar_url && !str_contains($vendor->user->avatar_url, 'ui-avatars'))
                                <img src="{{ $vendor->user->avatar_url }}" alt="">
                            @else
                                {{ strtoupper(substr($vendor->store_name ?? 'V', 0, 1)) }}
                            @endif
                        </div>
                        <div class="vendor-item-info">
                            <div class="vendor-item-name">{{ $vendor->store_name ?? $vendor->user->name }}</div>
                            <div class="vendor-item-date">Applied {{ $vendor->created_at->diffForHumans() }}</div>
                        </div>
                        <form action="{{ route($prefix.'vendors.status', $vendor) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="approved">
                            <button type="submit" class="btn btn-sm btn-success" title="Approve">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="empty-state" style="padding:24px 0;">
                        <i class="fas fa-check-circle" style="color:#10b981;"></i>
                        <p>No pending vendors</p>
                    </div>
                @endforelse

                <a href="{{ route($prefix.'vendors') }}"
                   class="btn btn-secondary btn-full btn-sm" style="margin-top:12px;">
                    View All Vendors
                </a>
            </div>
        </div>

        {{-- Platform Summary --}}
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3><i class="fas fa-chart-pie" style="color:#0ea5e9;margin-right:8px;"></i>Platform Summary</h3>
            </div>
            <div class="dashboard-card-body">
                <div class="stat-mini-row">
                    <div class="stat-mini">
                        <span class="stat-mini-label">Pending Orders</span>
                        <span class="stat-mini-val">{{ $stats['pending_orders'] ?? 0 }}</span>
                    </div>
                    <div class="stat-mini">
                        <span class="stat-mini-label">Products Listed</span>
                        <span class="stat-mini-val">{{ number_format($stats['total_products'] ?? 0) }}</span>
                    </div>
                    <div class="stat-mini">
                        <span class="stat-mini-label">Pending Payouts</span>
                        <span class="stat-mini-val">{{ $stats['pending_payouts'] ?? 0 }}</span>
                    </div>
                    <div class="stat-mini">
                        <span class="stat-mini-label">Open Disputes</span>
                        <span class="stat-mini-val">{{ $stats['open_disputes'] ?? 0 }}</span>
                    </div>
                </div>
                @if($prefix === 'superadmin.')
                <a href="{{ route($prefix.'analytics') }}"
                   class="btn btn-primary btn-full btn-sm" style="margin-top:14px;">
                    <i class="fas fa-chart-line"></i> View Full Analytics
                </a>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
