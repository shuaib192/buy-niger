{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin
    View: Admin — Order Management — Premium v2.0
--}}
@extends('layouts.app')
@section('title', 'Order Management')
@section('page_title', 'Order Management')
@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@push('styles')
<style>
.filters-bar {
    display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
    padding: 14px 20px; background: var(--surface); border-bottom: 1px solid var(--border-color);
}
.filter-tabs { display: flex; gap: 4px; flex-wrap: wrap; flex: 1; }
.filter-tab {
    padding: 5px 12px; border-radius: 8px; font-size: .8rem; font-weight: 600;
    color: var(--text-secondary); background: white; border: 1.5px solid var(--border-color);
    text-decoration: none; transition: all .15s;
}
.filter-tab:hover { border-color: #4f46e5; color: #4f46e5; }
.filter-tab.is-all.active     { background:#4f46e5;border-color:#4f46e5;color:white; }
.filter-tab.is-pending.active { background:#f59e0b;border-color:#f59e0b;color:white; }
.filter-tab.is-paid.active    { background:#0ea5e9;border-color:#0ea5e9;color:white; }
.filter-tab.is-processing.active { background:#8b5cf6;border-color:#8b5cf6;color:white; }
.filter-tab.is-shipped.active    { background:#06b6d4;border-color:#06b6d4;color:white; }
.filter-tab.is-delivered.active  { background:#10b981;border-color:#10b981;color:white; }
.filter-tab.is-cancelled.active  { background:#f43f5e;border-color:#f43f5e;color:white; }
.search-group {
    display: flex; gap: 0; border: 1.5px solid var(--border-color);
    border-radius: 10px; overflow: hidden;
}
.search-group .form-control { border: none; border-radius: 0; font-size: .8125rem; min-width: 180px; }
.search-group .form-control:focus { box-shadow: none; }
.search-group .btn { border-radius: 0; padding: 9px 12px; }
.customer-cell { display: flex; align-items: center; gap: 8px; }
.cust-avatar {
    width: 30px; height: 30px; border-radius: 8px;
    background: linear-gradient(135deg, #4f46e5, #8b5cf6);
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: .75rem; font-weight: 700;
}
.order-amount { font-weight: 700; font-size: .9rem; color: var(--text-primary); }
</style>
@endpush

@section('content')
@php $prefix = request()->is('admin*') ? 'admin.' : 'superadmin.'; @endphp

<div class="dashboard-card">
    <div class="dashboard-card-header">
        <div>
            <h3><i class="fas fa-bag-shopping" style="color:#10b981;margin-right:8px;"></i>All Orders</h3>
            <div style="font-size:.8rem;color:var(--text-muted);margin-top:2px;">
                View and manage platform-wide orders.
            </div>
        </div>
        <a href="{{ route($prefix.'orders.export', request()->all()) }}"
           class="btn btn-sm btn-success">
            <i class="fas fa-file-csv"></i> Export
        </a>
    </div>

    <div class="filters-bar">
        <div class="filter-tabs">
            <a href="{{ route($prefix.'orders') }}" class="filter-tab is-all {{ !request('status') ? 'active' : '' }}">All</a>
            <a href="{{ route($prefix.'orders', ['status'=>'pending']) }}"    class="filter-tab is-pending {{ request('status')=='pending' ? 'active':'' }}">Pending</a>
            <a href="{{ route($prefix.'orders', ['status'=>'paid']) }}"       class="filter-tab is-paid {{ request('status')=='paid' ? 'active':'' }}">Paid</a>
            <a href="{{ route($prefix.'orders', ['status'=>'processing']) }}" class="filter-tab is-processing {{ request('status')=='processing' ? 'active':'' }}">Processing</a>
            <a href="{{ route($prefix.'orders', ['status'=>'shipped']) }}"    class="filter-tab is-shipped {{ request('status')=='shipped' ? 'active':'' }}">Shipped</a>
            <a href="{{ route($prefix.'orders', ['status'=>'delivered']) }}"  class="filter-tab is-delivered {{ request('status')=='delivered' ? 'active':'' }}">Delivered</a>
            <a href="{{ route($prefix.'orders', ['status'=>'cancelled']) }}"  class="filter-tab is-cancelled {{ request('status')=='cancelled' ? 'active':'' }}">Cancelled</a>
        </div>
        <form action="{{ route($prefix.'orders') }}" method="GET">
            @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
            <div class="search-group">
                <input type="text" name="search" class="form-control"
                       placeholder="Order ID, customer..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Amount</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    @php
                        $statusMap = ['pending'=>'warning','paid'=>'info','processing'=>'primary','shipped'=>'info','delivered'=>'success','cancelled'=>'danger'];
                    @endphp
                    <tr>
                        <td><strong>#{{ $order->order_number }}</strong></td>
                        <td>
                            <div class="customer-cell">
                                <div class="cust-avatar">
                                    {{ strtoupper(substr($order->user->name ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight:600;font-size:.8125rem;">{{ $order->user->name ?? '—' }}</div>
                                    <div style="font-size:.7rem;color:var(--text-muted);">{{ $order->user->email ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:.8125rem;">{{ $order->items->count() }} item(s)</td>
                        <td><span class="order-amount">₦{{ number_format($order->total, 2) }}</span></td>
                        <td>
                            @if($order->payment_status == 'paid')
                                <span class="badge badge-success"><i class="fas fa-check" style="font-size:.55rem;"></i> Paid</span>
                            @else
                                <span class="badge badge-warning">Unpaid</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-{{ $statusMap[$order->status] ?? 'secondary' }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td style="font-size:.8125rem;color:var(--text-secondary);">
                            {{ $order->created_at->format('d M Y') }}
                        </td>
                        <td>
                            <a href="{{ route($prefix.'orders.show', $order->id) }}"
                               class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <p>No orders found.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:14px 20px;">
        {{ $orders->appends(request()->query())->links() }}
    </div>
</div>
@endsection
