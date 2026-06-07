{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin
    View: Admin — Order Details — Premium v2.0
--}}
@extends('layouts.app')

@section('title', 'Order ' . $order->order_number)
@section('page_title', 'Order Detail')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@php
    $prefix = request()->is('admin*') ? 'admin.' : 'superadmin.';
@endphp

@section('content')
<div class="row g-4">
    <!-- Header with Back Button -->
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold text-dark mb-1" style="font-family: 'Outfit', sans-serif;">Order: #{{ $order->order_number }}</h2>
            <p class="text-muted small mb-0">Purchased on {{ $order->created_at->format('M d, Y h:i A') }}</p>
        </div>
        <a href="{{ route($prefix.'orders') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
            <i class="fas fa-arrow-left me-1"></i> Back to Orders
        </a>
    </div>

    <!-- Order items -->
    <div class="col-lg-8">
        <div class="dashboard-card mb-4">
            <div class="dashboard-card-header">
                <h3>Order Items & Summary</h3>
                @php
                    $statusBadge = match($order->status) {
                        'delivered', 'completed' => 'badge-success',
                        'pending' => 'badge-warning',
                        'cancelled' => 'badge-danger',
                        default => 'badge-info',
                    };
                @endphp
                <span class="badge {{ $statusBadge }} px-3 py-2 rounded-pill">{{ strtoupper($order->status) }}</span>
            </div>
            <div class="dashboard-card-body p-0">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="ps-4">Product Details</th>
                                <th>Storefront</th>
                                <th>Item Unit Price</th>
                                <th>Quantity</th>
                                <th class="text-end pe-4">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        @if($item->product && $item->product->primary_image_url)
                                            <div class="rounded-3 border overflow-hidden bg-light" style="width: 44px; height: 44px; flex-shrink: 0;">
                                                <img src="{{ $item->product->primary_image_url }}" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-semibold text-dark">{{ $item->product_name }}</div>
                                            @if($item->variant_name)
                                                <small class="text-muted">{{ $item->variant_name }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-dark small fw-medium"><i class="fas fa-store text-muted me-1"></i> {{ $item->vendor->store_name }}</span>
                                </td>
                                <td>
                                    <span class="text-dark small">₦{{ number_format($item->price, 2) }}</span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $item->quantity }}</span>
                                </td>
                                <td class="text-end pe-4 fw-bold text-dark">
                                    ₦{{ number_format($item->subtotal, 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Totals Calculation Block -->
                <div class="p-4 border-top bg-light d-flex justify-content-end text-end">
                    <div style="width: 250px;">
                        <div class="d-flex justify-content-between mb-2 small text-muted">
                            <span>Subtotal</span>
                            <span class="text-dark">₦{{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 small text-muted">
                            <span>Shipping Costs</span>
                            <span class="text-dark">₦{{ number_format($order->shipping_cost, 2) }}</span>
                        </div>
                        @if($order->discount > 0)
                        <div class="d-flex justify-content-between mb-2 small text-danger">
                            <span>Campaign Discount</span>
                            <span>-₦{{ number_format($order->discount, 2) }}</span>
                        </div>
                        @endif
                        <hr class="my-2">
                        <div class="d-flex justify-content-between text-dark fw-bold" style="font-size:1.1rem;">
                            <span>Grand Total</span>
                            <span class="text-indigo">₦{{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline / Status History -->
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3>Order Status Log Timeline</h3>
            </div>
            <div class="dashboard-card-body">
                <div class="d-flex flex-column gap-4 ps-2">
                    @forelse($order->statusHistory as $history)
                    <div class="position-relative ps-4" style="border-left: 2px solid var(--indigo);">
                        <div class="position-absolute rounded-circle bg-indigo" style="width: 10px; height: 10px; left: -6px; top: 5px;"></div>
                        <div class="d-flex flex-column">
                            <div class="text-muted mb-1" style="font-size:11px;"><i class="fas fa-clock me-1"></i> {{ $history->created_at->format('M d, Y h:i A') }}</div>
                            <strong class="text-dark text-capitalize small">Status updated to: {{ $history->status }}</strong>
                            @if($history->notes)
                                <p class="text-muted small mb-1 bg-light p-2 rounded-3 mt-1" style="line-height:1.4;">{{ $history->notes }}</p>
                            @endif
                            <div class="text-muted" style="font-size: 11px;">Author: <strong>{{ $history->changed_by }}</strong></div>
                        </div>
                    </div>
                    @empty
                        <p class="text-center text-muted py-3">No status logs recorded.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Customer details sidebar -->
    <div class="col-lg-4">
        <div class="dashboard-card mb-4">
            <div class="dashboard-card-header">
                <h3>Purchasing Customer</h3>
            </div>
            <div class="dashboard-card-body">
                <div class="text-center mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2 overflow-hidden bg-light border" style="width: 60px; height: 60px;">
                        <span class="fw-bold text-indigo" style="font-size: 1.5rem; font-family:'Outfit', sans-serif;">{{ strtoupper(substr($order->user->name, 0, 1)) }}</span>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">{{ $order->user->name }}</h5>
                    <p class="text-muted small mb-0">{{ $order->user->email }}</p>
                </div>
                <hr class="my-3" style="border-color: var(--border-color);">
                <h6 class="fw-bold text-dark small mb-2"><i class="fas fa-truck text-muted me-1"></i> Shipping Address</h6>
                @php $addr = $order->shipping_address ?? []; @endphp
                <div class="p-3 border rounded-3 bg-light" style="border-color: var(--border-color) !important;">
                    <strong class="text-dark small d-block mb-1">{{ $addr['name'] ?? 'N/A' }}</strong>
                    <span class="text-muted small d-block mb-2" style="line-height:1.4;">
                        {{ $addr['address'] ?? '' }}<br>
                        {{ $addr['city'] ?? '' }}, {{ $addr['state'] ?? '' }}, {{ $addr['country'] ?? '' }}
                    </span>
                    <span class="text-dark small fw-semibold"><i class="fas fa-phone text-muted me-1"></i> {{ $addr['phone'] ?? '' }}</span>
                </div>
            </div>
        </div>

        <!-- Payment details -->
        @php
            $payStatusClass = $order->payment_status == 'paid' ? 'badge-success' : 'badge-warning';
        @endphp
        <div class="dashboard-card mb-4" style="border-left: 5px solid {{ $order->payment_status == 'paid' ? '#10b981' : '#f59e0b' }};">
            <div class="dashboard-card-header">
                <h3>Payment Verification</h3>
            </div>
            <div class="dashboard-card-body">
                <div class="d-flex flex-column gap-3">
                    <div>
                        <span class="text-muted small d-block mb-1">Gateway Settlement Status</span>
                        <span class="badge {{ $payStatusClass }}">{{ strtoupper($order->payment_status) }}</span>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Settlement Method</span>
                        <strong class="text-dark small">{{ ucfirst($order->payment_method ?? 'N/A') }}</strong>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Transaction Reference</span>
                        <code class="text-primary fw-bold small">{{ $order->payment_reference ?? 'N/A' }}</code>
                    </div>
                    @if($order->paid_at)
                    <div>
                        <span class="text-muted small d-block">Captured Date</span>
                        <strong class="text-dark small">{{ $order->paid_at->format('M d, Y h:i A') }}</strong>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Admin Override Actions -->
        <div class="dashboard-card">
            <div class="dashboard-card-header bg-dark text-white">
                <h3 class="text-white"><i class="fas fa-circle-exclamation me-1"></i> ADMINISTRATIVE OVERRIDE</h3>
            </div>
            <div class="dashboard-card-body">
                <form action="{{ route($prefix.'orders.status', $order->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-dark small fw-semibold">Fulfillment Lifecycle State</label>
                        <select name="status" class="form-select">
                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ $order->status == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="refunded" {{ $order->status == 'refunded' ? 'selected' : '' }}>Refunded</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark small fw-semibold">Audit Override Notes</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Document the reason for this administrative override..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger w-100 rounded-pill fw-bold">
                        <i class="fas fa-arrow-right-arrow-left me-1"></i> Commit Force Transition
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
