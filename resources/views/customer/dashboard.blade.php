{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    View: Customer Dashboard
--}}
@extends('layouts.app')

@section('title', 'My Account')
@section('page_title', 'My Account')

@section('sidebar')
    @include('customer.partials.sidebar')
@endsection

@section('content')
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['total_orders'] }}</h3>
                <p>Total Orders</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon orange">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['pending_orders'] }}</h3>
                <p>Pending Orders</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fas fa-heart"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['wishlist_count'] }}</h3>
                <p>Wishlist Items</p>
            </div>
        </div>
        <div class="stat-card">
            <a href="{{ route('customer.reviews.index') }}" style="display: flex; gap: 16px; align-items: center; width: 100%; text-decoration: none; color: inherit;">
                <div class="stat-icon purple">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stats['reviews_given'] }}</h3>
                    <p>Reviews Given</p>
                </div>
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="dashboard-card h-100">
                <div class="dashboard-card-header">
                    <h3>Recent Orders</h3>
                    <a href="{{ route('orders.index') }}" class="btn btn-sm btn-secondary">View All</a>
                </div>
                <div class="dashboard-card-body">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                    @php
                                        $statusColors = [
                                            'pending' => 'warning',
                                            'paid' => 'info',
                                            'processing' => 'primary',
                                            'shipped' => 'info',
                                            'delivered' => 'success',
                                            'cancelled' => 'danger',
                                        ];
                                    @endphp
                                    <tr>
                                        <td><strong>{{ $order->order_number }}</strong></td>
                                        <td>{{ $order->items->count() }} item(s)</td>
                                        <td>₦{{ number_format($order->total) }}</td>
                                        <td><span class="badge badge-{{ $statusColors[$order->status] ?? 'secondary' }}">{{ ucfirst($order->status) }}</span></td>
                                        <td><a href="{{ route('orders.detail', $order->order_number) }}" class="btn btn-sm btn-primary">View</a></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" style="text-align: center; padding: var(--spacing-xl); color: var(--secondary-500);">
                                            <i class="fas fa-shopping-bag" style="font-size: 2rem; margin-bottom: var(--spacing-md); display: block;"></i>
                                            No orders yet. <a href="{{ route('catalog') }}">Start shopping!</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="dashboard-card h-100">
                <div class="dashboard-card-header">
                    <h3>Quick Links</h3>
                </div>
                <div class="dashboard-card-body">
                    <div style="display: flex; flex-direction: column; gap: var(--spacing-md);">
                        <a href="{{ route('catalog') }}" class="btn btn-primary btn-full">
                            <i class="fas fa-shopping-cart"></i> Continue Shopping
                        </a>
                        <a href="{{ route('orders.index') }}" class="btn btn-secondary btn-full">
                            <i class="fas fa-truck"></i> Track Orders
                        </a>
                        <a href="{{ route('customer.profile') }}" class="btn btn-secondary btn-full">
                            <i class="fas fa-user"></i> Edit Profile
                        </a>
                        <a href="{{ route('customer.addresses') }}" class="btn btn-secondary btn-full">
                            <i class="fas fa-map-marker-alt"></i> Manage Addresses
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
