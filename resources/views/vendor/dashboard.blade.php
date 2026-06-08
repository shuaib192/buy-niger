@extends('layouts.app')

@section('title', 'Vendor Dashboard')
@section('page_title', 'Dashboard')

@section('sidebar')
    @include('vendor.partials.sidebar')
@endsection

@section('content')
    @if($vendor && $vendor->status === 'pending')
        <div class="alert alert-warning">
            <i class="fas fa-clock"></i>
            <span><strong>Account Pending:</strong> Your vendor account is awaiting approval. You can set up your store while you wait.</span>
        </div>
    @endif

    @if(($vendor->kyc_status ?? 'not_submitted') !== 'verified')
        <div class="alert alert-danger">
            <i class="fas fa-shield-alt"></i>
            <span><strong>KYC Verification Required:</strong> You must complete your Identity Verification to publish products and receive payouts. <a href="{{ route('vendor.settings') }}" class="text-danger"><strong>Complete KYC Now</strong></a></span>
        </div>
    @endif

    <!-- Stats Grid -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="dashboard-stat-card p-3 p-md-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-box bg-primary-soft text-primary">
                        <i class="fas fa-box"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-secondary-500 uppercase letter-spacing-1 mb-1">Products</div>
                        <div class="h4 font-bold mb-0 text-secondary-900">{{ number_format($stats['total_products']) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dashboard-stat-card p-3 p-md-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-box bg-success-soft text-success">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-secondary-500 uppercase letter-spacing-1 mb-1">Total Orders</div>
                        <div class="h4 font-bold mb-0 text-secondary-900">{{ number_format($stats['total_orders']) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dashboard-stat-card p-3 p-md-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-box bg-warning-soft text-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-secondary-500 uppercase letter-spacing-1 mb-1">Pending Earnings</div>
                        <div class="h4 font-bold mb-0 text-secondary-900">₦{{ number_format($stats['pending_earnings']) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dashboard-stat-card p-3 p-md-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-box bg-info-soft text-info">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-secondary-500 uppercase letter-spacing-1 mb-1">Available</div>
                        <div class="h4 font-bold mb-0 text-secondary-900">₦{{ number_format($stats['total_earnings']) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Grid -->
    <div class="row g-4">
        <!-- Main Column -->
        <div class="col-lg-8">
            <!-- Sales Chart -->
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h3>Sales Overview</h3>
                    <div class="period-pills">
                        <a href="?period=daily" class="period-pill {{ $period === 'daily' ? 'active' : '' }}">Daily</a>
                        <a href="?period=weekly" class="period-pill {{ $period === 'weekly' ? 'active' : '' }}">Weekly</a>
                        <a href="?period=monthly" class="period-pill {{ $period === 'monthly' ? 'active' : '' }}">Monthly</a>
                    </div>
                </div>
                <div class="dashboard-card-body">
                    <div style="height: 350px;">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top Products -->
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h3>Top Products</h3>
                </div>
                <div class="dashboard-card-body p-0">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Units Sold</th>
                                    <th>Revenue</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProducts as $item)
                                    @if($item->product)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if($item->product->primary_image_url)
                                                    <img src="{{ $item->product->primary_image_url }}" alt="" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                                @endif
                                                <span class="font-weight-bold text-sm">{{ Str::limit($item->product->name, 35) }}</span>
                                            </div>
                                        </td>
                                        <td><span class="badge badge-primary">{{ $item->total_sold }}</span></td>
                                        <td class="font-semibold">₦{{ number_format($item->total_revenue) }}</td>
                                        <td><a href="{{ route('vendor.products.edit', $item->product_id) }}" class="btn btn-sm btn-soft">Edit</a></td>
                                    </tr>
                                    @endif
                                @empty
                                    <tr><td colspan="4" class="text-center py-4 text-secondary-500">No sales data yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h3>Recent Orders</h3>
                    <a href="{{ route('vendor.orders') }}" class="btn btn-sm btn-soft">View All</a>
                </div>
                <div class="dashboard-card-body p-0">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $orderItem)
                                    <tr>
                                        <td class="font-semibold">{{ $orderItem->order->order_number ?? 'N/A' }}</td>
                                        <td>{{ $orderItem->order->user->name ?? 'Guest' }}</td>
                                        <td class="font-semibold">₦{{ number_format($orderItem->subtotal) }}</td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'pending' => 'badge-pending',
                                                    'processing' => 'badge-processing',
                                                    'shipped' => 'badge-shipped',
                                                    'delivered' => 'badge-delivered',
                                                    'cancelled' => 'badge-cancelled',
                                                ];
                                            @endphp
                                            <span class="badge {{ $statusColors[$orderItem->status] ?? 'badge-secondary' }}">{{ ucfirst($orderItem->status) }}</span>
                                        </td>
                                        <td><a href="{{ route('vendor.orders.show', $orderItem->id) }}" class="btn btn-sm btn-soft">Details</a></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="fas fa-inbox"></i>
                                                <h4>No orders yet</h4>
                                                <p>Your store hasn't received any orders.</p>
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

        <!-- Sidebar Column -->
        <div class="col-lg-4">
            <!-- Share Your Store -->
            @if($vendor && $vendor->store_slug)
            <div class="share-card dashboard-card">
                <div class="dashboard-card-body p-3 p-md-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="share-icon-box">
                            <i class="fas fa-share-alt"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold mb-0 text-secondary-900">Share Your Store</h4>
                            <p class="text-xs text-secondary-500 mb-0">Get more customers from social media</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <input type="text" id="storeUrl" readonly value="{{ url('/store/' . $vendor->store_slug) }}" class="form-control text-sm font-semibold mb-2">
                        <button onclick="copyStoreLink()" id="copyBtn" class="btn btn-premium btn-sm btn-full">
                            <i class="fas fa-copy"></i> Copy Store Link
                        </button>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="https://wa.me/?text=Check%20out%20my%20store%20on%20BuyNiger!%20{{ urlencode(url('/store/' . $vendor->store_slug)) }}" target="_blank" class="btn btn-sm flex-fill" style="background:#25d366;color:white;border:none;">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url('/store/' . $vendor->store_slug)) }}" target="_blank" class="btn btn-sm flex-fill" style="background:#1877f2;color:white;border:none;">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?text=Check%20out%20my%20store%20on%20BuyNiger!&url={{ urlencode(url('/store/' . $vendor->store_slug)) }}" target="_blank" class="btn btn-sm flex-fill" style="background:#0f172a;color:white;border:none;">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- AI Insight -->
            <div class="premium-ai-card">
                <div class="card-content p-3 p-md-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="ai-icon-box">
                            <i class="fas fa-robot"></i>
                        </div>
                        <h4 class="mb-0 text-xs font-bold uppercase letter-spacing-1" style="color:rgba(255,255,255,0.8);">AI Smart Insight</h4>
                    </div>
                    <p class="insight-text mb-4">
                        Based on current trends, your <strong>"{{ $topProducts->first()->product->name ?? 'top items' }}"</strong> are likely to stock out next week. Consider refreshing your inventory soon to maintain momentum.
                    </p>
                    <a href="{{ route('vendor.analytics') }}" class="btn btn-white btn-sm btn-full py-2 font-bold shadow-sm">
                        <i class="fas fa-bolt mr-2 text-primary"></i> Analyze Full Data
                    </a>
                </div>
                <div class="ai-card-glow"></div>
            </div>

            <!-- Low Stock Alerts -->
            @if($lowStockProducts->count() > 0)
            <div class="dashboard-card">
                <div class="dashboard-card-header bg-danger-50 py-3">
                    <h3 class="text-danger mb-0"><i class="fas fa-exclamation-triangle mr-2"></i> Low Stock Alerts</h3>
                </div>
                <div class="dashboard-card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($lowStockProducts as $prod)
                            <div class="list-group-item d-flex justify-content-between align-items-center border-0 py-3 px-4">
                                <div class="mr-3">
                                    <div class="font-bold text-sm text-secondary-900">{{ Str::limit($prod->name, 25) }}</div>
                                    <small class="text-xs text-secondary-400">SKU: {{ $prod->sku }}</small>
                                </div>
                                <span class="badge badge-low-stock px-2 py-1">{{ $prod->quantity }} left</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="p-3 bg-secondary-50">
                        <a href="{{ route('vendor.products') }}" class="btn btn-outline-danger btn-sm btn-full">
                            <i class="fas fa-boxes mr-2"></i> Restock Now
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h3>Quick Actions</h3>
                </div>
                <div class="dashboard-card-body">
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('vendor.products.create') }}" class="btn btn-premium btn-full py-2">
                            <i class="fas fa-plus-circle mr-2"></i> Add New Product
                        </a>
                        <a href="{{ route('vendor.messages.index') }}" class="btn btn-secondary btn-full py-2">
                            <i class="fas fa-envelope mr-2"></i> Customer Messages
                        </a>
                        <a href="{{ route('vendor.settings') }}" class="btn btn-secondary btn-full py-2">
                            <i class="fas fa-store-alt mr-2"></i> Store Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function() {
            var ctx = document.getElementById('salesChart');
            if (!ctx) return;
            var chartData = @json($chartData);

            new Chart(ctx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: chartData.datasets[0].label,
                        data: chartData.datasets[0].data,
                        borderColor: '#0066FF',
                        backgroundColor: 'rgba(0, 102, 255, 0.08)',
                        borderWidth: 3,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#0066FF',
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
                            backgroundColor: '#1E293B',
                            padding: 12,
                            cornerRadius: 8,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return '₦' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { borderDash: [5, 5], color: '#E2E8F0' },
                            ticks: {
                                callback: function(value) { return '₦' + value.toLocaleString(); },
                                color: '#64748B'
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: '#64748B' }
                        }
                    }
                }
            });
        })();

        function copyStoreLink() {
            var input = document.getElementById('storeUrl');
            var btn = document.getElementById('copyBtn');
            if (!input || !btn) return;
            input.select();
            document.execCommand('copy');
            btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
            btn.style.background = '#10b981';
            setTimeout(function() {
                btn.innerHTML = '<i class="fas fa-copy"></i> Copy Store Link';
                btn.style.background = '';
                btn.className = 'btn btn-premium btn-sm btn-full';
            }, 2000);
        }
    </script>
@endpush
@endsection
