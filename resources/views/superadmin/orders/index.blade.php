@extends('layouts.app')

@section('title', 'Order Management')
@section('page_title', 'Order Management')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
@php
    $prefix = request()->is('admin*') ? 'admin.' : 'superadmin.';
@endphp
<div class="container-fluid">
    <div class="row mb-4 g-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0 text-gray-800">Order Management</h1>
        </div>
        <div class="col-md-6 text-right">
            <div class="btn-group">
                <a href="{{ route($prefix.'orders', ['status' => 'pending']) }}" class="btn btn-sm btn-outline-warning">Pending</a>
                <a href="{{ route($prefix.'orders', ['status' => 'paid']) }}" class="btn btn-sm btn-outline-success">Paid</a>
                <a href="{{ route($prefix.'orders', ['status' => 'processing']) }}" class="btn btn-sm btn-outline-primary">Processing</a>
                <a href="{{ route($prefix.'orders', ['status' => 'shipped']) }}" class="btn btn-sm btn-outline-info">Shipped</a>
                <a href="{{ route($prefix.'orders', ['status' => 'delivered']) }}" class="btn btn-sm btn-success">Delivered</a>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">All Orders</h6>
            <form action="{{ route($prefix.'orders') }}" method="GET" class="form-inline">
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="Order ID..." value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="ordersTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td><strong>{{ $order->order_number }}</strong></td>
                            <td>{{ $order->user->name }}</td>
                            <td>₦{{ number_format($order->total, 2) }}</td>
                            <td>
                                <span class="badge badge-{{ $order->status_badge }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                                @if($order->payment_status == 'paid')
                                    <span class="badge badge-success">PAID</span>
                                @endif
                            </td>
                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route($prefix.'orders.show', $order->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
