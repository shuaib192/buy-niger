{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin
    View: Admin — User Details — Premium v2.0
--}}
@extends('layouts.app')

@section('title', 'User Details')
@section('page_title', 'User Details')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
@php
    $prefix = request()->is('admin*') ? 'admin.' : 'superadmin.';
@endphp

<div class="row g-4">
    <!-- User Quick Details Sidebar -->
    <div class="col-lg-4">
        <div class="dashboard-card text-center mb-4">
            <div class="dashboard-card-body py-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3 overflow-hidden bg-light border" style="width: 100px; height: 100px;">
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <span class="fw-bold text-indigo" style="font-size: 2.5rem; font-family:'Outfit', sans-serif;">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    @endif
                </div>
                <h4 class="fw-bold text-dark mb-1">{{ $user->name }}</h4>
                <p class="text-muted small mb-3">{{ $user->email }}</p>
                
                @php
                    $roleClass = $user->role_id == 1 ? 'badge-danger' : ($user->role_id == 3 ? 'badge-success' : 'badge-info');
                @endphp
                <span class="badge {{ $roleClass }} px-3 py-2 rounded-pill mb-3">
                    <i class="fas fa-user-tag me-1"></i> {{ $user->role->name }}
                </span>

                <div class="d-flex justify-content-center gap-2 mt-2">
                    <form action="{{ route($prefix.'users.ban', $user) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-outline-danger' : 'btn-outline-success' }} rounded-pill px-3">
                            <i class="fas {{ $user->is_active ? 'fa-ban' : 'fa-circle-check' }} me-1"></i>
                            {{ $user->is_active ? 'Suspend Account' : 'Activate Account' }}
                        </button>
                    </form>
                    <a href="mailto:{{ $user->email }}" class="btn btn-sm btn-secondary rounded-pill px-3">
                        <i class="fas fa-envelope me-1"></i> Email
                    </a>
                </div>
            </div>
        </div>

        <div class="dashboard-card mb-4">
            <div class="dashboard-card-header">
                <h3>Contact & Metadata</h3>
            </div>
            <div class="dashboard-card-body">
                <div class="d-flex flex-column gap-3">
                    <div>
                        <span class="text-muted small d-block">Telephone Number</span>
                        <strong class="text-dark small">{{ $user->phone ?? 'N/A' }}</strong>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Physical Address</span>
                        <strong class="text-dark small">{{ $user->address ?? 'N/A' }}</strong>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Joined Platform</span>
                        <strong class="text-dark small">{{ $user->created_at->format('M d, Y') }} ({{ $user->created_at->diffForHumans() }})</strong>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Last Login Trace</span>
                        <strong class="text-dark small">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never logged in' }}</strong>
                    </div>
                </div>
            </div>
        </div>

        @if($user->vendor)
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3>Linked Vendor Profile</h3>
            </div>
            <div class="dashboard-card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="topbar-user-avatar" style="width: 40px; height: 40px; font-size: 1rem;">
                        {{ strtoupper(substr($user->vendor->store_name ?? 'S', 0, 1)) }}
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">{{ $user->vendor->store_name }}</h6>
                        <span class="text-muted small">Store ID: #{{ $user->vendor->id }}</span>
                    </div>
                </div>
                <div class="mb-3">
                    <span class="text-muted small d-block">KYC Status</span>
                    @php
                        $kycBadge = ($user->vendor->kyc_status == 'verified') ? 'badge-success' : (($user->vendor->kyc_status == 'pending') ? 'badge-warning' : 'badge-secondary');
                    @endphp
                    <span class="badge {{ $kycBadge }} mt-1">
                        {{ ucfirst($user->vendor->kyc_status ?? 'Not Submitted') }}
                    </span>
                </div>
                <a href="{{ route($prefix.'vendors.show', $user->vendor) }}" class="btn btn-outline-primary btn-sm w-100 rounded-pill mt-1">
                    <i class="fas fa-store me-1"></i> Manage Storefront
                </a>
            </div>
        </div>
        @endif
    </div>

    <!-- Orders Lists -->
    <div class="col-lg-8">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <div>
                    <h3 class="mb-1">Recent Purchasing Activity</h3>
                    <p class="text-muted small mb-0">List of the last 10 order transactions generated by this customer.</p>
                </div>
            </div>
            <div class="dashboard-card-body p-0">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="ps-4">Order Code</th>
                                <th>Submission Date</th>
                                <th>Fulfillment Status</th>
                                <th>Gross Total</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($user->orders()->latest()->take(10)->get() as $order)
                                <tr>
                                    <td class="ps-4">
                                        <code class="text-primary fw-bold">#{{ $order->order_number }}</code>
                                    </td>
                                    <td>
                                        <span class="text-dark small">{{ $order->created_at->format('M d, Y') }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $orderBadge = match($order->status) {
                                                'delivered', 'completed' => 'badge-success',
                                                'pending' => 'badge-warning',
                                                'cancelled' => 'badge-danger',
                                                default => 'badge-info',
                                            };
                                        @endphp
                                        <span class="badge {{ $orderBadge }}">{{ ucfirst($order->status) }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark">₦{{ number_format($order->total, 2) }}</span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route($prefix.'orders.show', $order) }}" class="btn btn-sm btn-outline-indigo px-3 rounded-pill">
                                            <i class="fas fa-eye me-1"></i> View Order
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fas fa-cart-shopping fa-3x mb-3 text-muted"></i>
                                        <h5 class="text-muted">No Orders Found</h5>
                                        <p class="text-muted small">This user account has not processed any purchases yet.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
