{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin
    View: Customer Dashboard — Premium v2.0
--}}
@extends('layouts.app')

@section('title', 'My Account')
@section('page_title', 'My Account')

@section('sidebar')
    @include('customer.partials.sidebar')
@endsection

@push('styles')
<style>
    .dash-layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: 18px;
    }
    @media (min-width: 900px) {
        .dash-layout { grid-template-columns: 1fr 300px; }
    }
    .dash-main, .dash-side { display: flex; flex-direction: column; gap: 18px; }

    .welcome-banner {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 60%, #0ea5e9 100%);
        border-radius: 18px;
        padding: 24px 28px;
        color: white;
        margin-bottom: 20px;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }
    .welcome-banner::before {
        content: '';
        position: absolute;
        top: -60px; right: -40px;
        width: 180px; height: 180px;
        background: rgba(255,255,255,.08);
        border-radius: 50%;
    }
    .welcome-banner::after {
        content: '';
        position: absolute;
        bottom: -50px; right: 120px;
        width: 120px; height: 120px;
        background: rgba(255,255,255,.05);
        border-radius: 50%;
    }
    .welcome-content { position: relative; z-index: 1; }
    .welcome-content h2 {
        font-family: 'Outfit', sans-serif;
        color: white;
        font-size: 1.375rem;
        font-weight: 800;
        margin-bottom: 4px;
    }
    .welcome-content p { color: rgba(255,255,255,.75); font-size: 0.875rem; margin: 0; }
    .welcome-cta { position: relative; z-index: 1; }
    .btn-white-solid {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 10px 20px;
        background: white;
        color: #4f46e5;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.875rem;
        text-decoration: none;
        transition: all .2s;
        box-shadow: 0 4px 16px rgba(0,0,0,.15);
    }
    .btn-white-solid:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,.2);
        color: #4338ca;
    }

    .quick-link-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 13px 16px;
        border-radius: 12px;
        text-decoration: none;
        transition: all .2s;
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--text-secondary);
        background: var(--surface);
        border: 1.5px solid var(--border-color);
    }
    .quick-link-btn:hover {
        background: #ede9fe;
        border-color: #c4b5fd;
        color: #5b21b6;
        transform: translateX(4px);
    }
    .quick-link-btn .ql-icon {
        width: 34px; height: 34px;
        border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.9rem;
        flex-shrink: 0;
    }
    .ql-icon.indigo { background: #ede9fe; color: #5b21b6; }
    .ql-icon.blue   { background: #dbeafe; color: #1d4ed8; }
    .ql-icon.rose   { background: #ffe4e6; color: #be123c; }
    .ql-icon.green  { background: #d1fae5; color: #065f46; }
    .ql-icon.orange { background: #ffedd5; color: #9a3412; }
    .ql-icon.teal   { background: #ccfbf1; color: #0f766e; }

    .profile-progress {
        margin-top: 8px;
    }
    .profile-progress-bar {
        height: 6px;
        background: #e2e8f0;
        border-radius: 99px;
        overflow: hidden;
        margin-top: 6px;
    }
    .profile-progress-fill {
        height: 100%;
        border-radius: 99px;
        background: linear-gradient(90deg, #4f46e5, #8b5cf6);
        transition: width .5s ease;
    }
    .profile-progress-label {
        display: flex;
        justify-content: space-between;
        font-size: 0.75rem;
        color: var(--text-muted);
        margin-top: 4px;
    }
</style>
@endpush

@section('content')

{{-- ═══ WELCOME BANNER ═══ --}}
<div class="welcome-banner">
    <div class="welcome-content">
        <h2>Welcome back, {{ Str::words(Auth::user()->name, 1, '') }}! 👋</h2>
        <p>Here's what's happening with your account today.</p>
    </div>
    <div class="welcome-cta">
        <a href="{{ route('catalog') }}" class="btn-white-solid">
            <i class="fas fa-shop"></i> Continue Shopping
        </a>
    </div>
</div>

{{-- ═══ STAT CARDS ═══ --}}
<div class="stats-grid" style="margin-bottom:20px;">
    <div class="stat-card blue">
        <div class="stat-card-inner">
            <div class="stat-icon"><i class="fas fa-bag-shopping"></i></div>
            <div class="stat-info">
                <h3>{{ $stats['total_orders'] }}</h3>
                <p>Total Orders</p>
            </div>
        </div>
    </div>
    <div class="stat-card orange">
        <div class="stat-card-inner">
            <div class="stat-icon"><i class="fas fa-clock"></i></div>
            <div class="stat-info">
                <h3>{{ $stats['pending_orders'] }}</h3>
                <p>Pending Orders</p>
            </div>
        </div>
    </div>
    <div class="stat-card fire">
        <div class="stat-card-inner">
            <div class="stat-icon"><i class="fas fa-heart"></i></div>
            <div class="stat-info">
                <h3>{{ $stats['wishlist_count'] }}</h3>
                <p>Wishlist Items</p>
            </div>
        </div>
    </div>
    <div class="stat-card teal">
        <div class="stat-card-inner">
            <div class="stat-icon"><i class="fas fa-star"></i></div>
            <div class="stat-info">
                <h3>{{ $stats['reviews_given'] }}</h3>
                <p>Reviews Given</p>
            </div>
        </div>
    </div>
</div>

{{-- ═══ TWO-COLUMN LAYOUT ═══ --}}
<div class="dash-layout">

    {{-- LEFT: Recent Orders --}}
    <div class="dash-main">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3><i class="fas fa-receipt" style="color:#4f46e5;margin-right:8px;"></i>Recent Orders</h3>
                <a href="{{ route('orders.index') }}" class="btn btn-sm btn-secondary">
                    View All <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="dashboard-card-body" style="padding:0;">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                                @php
                                    $statusMap = ['pending'=>'warning','paid'=>'info','processing'=>'primary','shipped'=>'info','delivered'=>'success','cancelled'=>'danger'];
                                @endphp
                                <tr>
                                    <td><strong>#{{ $order->order_number }}</strong></td>
                                    <td>{{ $order->items->count() }} item(s)</td>
                                    <td><strong>₦{{ number_format($order->total) }}</strong></td>
                                    <td>
                                        <span class="badge badge-{{ $statusMap[$order->status] ?? 'secondary' }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('d M Y') }}</td>
                                    <td>
                                        <a href="{{ route('orders.detail', $order->order_number) }}"
                                           class="btn btn-sm btn-secondary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="empty-state">
                                            <i class="fas fa-bag-shopping"></i>
                                            <p>No orders yet. <a href="{{ route('catalog') }}">Start shopping!</a></p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT: Quick Links + Profile --}}
    <div class="dash-side">

        {{-- Quick Links --}}
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3><i class="fas fa-bolt" style="color:#f59e0b;margin-right:8px;"></i>Quick Links</h3>
            </div>
            <div class="dashboard-card-body" style="display:flex;flex-direction:column;gap:8px;">
                <a href="{{ route('orders.index') }}" class="quick-link-btn">
                    <div class="ql-icon indigo"><i class="fas fa-bag-shopping"></i></div>
                    <span>My Orders</span>
                    <i class="fas fa-chevron-right" style="margin-left:auto;font-size:.75rem;color:var(--text-muted);"></i>
                </a>
                <a href="{{ route('wishlist.index') }}" class="quick-link-btn">
                    <div class="ql-icon rose"><i class="fas fa-heart"></i></div>
                    <span>My Wishlist</span>
                    <i class="fas fa-chevron-right" style="margin-left:auto;font-size:.75rem;color:var(--text-muted);"></i>
                </a>
                <a href="{{ route('customer.profile') }}" class="quick-link-btn">
                    <div class="ql-icon blue"><i class="fas fa-user-pen"></i></div>
                    <span>Edit Profile</span>
                    <i class="fas fa-chevron-right" style="margin-left:auto;font-size:.75rem;color:var(--text-muted);"></i>
                </a>
                <a href="{{ route('customer.addresses') }}" class="quick-link-btn">
                    <div class="ql-icon green"><i class="fas fa-location-dot"></i></div>
                    <span>Manage Addresses</span>
                    <i class="fas fa-chevron-right" style="margin-left:auto;font-size:.75rem;color:var(--text-muted);"></i>
                </a>
                <a href="{{ route('customer.reviews.index') }}" class="quick-link-btn">
                    <div class="ql-icon orange"><i class="fas fa-star"></i></div>
                    <span>My Reviews</span>
                    <i class="fas fa-chevron-right" style="margin-left:auto;font-size:.75rem;color:var(--text-muted);"></i>
                </a>
                <a href="{{ route('notifications.index') }}" class="quick-link-btn">
                    <div class="ql-icon teal"><i class="fas fa-bell"></i></div>
                    <span>Notifications</span>
                    <i class="fas fa-chevron-right" style="margin-left:auto;font-size:.75rem;color:var(--text-muted);"></i>
                </a>
            </div>
        </div>

        {{-- Profile Completion --}}
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3><i class="fas fa-user-circle" style="color:#0ea5e9;margin-right:8px;"></i>Profile Completion</h3>
            </div>
            <div class="dashboard-card-body">
                @php
                    $user = Auth::user();
                    $completed = 0; $total = 4;
                    if ($user->name)  $completed++;
                    if ($user->email) $completed++;
                    if ($user->phone) $completed++;
                    if ($user->addresses && $user->addresses->count()) $completed++;
                    $pct = round(($completed / $total) * 100);
                @endphp
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                    <div class="vendor-avatar" style="width:44px;height:44px;font-size:1rem;border-radius:12px;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-weight:700;font-size:.875rem;color:var(--text-primary);">{{ $user->name }}</div>
                        <div style="font-size:.75rem;color:var(--text-muted);">{{ $user->email }}</div>
                    </div>
                </div>
                <div class="profile-progress">
                    <div class="profile-progress-bar">
                        <div class="profile-progress-fill" style="width:{{ $pct }}%;"></div>
                    </div>
                    <div class="profile-progress-label">
                        <span>Profile {{ $pct }}% complete</span>
                        <span>{{ $completed }}/{{ $total }}</span>
                    </div>
                </div>
                @if($pct < 100)
                    <a href="{{ route('customer.profile') }}"
                       class="btn btn-primary btn-full btn-sm" style="margin-top:12px;">
                        <i class="fas fa-pen"></i> Complete Profile
                    </a>
                @else
                    <div class="alert alert-success" style="margin-top:12px;margin-bottom:0;">
                        <i class="fas fa-check-circle"></i>
                        <span>Profile is complete!</span>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
