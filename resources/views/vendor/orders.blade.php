{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    View: Vendor Order List (Premium)
--}}
@extends('layouts.app')

@section('title', 'Manage Orders')
@section('page_title', 'Orders')

@section('sidebar')
    @include('vendor.partials.sidebar')
@endsection

@section('content')
<div class="orders-page">
    {{-- Page Header --}}
    <div class="page-header-premium">
        <div>
            <h1 class="page-title">Order Management</h1>
            <p class="page-subtitle">Track, manage and fulfill customer orders.</p>
        </div>
        <a href="{{ route('vendor.orders.export') }}" class="btn-premium btn-premium-outline">
            <i class="fas fa-file-csv"></i> Export CSV
        </a>
    </div>

    {{-- Summary Cards --}}
    <div class="row mb-4 g-3">
        <div class="col-6 col-md-3">
            <div class="order-stat-card">
                <div class="stat-icon bg-blue-subtle"><i class="fas fa-layer-group"></i></div>
                <div class="stat-value">{{ $counts['all'] }}</div>
                <div class="stat-label">Total Orders</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="order-stat-card">
                <div class="stat-icon bg-amber-subtle"><i class="fas fa-hourglass-half"></i></div>
                <div class="stat-value">{{ $counts['pending'] }}</div>
                <div class="stat-label">Pending</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="order-stat-card">
                <div class="stat-icon bg-indigo-subtle"><i class="fas fa-shipping-fast"></i></div>
                <div class="stat-value">{{ $counts['shipped'] ?? 0 }}</div>
                <div class="stat-label">Shipped</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="order-stat-card">
                <div class="stat-icon bg-emerald-subtle"><i class="fas fa-check-circle"></i></div>
                <div class="stat-value">{{ $counts['delivered'] }}</div>
                <div class="stat-label">Delivered</div>
            </div>
        </div>
    </div>

    {{-- Status Filter Tabs --}}
    <div class="premium-card">
        <div class="filter-tabs-bar">
            @php
                $tabs = [
                    'all' => ['label' => 'All', 'count' => $counts['all'], 'color' => ''],
                    'pending' => ['label' => 'Pending', 'count' => $counts['pending'], 'color' => 'amber'],
                    'processing' => ['label' => 'Processing', 'count' => $counts['processing'], 'color' => 'blue'],
                    'shipped' => ['label' => 'Shipped', 'count' => $counts['shipped'] ?? 0, 'color' => 'indigo'],
                    'delivered' => ['label' => 'Delivered', 'count' => $counts['delivered'], 'color' => 'emerald'],
                    'cancelled' => ['label' => 'Cancelled', 'count' => $counts['cancelled'] ?? 0, 'color' => 'red'],
                ];
            @endphp
            @foreach($tabs as $key => $tab)
                <a href="{{ route('vendor.orders', ['status' => $key]) }}" 
                   class="filter-tab {{ $status == $key ? 'active' : '' }}">
                    {{ $tab['label'] }}
                    <span class="tab-count {{ $tab['color'] ? 'bg-'.$tab['color'].'-subtle text-'.$tab['color'] : '' }}">{{ $tab['count'] }}</span>
                </a>
            @endforeach
        </div>

        {{-- Orders Table --}}
        <div class="table-responsive">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Product</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orderItems as $item)
                    <tr class="table-row-hover">
                        <td>
                            <span class="order-number">#{{ $item->order->order_number ?? 'N/A' }}</span>
                        </td>
                        <td>
                            <div class="date-cell">
                                <span class="date-main">{{ $item->created_at->format('M d, Y') }}</span>
                                <span class="date-sub">{{ $item->created_at->format('h:i A') }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="customer-cell">
                                <div class="customer-avatar">{{ substr($item->order->user->name ?? 'G', 0, 1) }}</div>
                                <span class="customer-name">{{ $item->order->user->name ?? 'Guest' }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="product-cell">
                                @if($item->product && $item->product->primary_image_url)
                                    <img src="{{ $item->product->primary_image_url }}" alt="" class="product-thumb">
                                @else
                                    <div class="product-thumb-placeholder"><i class="fas fa-box"></i></div>
                                @endif
                                <span class="product-name-text">{{ Str::limit($item->product_name ?? ($item->product->name ?? 'N/A'), 22) }}</span>
                            </div>
                        </td>
                        <td><span class="amount-cell">₦{{ number_format($item->subtotal) }}</span></td>
                        <td>
                            @php
                                $badgeMap = [
                                    'delivered' => 'badge-emerald',
                                    'cancelled' => 'badge-red',
                                    'processing' => 'badge-blue',
                                    'shipped' => 'badge-indigo',
                                    'pending' => 'badge-amber',
                                ];
                            @endphp
                            <span class="status-badge {{ $badgeMap[$item->status] ?? 'badge-gray' }}">
                                <span class="badge-dot"></span>{{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td class="text-right">
                            <a href="{{ route('vendor.orders.show', $item->id) }}" class="btn-action">
                                View <i class="fas fa-arrow-right"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state-premium">
                                <div class="empty-icon"><i class="fas fa-box-open"></i></div>
                                <h4>No orders found</h4>
                                <p>When you receive orders, they will appear here.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($orderItems->hasPages())
        <div class="pagination-bar">
            {{ $orderItems->links() }}
        </div>
        @endif
    </div>
</div>

<style>
    .orders-page { animation: fadeInUp 0.4s ease; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }

    .page-header-premium { display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 12px; }
    .page-title { font-size: 24px; font-weight: 800; color: #0f172a; margin: 0 0 4px; letter-spacing: -0.02em; }
    .page-subtitle { color: #64748b; font-size: 14px; margin: 0; font-weight: 500; }
    .btn-premium { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 12px; font-weight: 700; font-size: 13px; cursor: pointer; transition: all 0.2s ease; text-decoration: none; border: none; }
    .btn-premium-outline { background: white; color: #475569; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
    .btn-premium-outline:hover { background: #f8fafc; border-color: #cbd5e1; color: #1e293b; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.06); }

    /* Stat Cards */
    .order-stat-card { background: white; border: 1px solid #f1f5f9; border-radius: 16px; padding: 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.03); transition: all 0.25s ease; }
    .order-stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.06); }
    .stat-icon { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 16px; margin-bottom: 12px; }
    .bg-blue-subtle { background: #eff6ff; color: #2563eb; }
    .bg-amber-subtle { background: #fffbeb; color: #d97706; }
    .bg-indigo-subtle { background: #eef2ff; color: #4f46e5; }
    .bg-emerald-subtle { background: #ecfdf5; color: #059669; }
    .bg-red-subtle { background: #fef2f2; color: #dc2626; }
    .stat-value { font-size: 26px; font-weight: 800; color: #0f172a; line-height: 1; margin-bottom: 4px; }
    .stat-label { font-size: 12px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }

    /* Premium Card */
    .premium-card { background: white; border: 1px solid #f1f5f9; border-radius: 20px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.03); }

    /* Filter Tabs */
    .filter-tabs-bar { display: flex; gap: 4px; padding: 12px 16px; border-bottom: 1px solid #f1f5f9; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .filter-tab { display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 10px; font-size: 13px; font-weight: 600; color: #64748b; text-decoration: none; transition: all 0.2s ease; white-space: nowrap; border: 1px solid transparent; }
    .filter-tab:hover { background: #f8fafc; color: #1e293b; }
    .filter-tab.active { background: #0066FF; color: white; border-color: #0066FF; box-shadow: 0 2px 8px rgba(0,102,255,0.25); }
    .filter-tab.active .tab-count { background: rgba(255,255,255,0.25); color: white; }
    .tab-count { font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 20px; background: #f1f5f9; color: #64748b; }

    /* Premium Table */
    .premium-table { width: 100%; border-collapse: collapse; }
    .premium-table thead th { background: #fafbfc; color: #64748b; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; padding: 14px 16px; border-bottom: 1px solid #f1f5f9; white-space: nowrap; }
    .premium-table tbody td { padding: 16px; border-bottom: 1px solid #f8fafc; vertical-align: middle; font-size: 14px; }
    .table-row-hover { transition: background 0.15s ease; }
    .table-row-hover:hover { background: #fafbfc; }

    .order-number { font-weight: 700; color: #0f172a; font-family: 'JetBrains Mono', 'Fira Code', monospace; font-size: 13px; }
    .date-cell { display: flex; flex-direction: column; }
    .date-main { font-size: 13px; color: #334155; font-weight: 600; }
    .date-sub { font-size: 11px; color: #94a3b8; }

    .customer-cell { display: flex; align-items: center; gap: 10px; }
    .customer-avatar { width: 32px; height: 32px; border-radius: 10px; background: linear-gradient(135deg, #667eea, #764ba2); color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 13px; flex-shrink: 0; }
    .customer-name { font-weight: 600; color: #1e293b; font-size: 13px; }

    .product-cell { display: flex; align-items: center; gap: 10px; }
    .product-thumb { width: 36px; height: 36px; border-radius: 8px; object-fit: cover; border: 1px solid #f1f5f9; flex-shrink: 0; }
    .product-thumb-placeholder { width: 36px; height: 36px; border-radius: 8px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 14px; flex-shrink: 0; }
    .product-name-text { font-size: 13px; color: #475569; font-weight: 500; }

    .amount-cell { font-weight: 700; color: #0f172a; font-size: 14px; }

    /* Status Badges */
    .status-badge { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; }
    .badge-dot { width: 6px; height: 6px; border-radius: 50%; }
    .badge-emerald { background: #ecfdf5; color: #059669; } .badge-emerald .badge-dot { background: #059669; }
    .badge-red { background: #fef2f2; color: #dc2626; } .badge-red .badge-dot { background: #dc2626; }
    .badge-blue { background: #eff6ff; color: #2563eb; } .badge-blue .badge-dot { background: #2563eb; }
    .badge-indigo { background: #eef2ff; color: #4f46e5; } .badge-indigo .badge-dot { background: #4f46e5; }
    .badge-amber { background: #fffbeb; color: #d97706; } .badge-amber .badge-dot { background: #d97706; }
    .badge-gray { background: #f1f5f9; color: #475569; } .badge-gray .badge-dot { background: #475569; }

    .btn-action { display: inline-flex; align-items: center; gap: 6px; color: #0066FF; font-size: 13px; font-weight: 700; text-decoration: none; padding: 6px 14px; border-radius: 8px; transition: all 0.2s ease; }
    .btn-action:hover { background: #eff6ff; color: #004ecc; }
    .btn-action i { font-size: 10px; transition: transform 0.2s ease; }
    .btn-action:hover i { transform: translateX(3px); }

    /* Empty State */
    .empty-state-premium { text-align: center; padding: 60px 20px; }
    .empty-icon { width: 80px; height: 80px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 32px; color: #94a3b8; }
    .empty-state-premium h4 { font-weight: 700; color: #0f172a; margin-bottom: 4px; }
    .empty-state-premium p { color: #94a3b8; font-size: 14px; }

    /* Pagination */
    .pagination-bar { padding: 16px; border-top: 1px solid #f1f5f9; }

    /* Responsive */
    @media (max-width: 768px) {
        .page-header-premium { flex-direction: column; align-items: flex-start; }
        .premium-table thead { display: none; }
        .premium-table tbody tr { display: block; padding: 16px; border-bottom: 1px solid #f1f5f9; }
        .premium-table tbody td { display: flex; justify-content: space-between; padding: 6px 0; border: none; font-size: 13px; }
        .premium-table tbody td::before { content: attr(data-label); font-weight: 600; color: #64748b; font-size: 12px; }
    }
</style>
@endsection
