{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin
    View: Vendor Dashboard — Premium v2.0
--}}
@extends('layouts.app')

@section('title', 'Vendor Dashboard')
@section('page_title', 'Vendor Dashboard')

@section('sidebar')
    @include('vendor.partials.sidebar')
@endsection

@push('styles')
<style>
    .dash-layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: 18px;
    }
    @media (min-width: 900px) {
        .dash-layout { grid-template-columns: 1fr 320px; }
    }
    .dash-main, .dash-side { display: flex; flex-direction: column; gap: 18px; }

    /* Period toggle */
    .period-toggle {
        display: flex;
        background: var(--surface);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 3px;
        gap: 2px;
    }
    .period-toggle a {
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text-secondary);
        text-decoration: none;
        transition: all .15s;
    }
    .period-toggle a.active {
        background: #4f46e5;
        color: white;
        box-shadow: 0 2px 8px rgba(79,70,229,.3);
    }
    .period-toggle a:hover:not(.active) {
        background: var(--border-color);
        color: var(--text-primary);
    }

    /* Store Share Card */
    .store-share-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
        border-radius: 16px;
        padding: 20px;
        position: relative;
        overflow: hidden;
    }
    .store-share-card::before {
        content:'';
        position:absolute;
        top:-40px;right:-30px;
        width:120px;height:120px;
        background:rgba(255,255,255,.06);
        border-radius:50%;
    }
    .store-share-card h4 {
        font-family:'Outfit',sans-serif;
        color:white;
        font-size:.9375rem;
        font-weight:700;
        margin-bottom:2px;
        position:relative;z-index:1;
    }
    .store-share-card p {
        color:rgba(255,255,255,.6);
        font-size:.8rem;
        margin-bottom:12px;
        position:relative;z-index:1;
    }
    .store-url-input {
        width:100%;
        padding:9px 12px;
        background:rgba(255,255,255,.1);
        border:1px solid rgba(255,255,255,.15);
        border-radius:8px;
        font-size:.8rem;
        color:rgba(255,255,255,.85);
        margin-bottom:8px;
        font-weight:500;
        position:relative;z-index:1;
    }
    .store-url-input:focus { outline:none; }
    .copy-btn {
        width:100%;
        padding:9px;
        background:#4f46e5;
        color:white;
        border:none;
        border-radius:8px;
        font-size:.8125rem;
        font-weight:600;
        cursor:pointer;
        transition:all .2s;
        position:relative;z-index:1;
        display:flex;align-items:center;justify-content:center;gap:6px;
    }
    .copy-btn:hover { background:#4338ca; }
    .copy-btn.copied { background:#10b981; }
    .social-btns {
        display:flex;gap:6px;margin-top:8px;
        position:relative;z-index:1;
    }
    .social-btn {
        flex:1;
        display:flex;align-items:center;justify-content:center;gap:4px;
        padding:7px 4px;
        border-radius:8px;
        font-size:.75rem;
        font-weight:600;
        color:white;
        text-decoration:none;
        transition:opacity .2s;
    }
    .social-btn:hover { opacity:.85;color:white; }
    .social-btn.wa  { background:#25d366; }
    .social-btn.fb  { background:#1877f2; }
    .social-btn.tw  { background:#0f172a; }

    /* AI Insight */
    .ai-insight-card {
        background: linear-gradient(135deg, #312e81 0%, #4c1d95 100%);
        border-radius: 16px;
        padding: 20px;
        color: white;
        position: relative;
        overflow: hidden;
    }
    .ai-insight-card::before {
        content:'';
        position:absolute;
        top:-40px;right:-20px;
        width:120px;height:120px;
        background:rgba(255,255,255,.07);
        border-radius:50%;
    }
    .ai-insight-card::after {
        content:'';
        position:absolute;
        bottom:-30px;left:-10px;
        width:80px;height:80px;
        background:rgba(255,255,255,.05);
        border-radius:50%;
    }
    .ai-insight-inner { position:relative;z-index:1; }
    .ai-insight-header {
        display:flex;align-items:center;gap:10px;margin-bottom:12px;
    }
    .ai-icon {
        width:36px;height:36px;
        background:rgba(255,255,255,.2);
        border-radius:10px;
        display:flex;align-items:center;justify-content:center;
        font-size:.9rem;color:white;
    }
    .ai-tag {
        font-size:.6rem;
        font-weight:700;
        text-transform:uppercase;
        letter-spacing:.08em;
        color:rgba(255,255,255,.6);
    }
    .ai-title {
        font-family:'Outfit',sans-serif;
        font-size:.9375rem;
        font-weight:700;
        color:white;
    }
    .ai-text {
        font-size:.85rem;
        line-height:1.65;
        color:rgba(255,255,255,.85);
        margin-bottom:14px;
    }
    .btn-ghost-white {
        display:inline-flex;align-items:center;gap:6px;
        width:100%;justify-content:center;
        padding:9px;
        background:rgba(255,255,255,.15);
        border:1px solid rgba(255,255,255,.2);
        color:white;
        border-radius:9px;
        font-size:.8125rem;
        font-weight:600;
        text-decoration:none;
        transition:all .2s;
    }
    .btn-ghost-white:hover { background:rgba(255,255,255,.25);color:white; }

    /* Low stock */
    .low-stock-item {
        display:flex;align-items:center;justify-content:space-between;
        padding:10px 0;
        border-bottom:1px solid #f1f5f9;
    }
    .low-stock-item:last-child { border-bottom:none; }
    .low-stock-name { font-size:.8125rem;font-weight:600;color:var(--text-primary); }
    .low-stock-sku  { font-size:.7rem;color:var(--text-muted); }
    .low-stock-badge {
        background:#fee2e2;color:#dc2626;
        font-size:.7rem;font-weight:700;
        padding:3px 8px;border-radius:99px;
        white-space:nowrap;
    }

    /* Product row in table */
    .product-cell { display:flex;align-items:center;gap:10px; }
    .product-thumb {
        width:36px;height:36px;
        border-radius:8px;
        object-fit:cover;
        background:var(--surface);
        flex-shrink:0;
    }
</style>
@endpush

@section('content')

{{-- ═══ ALERTS ═══ --}}
@if($vendor && $vendor->status === 'pending')
    <div class="alert alert-warning">
        <i class="fas fa-clock"></i>
        <span><strong>Account Pending:</strong> Your vendor account is awaiting approval. You can set up your store while you wait.</span>
    </div>
@endif

@if(($vendor->kyc_status ?? 'not_submitted') !== 'verified')
    <div class="alert alert-danger">
        <i class="fas fa-shield-exclamation"></i>
        <span>
            <strong>KYC Required:</strong> Complete your Identity Verification to publish products and receive payouts.
            <a href="{{ route('vendor.settings') }}" style="color:inherit;font-weight:700;text-decoration:underline;">Complete KYC Now →</a>
        </span>
    </div>
@endif

{{-- ═══ STAT CARDS ═══ --}}
<div class="stats-grid" style="margin-bottom:20px;">
    <div class="stat-card green">
        <div class="stat-card-inner">
            <div class="stat-icon"><i class="fas fa-box-open"></i></div>
            <div class="stat-info">
                <h3>{{ number_format($stats['total_products']) }}</h3>
                <p>Products</p>
            </div>
        </div>
    </div>
    <div class="stat-card blue">
        <div class="stat-card-inner">
            <div class="stat-icon"><i class="fas fa-bag-shopping"></i></div>
            <div class="stat-info">
                <h3>{{ number_format($stats['total_orders']) }}</h3>
                <p>Total Orders</p>
            </div>
        </div>
    </div>
    <div class="stat-card orange">
        <div class="stat-card-inner">
            <div class="stat-icon"><i class="fas fa-clock"></i></div>
            <div class="stat-info">
                <h3>₦{{ number_format($stats['pending_earnings']) }}</h3>
                <p>Pending Earnings</p>
            </div>
        </div>
    </div>
    <div class="stat-card indigo">
        <div class="stat-card-inner">
            <div class="stat-icon"><i class="fas fa-wallet"></i></div>
            <div class="stat-info">
                <h3>₦{{ number_format($stats['total_earnings']) }}</h3>
                <p>Available Balance</p>
            </div>
        </div>
    </div>
</div>

{{-- ═══ MAIN LAYOUT ═══ --}}
<div class="dash-layout">

    {{-- LEFT COLUMN --}}
    <div class="dash-main">

        {{-- Sales Chart --}}
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3><i class="fas fa-chart-area" style="color:#4f46e5;margin-right:8px;"></i>Sales Overview</h3>
                <div class="period-toggle">
                    <a href="?period=daily"   class="{{ $period === 'daily'   ? 'active' : '' }}">Daily</a>
                    <a href="?period=weekly"  class="{{ $period === 'weekly'  ? 'active' : '' }}">Weekly</a>
                    <a href="?period=monthly" class="{{ $period === 'monthly' ? 'active' : '' }}">Monthly</a>
                </div>
            </div>
            <div class="dashboard-card-body">
                <div style="height:260px;position:relative;">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Top Products --}}
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3><i class="fas fa-fire" style="color:#f97316;margin-right:8px;"></i>Top Products</h3>
                <a href="{{ route('vendor.products') }}" class="btn btn-sm btn-secondary">View All</a>
            </div>
            <div class="dashboard-card-body" style="padding:0;">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Sold</th>
                                <th>Revenue</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProducts as $item)
                                @if($item->product)
                                <tr>
                                    <td>
                                        <div class="product-cell">
                                            @if($item->product->primary_image_url)
                                                <img src="{{ $item->product->primary_image_url }}"
                                                     alt="" class="product-thumb">
                                            @else
                                                <div class="product-thumb" style="display:flex;align-items:center;justify-content:center;font-size:.8rem;color:var(--text-muted);">
                                                    <i class="fas fa-image"></i>
                                                </div>
                                            @endif
                                            <span style="font-weight:600;font-size:.8125rem;">
                                                {{ Str::limit($item->product->name, 30) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td><span class="badge badge-primary">{{ $item->total_sold }}</span></td>
                                    <td><strong>₦{{ number_format($item->total_revenue) }}</strong></td>
                                    <td>
                                        <a href="{{ route('vendor.products.edit', $item->product_id) }}"
                                           class="btn btn-sm btn-secondary">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="4">
                                        <div class="empty-state">
                                            <i class="fas fa-chart-bar"></i>
                                            <p>No sales data yet</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Recent Orders --}}
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3><i class="fas fa-receipt" style="color:#10b981;margin-right:8px;"></i>Recent Orders</h3>
                <a href="{{ route('vendor.orders') }}" class="btn btn-sm btn-secondary">View All</a>
            </div>
            <div class="dashboard-card-body" style="padding:0;">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $orderItem)
                                @php
                                    $statusMap = ['pending'=>'warning','processing'=>'info','shipped'=>'primary','delivered'=>'success','cancelled'=>'danger'];
                                @endphp
                                <tr>
                                    <td><strong>{{ $orderItem->order->order_number ?? 'N/A' }}</strong></td>
                                    <td>{{ $orderItem->order->user->name ?? 'Guest' }}</td>
                                    <td><strong>₦{{ number_format($orderItem->subtotal) }}</strong></td>
                                    <td>
                                        <span class="badge badge-{{ $statusMap[$orderItem->status] ?? 'secondary' }}">
                                            {{ ucfirst($orderItem->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('vendor.orders.show', $orderItem->id) }}"
                                           class="btn btn-sm btn-secondary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="empty-state">
                                            <i class="fas fa-inbox"></i>
                                            <p>No orders yet</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT COLUMN --}}
    <div class="dash-side">

        {{-- Share Store --}}
        @if($vendor && $vendor->store_slug)
        <div class="store-share-card">
            <h4><i class="fas fa-share-nodes" style="margin-right:6px;"></i>Share Your Store</h4>
            <p>Get more customers from social media</p>
            <input type="text" class="store-url-input" id="storeUrl"
                   value="{{ url('/store/' . $vendor->store_slug) }}" readonly>
            <button class="copy-btn" id="copyBtn" onclick="copyStoreLink()">
                <i class="fas fa-copy" id="copyIcon"></i>
                <span id="copyText">Copy Store Link</span>
            </button>
            <div class="social-btns">
                <a href="https://wa.me/?text={{ urlencode('Check out my store on BuyNiger! ' . url('/store/' . $vendor->store_slug)) }}"
                   target="_blank" class="social-btn wa">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url('/store/' . $vendor->store_slug)) }}"
                   target="_blank" class="social-btn fb">
                    <i class="fab fa-facebook-f"></i> Facebook
                </a>
                <a href="https://twitter.com/intent/tweet?text={{ urlencode('Check out my store on BuyNiger! ' . url('/store/' . $vendor->store_slug)) }}"
                   target="_blank" class="social-btn tw">
                    <i class="fab fa-x-twitter"></i> X
                </a>
            </div>
        </div>
        @endif

        {{-- AI Insight --}}
        <div class="ai-insight-card">
            <div class="ai-insight-inner">
                <div class="ai-insight-header">
                    <div class="ai-icon"><i class="fas fa-robot"></i></div>
                    <div>
                        <div class="ai-tag">AI Smart Insight</div>
                        <div class="ai-title">Sales Intelligence</div>
                    </div>
                </div>
                <p class="ai-text">
                    Based on current trends, your
                    <strong style="color:white;">"{{ $topProducts->first()?->product?->name ?? 'top items' }}"</strong>
                    may stock out this week. Refresh inventory to maintain momentum.
                </p>
                <a href="{{ route('vendor.analytics') }}" class="btn-ghost-white">
                    <i class="fas fa-chart-line"></i> View Full Analytics
                </a>
            </div>
        </div>

        {{-- Low Stock Alerts --}}
        @if($lowStockProducts->count() > 0)
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3 style="color:#dc2626;">
                    <i class="fas fa-triangle-exclamation" style="margin-right:8px;"></i>Low Stock Alerts
                </h3>
                <span style="background:#fee2e2;color:#dc2626;font-size:.7rem;font-weight:700;padding:2px 8px;border-radius:99px;">
                    {{ $lowStockProducts->count() }}
                </span>
            </div>
            <div class="dashboard-card-body" style="padding-top:8px;padding-bottom:8px;">
                @foreach($lowStockProducts as $prod)
                    <div class="low-stock-item">
                        <div>
                            <div class="low-stock-name">{{ Str::limit($prod->name, 22) }}</div>
                            <div class="low-stock-sku">SKU: {{ $prod->sku }}</div>
                        </div>
                        <span class="low-stock-badge">{{ $prod->quantity }} left</span>
                    </div>
                @endforeach
            </div>
            <div style="padding:10px 20px 14px;">
                <a href="{{ route('vendor.products') }}"
                   class="btn btn-sm btn-full"
                   style="background:#fee2e2;color:#dc2626;border:1.5px solid #fca5a5;font-weight:700;border-radius:10px;text-decoration:none;display:flex;align-items:center;justify-content:center;gap:6px;padding:9px;">
                    <i class="fas fa-boxes-stacked"></i> Restock Now
                </a>
            </div>
        </div>
        @endif

        {{-- Quick Actions --}}
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3><i class="fas fa-bolt" style="color:#f59e0b;margin-right:8px;"></i>Quick Actions</h3>
            </div>
            <div class="dashboard-card-body" style="display:flex;flex-direction:column;gap:8px;">
                <a href="{{ route('vendor.products.create') }}" class="btn btn-primary btn-full">
                    <i class="fas fa-plus-circle"></i> Add New Product
                </a>
                <a href="{{ route('vendor.orders') }}" class="btn btn-secondary btn-full">
                    <i class="fas fa-bag-shopping"></i> View Orders
                </a>
                <a href="{{ route('vendor.messages.index') }}" class="btn btn-secondary btn-full">
                    <i class="fas fa-comments"></i> Customer Messages
                </a>
                <a href="{{ route('vendor.finances') }}" class="btn btn-secondary btn-full">
                    <i class="fas fa-wallet"></i> Finances & Payouts
                </a>
                <a href="{{ route('vendor.settings') }}" class="btn btn-secondary btn-full">
                    <i class="fas fa-store"></i> Store Settings
                </a>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function() {
    const ctx       = document.getElementById('salesChart').getContext('2d');
    const chartData = @json($chartData);

    const gradient = ctx.createLinearGradient(0, 0, 0, 260);
    gradient.addColorStop(0,   'rgba(79,70,229,.25)');
    gradient.addColorStop(1,   'rgba(79,70,229,.01)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: chartData.datasets[0].label,
                data: chartData.datasets[0].data,
                borderColor: '#4f46e5',
                backgroundColor: gradient,
                borderWidth: 2.5,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#4f46e5',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: '#0f172a',
                    titleColor: '#fff',
                    bodyColor: 'rgba(255,255,255,.8)',
                    padding: 12,
                    cornerRadius: 10,
                    displayColors: false,
                    callbacks: {
                        label: function(ctx) {
                            return '₦' + ctx.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9', borderDash: [4,4] },
                    ticks: {
                        color: '#94a3b8',
                        callback: v => '₦' + (v >= 1000 ? (v/1000).toFixed(0)+'k' : v)
                    },
                    border: { display: false }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#94a3b8', maxTicksLimit: 8 },
                    border: { display: false }
                }
            }
        }
    });
})();

function copyStoreLink() {
    const input   = document.getElementById('storeUrl');
    const btn     = document.getElementById('copyBtn');
    const icon    = document.getElementById('copyIcon');
    const txt     = document.getElementById('copyText');

    input.select();
    try {
        navigator.clipboard.writeText(input.value);
    } catch (e) {
        document.execCommand('copy');
    }

    btn.classList.add('copied');
    icon.className = 'fas fa-check';
    txt.textContent = 'Copied!';
    setTimeout(function() {
        btn.classList.remove('copied');
        icon.className = 'fas fa-copy';
        txt.textContent = 'Copy Store Link';
    }, 2500);
}
</script>
@endpush
