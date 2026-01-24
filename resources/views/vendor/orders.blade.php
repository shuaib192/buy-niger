{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    View: Vendor Order List
--}}
@extends('layouts.app')

@section('title', 'Manage Orders')
@section('page_title', 'Orders')

@section('sidebar')
    @include('vendor.partials.sidebar')
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 font-bold text-secondary-900 mb-1">Orders</h2>
                <p class="text-secondary-500 mb-0">Manage customer orders and shipments.</p>
            </div>
            <a href="{{ route('vendor.orders.export') }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-file-csv mr-2"></i> Export CSV
            </a>
        </div>

        <div class="dashboard-card border-0 shadow-sm mb-4">
            <div class="dashboard-card-header bg-white border-bottom border-light py-0">
                <ul class="nav nav-tabs border-0 card-header-tabs" style="margin-bottom: -1px;">
                    <li class="nav-item">
                        <a class="nav-link py-3 px-4 font-bold border-0 {{ $status == 'all' ? 'active border-bottom border-primary text-primary' : 'text-secondary-500' }}" href="{{ route('vendor.orders', ['status' => 'all']) }}">
                            All <span class="badge badge-light ml-2">{{ $counts['all'] }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-3 px-4 font-bold border-0 {{ $status == 'pending' ? 'active border-bottom border-primary text-primary' : 'text-secondary-500' }}" href="{{ route('vendor.orders', ['status' => 'pending']) }}">
                            Pending <span class="badge badge-warning-soft ml-2">{{ $counts['pending'] }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-3 px-4 font-bold border-0 {{ $status == 'processing' ? 'active border-bottom border-primary text-primary' : 'text-secondary-500' }}" href="{{ route('vendor.orders', ['status' => 'processing']) }}">
                            Processing <span class="badge badge-info-soft ml-2">{{ $counts['processing'] }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-3 px-4 font-bold border-0 {{ $status == 'shipped' ? 'active border-bottom border-primary text-primary' : 'text-secondary-500' }}" href="{{ route('vendor.orders', ['status' => 'shipped']) }}">
                            Shipped <span class="badge badge-primary-soft ml-2">{{ $counts['shipped'] }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-3 px-4 font-bold border-0 {{ $status == 'delivered' ? 'active border-bottom border-primary text-primary' : 'text-secondary-500' }}" href="{{ route('vendor.orders', ['status' => 'delivered']) }}">
                            Delivered <span class="badge badge-success-soft ml-2">{{ $counts['delivered'] }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-3 px-4 font-bold border-0 {{ $status == 'cancelled' ? 'active border-bottom border-primary text-primary' : 'text-secondary-500' }}" href="{{ route('vendor.orders', ['status' => 'cancelled']) }}">
                            Cancelled <span class="badge badge-danger-soft ml-2">{{ $counts['cancelled'] }}</span>
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="dashboard-card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 py-3 px-4 text-xs font-bold uppercase text-secondary-500">Order</th>
                                <th class="border-0 py-3 px-4 text-xs font-bold uppercase text-secondary-500">Date</th>
                                <th class="border-0 py-3 px-4 text-xs font-bold uppercase text-secondary-500">Customer</th>
                                <th class="border-0 py-3 px-4 text-xs font-bold uppercase text-secondary-500">Amount</th>
                                <th class="border-0 py-3 px-4 text-xs font-bold uppercase text-secondary-500">Status</th>
                                <th class="border-0 py-3 px-4 text-right text-xs font-bold uppercase text-secondary-500">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orderItems as $item)
                            <tr>
                                <td class="py-3 px-4 align-middle font-bold text-secondary-900">#{{ $item->order->order_number }}</td>
                                <td class="py-3 px-4 align-middle text-secondary-500">{{ $item->created_at->format('M d, Y') }}<br><small>{{ $item->created_at->format('h:i A') }}</small></td>
                                <td class="py-3 px-4 align-middle">
                                    <div class="d-flex align-items-center">
                                        <div class="d-flex align-items-center justify-content-center bg-light rounded-circle mr-3" style="width: 32px; height: 32px;">
                                            <i class="fas fa-user text-secondary-400 text-xs"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-secondary-900 text-sm">{{ $item->order->user->name ?? 'Guest' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 align-middle font-bold text-secondary-900">₦{{ number_format($item->subtotal) }}</td>
                                <td class="py-3 px-4 align-middle">
                                    <span class="badge badge-{{ $item->status == 'delivered' ? 'success' : ($item->status == 'cancelled' ? 'danger' : ($item->status == 'processing' ? 'info' : ($item->status == 'shipped' ? 'primary' : 'warning'))) }}-soft px-3 py-1 rounded-pill">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 align-middle text-right">
                                    <a href="{{ route('vendor.orders.show', $item->id) }}" class="btn btn-sm btn-outline-primary shadow-sm rounded-pill px-3">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="empty-state">
                                        <div class="icon-box bg-light text-secondary-300 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                                            <i class="fas fa-box-open fa-2x"></i>
                                        </div>
                                        <h4 class="h5 font-bold text-secondary-900 mb-2">No orders found</h4>
                                        <p class="text-secondary-500 mb-0">When you receive orders, they will appear here.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            @if($orderItems->hasPages())
            <div class="dashboard-card-footer bg-white border-top p-4">
                {{ $orderItems->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
