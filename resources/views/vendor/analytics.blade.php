{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    View: Vendor Analytics (v2 — Full Overview + AI Insights)
--}}
@extends('layouts.app')

@section('title', 'Analytics & Insights')
@section('page_title', 'Analytics')

@section('sidebar')
    @include('vendor.partials.sidebar')
@endsection

@section('content')
<div class="an-wrap py-4">

    {{-- ===== HEADER + PERIOD TABS ===== --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="an-title mb-1">Analytics & Insights</h1>
            <p class="an-sub mb-0">
                <i class="fas fa-circle-notch fa-spin me-1 text-primary small"></i>
                Showing data for <strong>{{ $periodLabel }}</strong>
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            @foreach($periodMap as $key => $p)
            <a href="{{ route('vendor.analytics', ['period' => $key]) }}"
               class="btn btn-sm an-period-btn {{ $period == $key ? 'active' : '' }}">
                {{ $p['label'] }}
            </a>
            @endforeach
            <a href="{{ route('vendor.analytics.export', ['period' => $period]) }}" class="btn btn-sm btn-outline-secondary ms-2">
                <i class="fas fa-download me-1"></i> Export
            </a>
        </div>
    </div>

    {{-- ===== KPI GRID ===== --}}
    <div class="an-kpi-grid mb-4">
        {{-- Revenue --}}
        <div class="an-kpi-card an-kpi-revenue">
            <div class="an-kpi-icon"><i class="fas fa-wallet"></i></div>
            <div class="an-kpi-body">
                <span class="an-kpi-label">Total Revenue</span>
                <h2 class="an-kpi-value">₦{{ number_format($totalRevenue) }}</h2>
                <span class="an-kpi-sub">{{ $deliveredOrders }} delivered orders</span>
            </div>
            @if($revenueGrowth !== null)
            <div class="an-kpi-badge {{ $revenueGrowth >= 0 ? 'positive' : 'negative' }}">
                <i class="fas fa-caret-{{ $revenueGrowth >= 0 ? 'up' : 'down' }} me-1"></i>{{ abs($revenueGrowth) }}%
            </div>
            @endif
        </div>

        {{-- Orders --}}
        <div class="an-kpi-card an-kpi-orders">
            <div class="an-kpi-icon"><i class="fas fa-shopping-bag"></i></div>
            <div class="an-kpi-body">
                <span class="an-kpi-label">Total Orders</span>
                <h2 class="an-kpi-value">{{ number_format($totalOrders) }}</h2>
                <span class="an-kpi-sub">{{ $pendingOrders }} pending · {{ $cancelledOrders }} cancelled</span>
            </div>
            @if($ordersGrowth !== null)
            <div class="an-kpi-badge {{ $ordersGrowth >= 0 ? 'positive' : 'negative' }}">
                <i class="fas fa-caret-{{ $ordersGrowth >= 0 ? 'up' : 'down' }} me-1"></i>{{ abs($ordersGrowth) }}%
            </div>
            @endif
        </div>

        {{-- Customers --}}
        <div class="an-kpi-card an-kpi-customers">
            <div class="an-kpi-icon"><i class="fas fa-users"></i></div>
            <div class="an-kpi-body">
                <span class="an-kpi-label">Unique Customers</span>
                <h2 class="an-kpi-value">{{ number_format($totalCustomers) }}</h2>
                <span class="an-kpi-sub">people who ordered from you</span>
            </div>
            @if($customersGrowth !== null)
            <div class="an-kpi-badge {{ $customersGrowth >= 0 ? 'positive' : 'negative' }}">
                <i class="fas fa-caret-{{ $customersGrowth >= 0 ? 'up' : 'down' }} me-1"></i>{{ abs($customersGrowth) }}%
            </div>
            @endif
        </div>

        {{-- Avg Order Value --}}
        <div class="an-kpi-card an-kpi-avg">
            <div class="an-kpi-icon"><i class="fas fa-receipt"></i></div>
            <div class="an-kpi-body">
                <span class="an-kpi-label">Avg Order Value</span>
                <h2 class="an-kpi-value">₦{{ number_format($avgOrderValue) }}</h2>
                <span class="an-kpi-sub">per delivered order</span>
            </div>
        </div>

        {{-- Products --}}
        <div class="an-kpi-card an-kpi-products">
            <div class="an-kpi-icon"><i class="fas fa-box"></i></div>
            <div class="an-kpi-body">
                <span class="an-kpi-label">Active Products</span>
                <h2 class="an-kpi-value">{{ number_format($totalProducts) }}</h2>
                <span class="an-kpi-sub">listed in your store</span>
            </div>
        </div>

        {{-- Conversion --}}
        <div class="an-kpi-card an-kpi-conv">
            <div class="an-kpi-icon"><i class="fas fa-bullseye"></i></div>
            <div class="an-kpi-body">
                <span class="an-kpi-label">Conversion Index</span>
                <h2 class="an-kpi-value">{{ $conversionRate }}%</h2>
                <span class="an-kpi-sub">orders per product ratio</span>
            </div>
        </div>
    </div>

    {{-- ===== MAIN CHART + ORDER STATUS ===== --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="an-card h-100">
                <div class="an-card-header">
                    <div>
                        <h5 class="mb-0">Revenue & Orders Over Time</h5>
                        <small class="text-muted">{{ $periodLabel }}</small>
                    </div>
                    <div class="d-flex gap-3">
                        <span class="an-legend"><span class="dot bg-primary"></span>Revenue</span>
                        <span class="an-legend"><span class="dot bg-warning"></span>Orders</span>
                    </div>
                </div>
                <div class="an-chart-body">
                    @if(count($chartLabels) > 0)
                    <canvas id="revenueChart" height="110"></canvas>
                    @else
                    <div class="an-empty">
                        <i class="fas fa-chart-bar fa-2x mb-2 text-muted"></i>
                        <p class="text-muted mb-0">No sales data for this period</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="an-card h-100">
                <div class="an-card-header">
                    <h5 class="mb-0">Orders by Status</h5>
                </div>
                <div class="an-card-body">
                    @php
                        $statusColors = ['pending'=>'#f59e0b','processing'=>'#3b82f6','shipped'=>'#8b5cf6','delivered'=>'#22c55e','cancelled'=>'#ef4444'];
                        $total = array_sum($ordersByStatus);
                    @endphp
                    @if($total > 0)
                        <canvas id="statusChart" height="180"></canvas>
                        <div class="mt-3">
                        @foreach($ordersByStatus as $st => $cnt)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="d-flex align-items-center gap-2">
                                <span class="an-dot" style="background:{{ $statusColors[$st] ?? '#94a3b8' }}"></span>
                                <span class="text-capitalize">{{ $st }}</span>
                            </span>
                            <span class="an-status-count">{{ $cnt }} ({{ $total > 0 ? round($cnt/$total*100) : 0 }}%)</span>
                        </div>
                        @endforeach
                        </div>
                    @else
                    <div class="an-empty">
                        <i class="fas fa-inbox fa-2x mb-2 text-muted"></i>
                        <p class="text-muted mb-0">No orders yet</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ===== AI INSIGHTS ===== --}}
    <div class="an-card mb-4">
        <div class="an-card-header">
            <div>
                <h5 class="mb-0"><i class="fas fa-brain me-2 text-primary"></i>AI Business Insights</h5>
                <small class="text-muted">Smart analysis based on your store's {{ $periodLabel }} performance</small>
            </div>
        </div>
        <div class="an-card-body">
            @if(count($insights) > 0)
            <div class="an-insights-grid">
                @foreach($insights as $insight)
                <div class="an-insight an-insight-{{ $insight['type'] }}">
                    <div class="an-insight-icon">
                        <i class="fas {{ $insight['icon'] }}"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">{{ $insight['title'] }}</h6>
                        <p class="mb-0 small">{!! $insight['text'] !!}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-muted mb-0">Not enough data yet to generate insights. Keep selling and come back soon!</p>
            @endif
        </div>
    </div>

    {{-- ===== TOP PRODUCTS + REVENUE BY CATEGORY ===== --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <div class="an-card h-100">
                <div class="an-card-header">
                    <h5 class="mb-0">Top Products by Revenue</h5>
                    <a href="{{ route('vendor.products') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="an-card-body p-0">
                    @if($topProducts->isNotEmpty())
                    @php $maxRevenue = $topProducts->max('revenue') ?: 1; @endphp
                    <div class="table-responsive">
                    <table class="table table-hover an-table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Units Sold</th>
                                <th>Revenue</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($topProducts as $i => $item)
                        <tr>
                            <td><span class="an-rank">{{ $i + 1 }}</span></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($item->product && $item->product->images)
                                    @php
                                        $imgs = $item->product->images;
                                        $firstImg = is_array($imgs) ? ($imgs[0] ?? '') : (is_string($imgs) ? (json_decode($imgs, true)[0] ?? '') : '');
                                    @endphp
                                    <img src="{{ Storage::url($firstImg) }}" class="an-product-img" alt="">
                                    @else
                                    <div class="an-product-img-placeholder"><i class="fas fa-box"></i></div>
                                    @endif
                                    <span class="fw-500">{{ $item->product->name ?? 'Deleted Product' }}</span>
                                </div>
                            </td>
                            <td>{{ number_format($item->quantity_sold) }}</td>
                            <td class="fw-600 text-success">₦{{ number_format($item->revenue) }}</td>
                            <td style="width:100px">
                                <div class="an-bar-track">
                                    <div class="an-bar-fill" style="width:{{ round(($item->revenue / $maxRevenue) * 100) }}%"></div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    </div>
                    @else
                    <div class="an-empty py-4">
                        <i class="fas fa-box-open fa-2x mb-2 text-muted"></i>
                        <p class="text-muted mb-0">No product sales in this period</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="an-card h-100">
                <div class="an-card-header">
                    <h5 class="mb-0">Revenue by Category</h5>
                </div>
                <div class="an-card-body">
                    @if($revenueByCategory->isNotEmpty())
                    @php
                        $catTotal = $revenueByCategory->sum('revenue') ?: 1;
                        $catColors = ['#6366f1','#22c55e','#f59e0b','#ef4444','#8b5cf6','#06b6d4'];
                    @endphp
                    <canvas id="categoryChart" height="200"></canvas>
                    <div class="mt-3">
                    @foreach($revenueByCategory as $i => $cat)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="d-flex align-items-center gap-2">
                            <span class="an-dot" style="background:{{ $catColors[$i % count($catColors)] }}"></span>
                            <span>{{ $cat->category }}</span>
                        </span>
                        <span class="fw-600">₦{{ number_format($cat->revenue) }}</span>
                    </div>
                    @endforeach
                    </div>
                    @else
                    <div class="an-empty py-4">
                        <i class="fas fa-tags fa-2x mb-2 text-muted"></i>
                        <p class="text-muted mb-0">No category data yet</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ===== RECENT ORDERS TABLE ===== --}}
    <div class="an-card mb-4">
        <div class="an-card-header">
            <h5 class="mb-0">Recent Orders</h5>
            <a href="{{ route('vendor.orders') }}" class="btn btn-sm btn-outline-primary">View All Orders</a>
        </div>
        <div class="an-card-body p-0">
            @if($recentOrders->isNotEmpty())
            <div class="table-responsive">
            <table class="table table-hover an-table mb-0">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($recentOrders as $item)
                @php
                    $stBg = ['pending'=>'#fef3c7','processing'=>'#dbeafe','shipped'=>'#ede9fe','delivered'=>'#d1fae5','cancelled'=>'#fee2e2'];
                    $stFg = ['pending'=>'#92400e','processing'=>'#1e40af','shipped'=>'#5b21b6','delivered'=>'#065f46','cancelled'=>'#991b1b'];
                @endphp
                <tr>
                    <td class="fw-600">#{{ $item->order_id }}</td>
                    <td>{{ $item->order->user->name ?? 'Guest' }}</td>
                    <td>{{ $item->product->name ?? '—' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td class="fw-600">₦{{ number_format($item->subtotal) }}</td>
                    <td>
                        <span class="an-badge" style="background:{{ $stBg[$item->status] ?? '#f1f5f9' }};color:{{ $stFg[$item->status] ?? '#475569' }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    </td>
                    <td class="text-muted small">{{ $item->created_at->format('M d, Y') }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
            </div>
            @else
            <div class="an-empty py-4">
                <i class="fas fa-clipboard-list fa-2x mb-2 text-muted"></i>
                <p class="text-muted mb-0">No recent orders in this period</p>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
/* ============ ANALYTICS PAGE STYLES ============ */
.an-title { font-size:1.6rem; font-weight:800; color:#1e293b; }
.an-sub { color:#64748b; font-size:.9rem; }

/* Period buttons */
.an-period-btn {
    border:1.5px solid #e2e8f0;
    background:#fff;
    color:#475569;
    border-radius:8px;
    font-weight:600;
    font-size:.8rem;
    padding:6px 14px;
    transition:.2s;
}
.an-period-btn:hover { border-color:#6366f1; color:#6366f1; background:#f0f0ff; }
.an-period-btn.active { background:#6366f1; border-color:#6366f1; color:#fff; }

/* KPI Grid */
.an-kpi-grid {
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));
    gap:16px;
}
.an-kpi-card {
    background:#fff;
    border-radius:16px;
    padding:22px 20px;
    position:relative;
    overflow:hidden;
    box-shadow:0 2px 12px rgba(0,0,0,.06);
    border:1px solid #f1f5f9;
    display:flex;
    align-items:flex-start;
    gap:14px;
}
.an-kpi-icon {
    width:48px; height:48px;
    border-radius:12px;
    display:flex; align-items:center; justify-content:center;
    font-size:1.1rem;
    flex-shrink:0;
}
.an-kpi-revenue .an-kpi-icon  { background:#eef2ff; color:#6366f1; }
.an-kpi-orders .an-kpi-icon   { background:#fff7ed; color:#f59e0b; }
.an-kpi-customers .an-kpi-icon{ background:#f0fdf4; color:#22c55e; }
.an-kpi-avg .an-kpi-icon      { background:#fdf4ff; color:#a855f7; }
.an-kpi-products .an-kpi-icon { background:#eff6ff; color:#3b82f6; }
.an-kpi-conv .an-kpi-icon     { background:#fff1f2; color:#ef4444; }
.an-kpi-body { flex:1; min-width:0; }
.an-kpi-label { font-size:.75rem; font-weight:600; text-transform:uppercase; letter-spacing:.5px; color:#94a3b8; }
.an-kpi-value { font-size:1.5rem; font-weight:800; color:#1e293b; margin:4px 0 2px; }
.an-kpi-sub  { font-size:.75rem; color:#94a3b8; }
.an-kpi-badge {
    position:absolute; top:14px; right:14px;
    font-size:.7rem; font-weight:700;
    padding:3px 8px; border-radius:100px;
}
.an-kpi-badge.positive { background:#d1fae5; color:#065f46; }
.an-kpi-badge.negative { background:#fee2e2; color:#991b1b; }

/* Cards */
.an-card {
    background:#fff;
    border-radius:16px;
    box-shadow:0 2px 12px rgba(0,0,0,.06);
    border:1px solid #f1f5f9;
    overflow:hidden;
}
.an-card-header {
    display:flex; justify-content:space-between; align-items:center;
    padding:18px 22px 14px;
    border-bottom:1px solid #f1f5f9;
}
.an-card-header h5 { font-weight:700; color:#1e293b; font-size:1rem; }
.an-chart-body { padding:20px 22px 16px; }
.an-card-body { padding:20px 22px; }

/* Legend */
.an-legend { display:flex; align-items:center; gap:6px; font-size:.8rem; color:#64748b; }
.dot { width:10px; height:10px; border-radius:50%; display:inline-block; }

/* Empty state */
.an-empty { display:flex; flex-direction:column; align-items:center; justify-content:center; padding:32px 20px; }

/* AI Insights */
.an-insights-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:14px; }
.an-insight {
    display:flex; align-items:flex-start; gap:14px;
    padding:16px; border-radius:12px; border:1px solid transparent;
}
.an-insight-success { background:#f0fdf4; border-color:#bbf7d0; }
.an-insight-danger  { background:#fff1f2; border-color:#fecdd3; }
.an-insight-warning { background:#fffbeb; border-color:#fde68a; }
.an-insight-info    { background:#eef2ff; border-color:#c7d2fe; }
.an-insight-icon {
    width:40px; height:40px; border-radius:10px;
    display:flex; align-items:center; justify-content:center;
    font-size:.95rem; flex-shrink:0;
}
.an-insight-success .an-insight-icon { background:#22c55e; color:#fff; }
.an-insight-danger  .an-insight-icon { background:#ef4444; color:#fff; }
.an-insight-warning .an-insight-icon { background:#f59e0b; color:#fff; }
.an-insight-info    .an-insight-icon { background:#6366f1; color:#fff; }
.an-insight h6 { font-weight:700; color:#1e293b; font-size:.9rem; }
.an-insight p  { color:#475569; font-size:.82rem; line-height:1.6; }

/* Table */
.an-table { font-size:.875rem; }
.an-table thead th { font-size:.75rem; text-transform:uppercase; letter-spacing:.5px; color:#94a3b8; font-weight:600; border-bottom:2px solid #f1f5f9; padding:12px 16px; }
.an-table tbody td { padding:12px 16px; vertical-align:middle; border-color:#f8fafc; color:#475569; }
.an-rank { width:24px; height:24px; background:#f1f5f9; border-radius:6px; display:inline-flex; align-items:center; justify-content:center; font-weight:700; font-size:.8rem; color:#64748b; }
.an-product-img { width:34px; height:34px; border-radius:8px; object-fit:cover; }
.an-product-img-placeholder { width:34px; height:34px; border-radius:8px; background:#f1f5f9; display:inline-flex; align-items:center; justify-content:center; color:#94a3b8; font-size:.8rem; }
.an-bar-track { height:6px; background:#f1f5f9; border-radius:100px; overflow:hidden; }
.an-bar-fill { height:100%; background:linear-gradient(90deg,#6366f1,#8b5cf6); border-radius:100px; }
.an-badge { font-size:.72rem; font-weight:700; padding:4px 10px; border-radius:100px; }
.an-dot { width:12px; height:12px; border-radius:50%; display:inline-block; flex-shrink:0; }
.an-status-count { font-size:.8rem; font-weight:600; color:#1e293b; }
.fw-500 { font-weight:500; }
.fw-600 { font-weight:600; }

@media(max-width:768px) {
    .an-kpi-grid { grid-template-columns:repeat(2, 1fr); }
    .an-insights-grid { grid-template-columns:1fr; }
    .an-kpi-value { font-size:1.2rem; }
}
@media(max-width:480px) {
    .an-kpi-grid { grid-template-columns:1fr; }
}
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ── Revenue + Orders dual-axis chart ──
@if(count($chartLabels) > 0)
const rCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(rCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($chartLabels) !!},
        datasets: [
            {
                label: 'Revenue (₦)',
                data: {!! json_encode($chartValues) !!},
                backgroundColor: 'rgba(99,102,241,.2)',
                borderColor: '#6366f1',
                borderWidth: 2,
                borderRadius: 6,
                type: 'bar',
                yAxisID: 'y',
            },
            {
                label: 'Orders',
                data: {!! json_encode($chartOrders) !!},
                borderColor: '#f59e0b',
                backgroundColor: 'transparent',
                borderWidth: 2.5,
                pointRadius: 4,
                pointBackgroundColor: '#f59e0b',
                tension: 0.4,
                type: 'line',
                yAxisID: 'y1',
            }
        ]
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label(ctx) {
                        return ctx.datasetIndex === 0
                            ? `Revenue: ₦${Number(ctx.parsed.y).toLocaleString()}`
                            : `Orders: ${ctx.parsed.y}`;
                    }
                }
            }
        },
        scales: {
            x: { grid: { display: false }, ticks: { color:'#94a3b8', font:{ size:11 } } },
            y:  { position:'left',  grid:{ color:'#f1f5f9' }, ticks:{ color:'#94a3b8', callback: v => '₦'+Number(v).toLocaleString() } },
            y1: { position:'right', grid:{ drawOnChartArea:false }, ticks:{ color:'#f59e0b', stepSize:1 } }
        }
    }
});
@endif

// ── Order Status Doughnut ──
@if(count($ordersByStatus) > 0)
const sCtx = document.getElementById('statusChart').getContext('2d');
const statusColors = {
    pending:'#f59e0b', processing:'#3b82f6', shipped:'#8b5cf6',
    delivered:'#22c55e', cancelled:'#ef4444'
};
const statusData = {!! json_encode($ordersByStatus) !!};
new Chart(sCtx, {
    type: 'doughnut',
    data: {
        labels: Object.keys(statusData).map(s => s.charAt(0).toUpperCase() + s.slice(1)),
        datasets: [{
            data: Object.values(statusData),
            backgroundColor: Object.keys(statusData).map(s => statusColors[s] || '#94a3b8'),
            borderWidth: 3,
            borderColor: '#fff',
            hoverOffset: 6,
        }]
    },
    options: {
        cutout: '70%',
        plugins: { legend: { display: false } }
    }
});
@endif

// ── Revenue by Category Doughnut ──
@if($revenueByCategory->isNotEmpty())
const cCtx = document.getElementById('categoryChart').getContext('2d');
const catColors = ['#6366f1','#22c55e','#f59e0b','#ef4444','#8b5cf6','#06b6d4'];
new Chart(cCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($revenueByCategory->pluck('category')->toArray()) !!},
        datasets: [{
            data: {!! json_encode($revenueByCategory->pluck('revenue')->toArray()) !!},
            backgroundColor: catColors,
            borderWidth: 3,
            borderColor: '#fff',
            hoverOffset: 6,
        }]
    },
    options: {
        cutout: '68%',
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: { label: ctx => `₦${Number(ctx.parsed).toLocaleString()}` }
            }
        }
    }
});
@endif
</script>
@endpush
