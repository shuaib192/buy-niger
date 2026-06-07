{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    View: Super Admin Dashboard (Dynamic)
--}}
@extends('layouts.app')

@section('title', 'Super Admin Dashboard')
@section('page_title', 'Dashboard')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3>{{ number_format($stats['total_users']) }}</h3>
                <p>Total Users</p>
                <span class="stat-change up"><i class="fas fa-arrow-up"></i> 12% this month</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fas fa-store"></i>
            </div>
            <div class="stat-info">
                <h3>{{ number_format($stats['active_vendors']) }}</h3>
                <p>Active Vendors</p>
                <span class="stat-change up"><i class="fas fa-arrow-up"></i> 8 new</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon orange">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stat-info">
                <h3>{{ number_format($stats['total_orders']) }}</h3>
                <p>Total Orders</p>
                <span class="stat-change up"><i class="fas fa-arrow-up"></i> 23% growth</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon purple">
                <i class="fas fa-naira-sign"></i>
            </div>
            <div class="stat-info">
                <h3>₦{{ number_format($stats['total_revenue']) }}</h3>
                <p>Total Revenue</p>
                <span class="stat-change up"><i class="fas fa-arrow-up"></i> 18% this month</span>
            </div>
        </div>
    </div>

@php
    $prefix = request()->is('admin*') ? 'admin.' : 'superadmin.';
@endphp
    <!-- Dashboard Grid -->
    <div class="row g-4">
        <!-- Track Order Card -->
        <div class="col-12">
            <div class="dashboard-card bg-primary-50 border-primary-100">
                <div class="dashboard-card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <h3 class="h5 font-bold text-primary-900 mb-1">Track Order</h3>
                        <p class="text-primary-700 mb-0 text-sm">Enter an Order Number (e.g., BN-...) or Tracking ID to view details.</p>
                    </div>
                    <form action="{{ route($prefix.'track') }}" method="POST" class="d-flex gap-2" style="min-width: 400px;">
                        @csrf
                        <input type="text" name="order_number" class="form-control" placeholder="Enter Order # or Tracking ID" required>
                        <button type="submit" class="btn btn-primary white-space-nowrap">Track</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="dashboard-card col-8">
            <div class="dashboard-card-header">
                <h3>Recent Orders</h3>
                <a href="{{ route($prefix.'orders') }}" class="btn btn-sm btn-secondary">View All</a>
            </div>
            <div class="dashboard-card-body">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                                <tr>
                                    <td><strong>#{{ $order->order_number }}</strong></td>
                                    <td>{{ $order->user->name }}</td>
                                    <td>₦{{ number_format($order->total) }}</td>
                                    <td><span class="badge badge-{{ $order->status_badge }}">{{ ucfirst($order->status) }}</span></td>
                                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align: center; color: var(--secondary-500); padding: var(--spacing-md);">No orders found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- AI Status -->
        @if($prefix === 'superadmin.')
        <div class="dashboard-card col-4">
            <div class="dashboard-card-header">
                <h3>AI System Status</h3>
            </div>
            <div class="dashboard-card-body">
                <div style="display: flex; align-items: center; gap: var(--spacing-md); margin-bottom: var(--spacing-lg);">
                    <div style="width: 12px; height: 12px; background: var(--success); border-radius: 50%; animation: pulse 2s infinite;"></div>
                    <span style="font-weight: 600; color: var(--success);">Shadow Mode Active</span>
                </div>
                <div style="margin-bottom: var(--spacing-lg);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="font-size: 0.875rem; color: var(--secondary-600);">Pending Proposals</span>
                        <strong>3</strong>
                    </div>
                </div>
                <a href="{{ route($prefix.'ai') }}" class="btn btn-primary btn-full btn-sm">
                    <i class="fas fa-robot"></i> Manage AI
                </a>
            </div>
        </div>
        @endif

        <!-- Pending Vendors -->
        <div class="dashboard-card col-4">
            <div class="dashboard-card-header">
                <h3>Pending Vendors</h3>
                @if($stats['pending_vendors'] > 0)
                    <span class="badge badge-warning">{{ $stats['pending_vendors'] }}</span>
                @endif
            </div>
            <div class="dashboard-card-body">
                <div style="display: flex; flex-direction: column; gap: var(--spacing-md);">
                    @forelse($pendingVendors as $vendor)
                        <div style="display: flex; align-items: center; gap: var(--spacing-md);">
                            <img src="{{ $vendor->user->avatar_url }}" style="width: 40px; height: 40px; border-radius: 50%;">
                            <div style="flex: 1;">
                                <strong style="font-size: 0.875rem;">{{ $vendor->store_name }}</strong>
                                <div style="font-size: 0.75rem; color: var(--secondary-500);">Applied {{ $vendor->created_at->diffForHumans() }}</div>
                            </div>
                            <form action="{{ route($prefix.'vendors.status', $vendor) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-check"></i></button>
                            </form>
                        </div>
                    @empty
                        <p style="text-align: center; color: var(--secondary-500); padding: var(--spacing-md);">No pending vendors</p>
                    @endforelse
                </div>
                <a href="{{ route($prefix.'vendors') }}" class="btn btn-secondary btn-full btn-sm mt-3">View All Vendors</a>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="dashboard-card col-8">
            <div class="dashboard-card-header">
                <h3>Quick Actions</h3>
            </div>
            <div class="dashboard-card-body">
                <div class="quick-actions">
                    <button class="quick-action-btn">
                        <i class="fas fa-user-plus"></i>
                        <span>Add User</span>
                    </button>
                    <button class="quick-action-btn">
                        <i class="fas fa-store"></i>
                        <span>Add Vendor</span>
                    </button>
                    <button class="quick-action-btn">
                        <i class="fas fa-envelope"></i>
                        <span>Send Email</span>
                    </button>
                    <button class="quick-action-btn">
                        <i class="fas fa-bullhorn"></i>
                        <span>Campaign</span>
                    </button>
                    @if($prefix === 'superadmin.')
                    <a href="{{ route($prefix.'settings') }}" class="quick-action-btn">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
@endsection
