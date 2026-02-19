@extends('layouts.app')

@section('title', 'Order ' . $order->order_number)
@section('page_title', 'Order ' . $order->order_number)

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@php
    $prefix = request()->is('admin*') ? 'admin.' : 'superadmin.';
@endphp

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Order {{ $order->order_number }}</h1>
        <a href="{{ route($prefix.'orders') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Orders
        </a>
    </div>

    <div class="row">
        <!-- Order info -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Order Items</h6>
                    <span class="badge badge-{{ $order->status_badge }}">{{ strtoupper($order->status) }}</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Vendor</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($item->product && $item->product->primary_image_url)
                                                <img src="{{ $item->product->primary_image_url }}" alt="" style="width: 40px; height: 40px; object-fit: cover;" class="mr-2 rounded">
                                            @endif
                                            <div>
                                                <strong>{{ $item->product_name }}</strong>
                                                @if($item->variant_name)
                                                    <br><small class="text-muted">{{ $item->variant_name }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $item->vendor->store_name }}</td>
                                    <td>₦{{ number_format($item->price, 2) }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>₦{{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-right">Subtotal</th>
                                    <td>₦{{ number_format($order->subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <th colspan="4" class="text-right">Shipping</th>
                                    <td>₦{{ number_format($order->shipping_cost, 2) }}</td>
                                </tr>
                                @if($order->discount > 0)
                                <tr>
                                    <th colspan="4" class="text-right text-danger">Discount</th>
                                    <td class="text-danger">-₦{{ number_format($order->discount, 2) }}</td>
                                </tr>
                                @endif
                                <tr class="bg-light">
                                    <th colspan="4" class="text-right"><strong>Total</strong></th>
                                    <td><strong>₦{{ number_format($order->total, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status History</h6>
                </div>
                <div class="card-body">
                    <div class="timeline shadow-sm p-3">
                        @forelse($order->statusHistory as $history)
                        <div class="mb-3 pl-3 border-left border-primary">
                            <div class="small text-muted">{{ $history->created_at->format('M d, Y h:i A') }}</div>
                            <strong>{{ strtoupper($history->status) }}</strong>
                            @if($history->notes)
                                <p class="mb-0">{{ $history->notes }}</p>
                            @endif
                            <div class="small text-gray-500">By: {{ $history->changed_by }}</div>
                        </div>
                        @empty
                            <p class="text-center text-muted">No status history yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer & Stats -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Customer Details</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <img class="img-profile rounded-circle" src="https://ui-avatars.com/api/?name={{ urlencode($order->user->name) }}&background=random" style="width: 60px;">
                        <h5 class="mt-2">{{ $order->user->name }}</h5>
                        <p class="text-muted">{{ $order->user->email }}</p>
                    </div>
                    <hr>
                    <h6>Shipping Address</h6>
                    @php $addr = $order->shipping_address ?? []; @endphp
                    <p class="small">
                        <strong>{{ $addr['name'] ?? 'N/A' }}</strong><br>
                        {{ $addr['address'] ?? '' }}<br>
                        {{ $addr['city'] ?? '' }}, {{ $addr['state'] ?? '' }}, {{ $addr['country'] ?? '' }}<br>
                        <i class="fas fa-phone"></i> {{ $addr['phone'] ?? '' }}
                    </p>
                </div>
            </div>

            <div class="card shadow mb-4 border-left-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }}">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Payment Details</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2"><strong>Status:</strong> <span class="badge badge-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }}">{{ strtoupper($order->payment_status) }}</span></div>
                    <div class="mb-2"><strong>Method:</strong> {{ $order->payment_method ?? 'N/A' }}</div>
                    <div class="mb-2"><strong>Reference:</strong> {{ $order->payment_reference ?? 'N/A' }}</div>
                    @if($order->paid_at)
                        <div class="mb-2"><strong>Paid At:</strong> {{ $order->paid_at->format('M d, Y h:i A') }}</div>
                    @endif
                </div>
            </div>

            <!-- Admin Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-dark text-white">
                    <h6 class="m-0 font-weight-bold">ADMIN OVERRIDE</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route($prefix.'orders.status', $order->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Change Overall Status</label>
                            <select name="status" class="form-control">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ $order->status == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                <option value="refunded" {{ $order->status == 'refunded' ? 'selected' : '' }}>Refunded</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Admin Note (Optional)</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger btn-block">Force Update Status</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
