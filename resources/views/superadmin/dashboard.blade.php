@extends('layouts.app')

@section('title', 'Super Admin Dashboard')
@section('page_title', 'Dashboard')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
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

    <div class="row g-4">
        <!-- Track Order -->
        <div class="col-12">
            <div class="dashboard-card bg-primary-soft">
                <div class="dashboard-card-body d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 p-4">
                    <div>
                        <h3 class="h5 font-bold text-primary-900 mb-1">Track Order</h3>
                        <p class="text-secondary-600 text-sm mb-0">Enter an Order Number or Tracking ID</p>
                    </div>
                    <form action="{{ route($prefix.'track') }}" method="POST" class="d-flex flex-column flex-sm-row gap-2" style="max-width: 500px;">
                        @csrf
                        <input type="text" name="order_number" class="form-control" placeholder="Enter Order # or Tracking ID" required>
                        <button type="submit" class="btn btn-premium">Track</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="col-lg-8">
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h3>Recent Orders</h3>
                    <a href="{{ route($prefix.'orders') }}" class="btn btn-sm btn-soft">View All</a>
                </div>
                <div class="dashboard-card-body p-0">
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
                                        <td class="font-semibold">#{{ $order->order_number }}</td>
                                        <td>{{ optional($order->user)->name ?? 'Unknown User' }}</td>
                                        <td class="font-semibold">₦{{ number_format($order->total) }}</td>
                                        <td><span class="badge badge-{{ $order->status_badge }}">{{ ucfirst($order->status) }}</span></td>
                                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-secondary-500">No orders found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Status + Pending Vendors -->
        <div class="col-lg-4 d-flex flex-column gap-4">
            @if($prefix === 'superadmin.')
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h3>AI System Status</h3>
                </div>
                <div class="dashboard-card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="d-inline-block" style="width:12px;height:12px;background:var(--success);border-radius:50%;box-shadow:0 0 6px rgba(16,185,129,0.6);"></span>
                        <span class="font-semibold text-success">Shadow Mode Active</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-sm text-secondary-600">Pending Proposals</span>
                        <strong>3</strong>
                    </div>
                    <a href="{{ route($prefix.'ai') }}" class="btn btn-premium btn-sm btn-full">
                        <i class="fas fa-robot"></i> Manage AI
                    </a>
                </div>
            </div>
            @endif

            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h3>Pending Vendors</h3>
                    @if($stats['pending_vendors'] > 0)
                        <span class="badge badge-warning">{{ $stats['pending_vendors'] }}</span>
                    @endif
                </div>
                <div class="dashboard-card-body">
                    @forelse($pendingVendors as $vendor)
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <img src="{{ optional($vendor->user)->avatar_url ?? asset('images/default-avatar.png') }}" class="rounded-circle" style="width:40px;height:40px;object-fit:cover;">
                            <div class="flex-fill">
                                <div class="font-semibold text-sm">{{ $vendor->store_name }}</div>
                                <div class="text-xs text-secondary-500">Applied {{ $vendor->created_at->diffForHumans() }}</div>
                            </div>
                            <form action="{{ route($prefix.'vendors.status', $vendor) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-check"></i></button>
                            </form>
                        </div>
                    @empty
                        <p class="text-center text-secondary-500 py-3 mb-0">No pending vendors</p>
                    @endforelse
                    <a href="{{ route($prefix.'vendors') }}" class="btn btn-soft btn-sm btn-full mt-2">View All Vendors</a>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-12">
            <div class="dashboard-card">
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
    </div>
@endsection
