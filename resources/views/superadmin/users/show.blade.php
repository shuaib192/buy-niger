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
<div class="row">
    <div class="col-4">
        <div class="dashboard-card mb-4">
            <div class="dashboard-card-body text-center">
                <img src="{{ $user->avatar_url }}" class="rounded-circle mb-3" width="100" height="100">
                <h4>{{ $user->name }}</h4>
                <p class="text-muted">{{ $user->email }}</p>
                
                <span class="badge badge-{{ $user->role_id == 1 ? 'purple' : ($user->role_id == 3 ? 'orange' : 'blue') }} mb-3">
                    {{ $user->role->name }}
                </span>

                <div class="d-flex justify-content-center gap-2 mt-3">
                    <form action="{{ route($prefix.'users.ban', $user) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}">
                            {{ $user->is_active ? 'Ban User' : 'Activate User' }}
                        </button>
                    </form>
                    <a href="mailto:{{ $user->email }}" class="btn btn-sm btn-secondary">Email</a>
                </div>
            </div>
        </div>

        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3>Contact Info</h3>
            </div>
            <div class="dashboard-card-body">
                <p><strong>Phone:</strong> {{ $user->phone ?? 'N/A' }}</p>
                <p><strong>Address:</strong> {{ $user->address ?? 'N/A' }}</p>
                <p><strong>Joined:</strong> {{ $user->created_at->format('M d, Y') }}</p>
                <p><strong>Last Login:</strong> {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</p>
            </div>
        </div>

        @if($user->vendor)
        <div class="dashboard-card mb-4">
            <div class="dashboard-card-header">
                <h3>Vendor Profile</h3>
            </div>
            <div class="dashboard-card-body">
                <p><strong>Store:</strong> {{ $user->vendor->store_name }}</p>
                <div class="mb-3">
                    <strong>KYC:</strong> 
                    <span class="badge badge-{{ ($user->vendor->kyc_status == 'verified') ? 'success' : (($user->vendor->kyc_status == 'pending') ? 'warning' : 'secondary') }}">
                        {{ ucfirst($user->vendor->kyc_status ?? 'Not Submitted') }}
                    </span>
                </div>
                <a href="{{ route($prefix.'vendors.show', $user->vendor) }}" class="btn btn-outline-primary btn-sm w-100">
                    <i class="fas fa-store me-1"></i> Manage Vendor
                </a>
            </div>
        </div>
        @endif
    </div>

    <div class="col-8">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3>Recent Orders</h3>
            </div>
            <div class="dashboard-card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($user->orders()->latest()->take(10)->get() as $order)
                                <tr>
                                    <td>{{ $order->order_number }}</td>
                                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                                    <td><span class="badge badge-secondary">{{ ucfirst($order->status) }}</span></td>
                                    <td>₦{{ number_format($order->total) }}</td>
                                    <td>
                                        <a href="{{ route($prefix.'orders.show', $order) }}" class="btn btn-sm btn-link">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-3">No orders found.</td>
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
