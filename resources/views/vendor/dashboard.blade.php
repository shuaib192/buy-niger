{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    View: Vendor Dashboard
--}}
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

    <!-- Stats Grid -->
    <div class="row mb-5">
        <div class="col-md-3">
            <div class="dashboard-stat-card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-primary-soft text-primary mr-3">
                            <i class="fas fa-box"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-secondary-500 uppercase letter-spacing-1 mb-1">Products</div>
                            <div class="h4 font-bold mb-0 text-secondary-900">{{ number_format($stats['total_products']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-stat-card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-success-soft text-success mr-3">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-secondary-500 uppercase letter-spacing-1 mb-1">Total Orders</div>
                            <div class="h4 font-bold mb-0 text-secondary-900">{{ number_format($stats['total_orders']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-stat-card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-warning-soft text-warning mr-3">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-secondary-500 uppercase letter-spacing-1 mb-1">Pending</div>
                            <div class="h4 font-bold mb-0 text-secondary-900">₦{{ number_format($stats['pending_earnings']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-stat-card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-info-soft text-info mr-3">
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
    </div>

    <!-- Dashboard Grid -->
    <div class="row">
        <!-- Main Column -->
        <div class="col-lg-8">
            <!-- Sales Chart -->
            <div class="dashboard-card mb-4 border-0 shadow-sm">
                <div class="dashboard-card-header bg-white border-0 py-4 d-flex justify-content-between align-items-center">
                    <h3 class="h5 font-bold mb-0">Sales Overview</h3>
                    <div class="btn-group btn-group-sm bg-light rounded-pill p-1">
                        <a href="?period=daily" class="btn btn-sm rounded-pill px-3 {{ $period === 'daily' ? 'btn-primary shadow-sm' : 'btn-light border-0' }}">Daily</a>
                        <a href="?period=weekly" class="btn btn-sm rounded-pill px-3 {{ $period === 'weekly' ? 'btn-primary shadow-sm' : 'btn-light border-0' }}">Weekly</a>
                        <a href="?period=monthly" class="btn btn-sm rounded-pill px-3 {{ $period === 'monthly' ? 'btn-primary shadow-sm' : 'btn-light border-0' }}">Monthly</a>
                    </div>
                </div>
                <div class="dashboard-card-body">
                    <div style="height: 350px;">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top Products Widget -->
            <div class="dashboard-card mb-4">
                <div class="dashboard-card-header">
                    <h3>Top Products</h3>
                </div>
                <div class="dashboard-card-body">
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
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($item->product->primary_image_url)
                                                    <img src="{{ $item->product->primary_image_url }}" alt="" class="rounded mr-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                @endif
                                                <span class="font-weight-bold text-sm">{{ Str::limit($item->product->name, 35) }}</span>
                                            </div>
                                        </td>
                                        <td><span class="badge badge-primary">{{ $item->total_sold }}</span></td>
                                        <td>₦{{ number_format($item->total_revenue) }}</td>
                                        <td><a href="{{ route('vendor.products.edit', $item->product_id) }}" class="btn btn-sm btn-outline-secondary">Edit</a></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center py-4">No sales data yet.</td></tr>
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
                    <a href="{{ route('vendor.orders') }}" class="btn btn-sm btn-secondary">View All</a>
                </div>
                <div class="dashboard-card-body">
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
                                        <td><strong>{{ $orderItem->order->order_number ?? 'N/A' }}</strong></td>
                                        <td>{{ $orderItem->order->user->name ?? 'Guest' }}</td>
                                        <td>₦{{ number_format($orderItem->subtotal) }}</td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'pending' => 'warning',
                                                    'processing' => 'info',
                                                    'shipped' => 'primary',
                                                    'delivered' => 'success',
                                                    'cancelled' => 'danger',
                                                ];
                                            @endphp
                                            <span class="badge badge-{{ $statusColors[$orderItem->status] ?? 'secondary' }}">{{ ucfirst($orderItem->status) }}</span>
                                        </td>
                                        <td><a href="{{ route('vendor.orders.show', $orderItem->id) }}" class="btn btn-sm btn-primary">Details</a></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                            <p class="text-muted">No orders yet.</p>
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
        <div class="dashboard-sidebar col-4">
            <!-- AI Insight (Enhanced) -->
            <div class="premium-ai-card mb-4">
                <div class="card-content p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="ai-icon-box mr-3">
                            <i class="fas fa-robot"></i>
                        </div>
                        <h4 class="mb-0 font-bold uppercase text-xs letter-spacing-1">AI Smart Insight</h4>
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
            <div class="dashboard-card mb-4 border-danger-100">
                <div class="dashboard-card-header bg-danger-50 py-3">
                    <h3 class="text-danger mb-0"><i class="fas fa-exclamation-triangle mr-2"></i> Low Stock Alerts</h3>
                </div>
                <div class="dashboard-card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($lowStockProducts as $prod)
                            <div class="list-group-item d-flex justify-content-between align-items-center border-0 py-3 px-4">
                                <div class="mr-3">
                                    <div class="font-weight-bold text-sm text-secondary-900">{{ Str::limit($prod->name, 25) }}</div>
                                    <code class="text-xs text-secondary-400">SKU: {{ $prod->sku }}</code>
                                </div>
                                <span class="badge badge-danger-soft text-danger px-2 py-1">{{ $prod->quantity }} left</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="p-3 bg-secondary-50">
                        <a href="{{ route('vendor.products') }}" class="btn btn-outline-danger btn-sm btn-full border-2">
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
                    <div class="d-flex flex-column gap-3">
                        <a href="{{ route('vendor.products.create') }}" class="btn btn-primary btn-full py-2 shadow-sm">
                            <i class="fas fa-plus-circle mr-2"></i> Add New Product
                        </a>
                        <a href="{{ route('vendor.messages.index') }}" class="btn btn-secondary btn-full py-2">
                            <i class="fas fa-envelope mr-2"></i> Customer Messages
                        </a>
                        <a href="{{ route('vendor.settings') }}" class="btn btn-secondary btn-full py-2">
                            <i class="fas fa-store-alt mr-2"></i> Store Front Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('styles')
<style>
    .font-bold { font-weight: 700; }
    .uppercase { text-transform: uppercase; }
    .letter-spacing-1 { letter-spacing: 0.05em; }
    .text-xs { font-size: 0.75rem; }
    .text-sm { font-size: 0.875rem; }
    .text-secondary-900 { color: #0f172a; }
    .text-secondary-500 { color: #64748b; }
    .text-secondary-400 { color: #94a3b8; }
    .text-primary-900 { color: #1e3a8a; }
    
    .bg-primary-soft { background: #eff6ff; }
    .bg-success-soft { background: #ecfdf5; }
    .bg-warning-soft { background: #fffbeb; }
    .bg-info-soft { background: #f0f9ff; }
    .bg-danger-soft { background: #fef2f2; }
    .bg-danger-50 { background: #fff1f2; }
    
    .dashboard-stat-card {
        background: #ffffff;
        border-radius: 20px;
        transition: transform 0.3s ease;
    }
    .dashboard-stat-card:hover { transform: translateY(-5px); }
    
    .icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }
    
    .premium-ai-card {
        position: relative;
        background: linear-gradient(135deg, #0066FF 0%, #004ecc 100%);
        border-radius: 24px;
        color: white;
        overflow: hidden;
        border: none;
    }
    
    .ai-icon-box {
        width: 36px;
        height: 36px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
    
    .insight-text {
        font-size: 0.9rem;
        line-height: 1.6;
        opacity: 0.95;
    }
    
    .ai-card-glow {
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
        pointer-events: none;
    }
    
    .btn-white {
        background: white;
        color: #0066FF;
        border: none;
    }
    .btn-white:hover {
        background: #f8fafc;
        color: #004ecc;
    }
    
    .badge-primary { background: #0066FF; }
    .badge-danger-soft { background: #fee2e2; color: #dc2626; }
    
    .dashboard-card { border-radius: 20px; overflow: hidden; }
    .data-table th { background: #f8fafc; color: #64748b; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 12px 16px; }
    .data-table td { padding: 16px; border-bottom: 1px solid #f1f5f9; }
    
    .btn-group.bg-light { padding: 4px; }
    .btn-group .btn-light { color: #64748b; font-weight: 600; }
    .btn-group .btn-primary { font-weight: 700; }
    
    .shadow-primary-200 { box-shadow: 0 4px 14px 0 rgba(0, 102, 255, 0.3); }
</style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        const chartData = @json($chartData);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: chartData.datasets[0].label,
                    data: chartData.datasets[0].data,
                    borderColor: '#0066FF',
                    backgroundColor: 'rgba(0, 102, 255, 0.1)',
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
                        titleColor: '#fff',
                        bodyColor: '#fff',
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
                        grid: { 
                            borderDash: [5, 5],
                            color: '#E2E8F0'
                        },
                        ticks: {
                            callback: function(value) {
                                return '₦' + value.toLocaleString();
                            },
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
    </script>
    @endpush
@endsection
