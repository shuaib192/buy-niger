{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    View: Vendor Analytics
--}}
@extends('layouts.app')

@section('title', 'Analytics & Insights')
@section('page_title', 'Analytics')

@section('sidebar')
    @include('vendor.partials.sidebar')
@endsection

@section('content')
<div class="analytics-container py-4">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="h3 font-bold text-secondary-900 mb-1">Analytics & Business Insights</h1>
            <p class="text-secondary-500 mb-0">Track your store's growth and customer engagement metrics.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary bg-white shadow-sm border-0 px-4">
                <i class="fas fa-download mr-2"></i> Export Report
            </button>
            <div class="dropdown">
                <button class="btn btn-primary shadow-primary-200 border-0 px-4 dropdown-toggle" type="button" data-toggle="dropdown">
                    Last 30 Days
                </button>
                <div class="dropdown-menu dropdown-menu-right shadow-lg border-0">
                    <a class="dropdown-item py-2" href="#">Last 7 Days</a>
                    <a class="dropdown-item py-2 active" href="#">Last 30 Days</a>
                    <a class="dropdown-item py-2" href="#">Year to Date</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row mb-5">
        <div class="col-md-4">
            <div class="premium-stats-card stats-revenue">
                <div class="card-content">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="icon-box">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div class="trend-badge positive">
                            <i class="fas fa-caret-up mr-1"></i> 12.5%
                        </div>
                    </div>
                    <div class="stats-data">
                        <span class="label">Total Revenue</span>
                        <h2 class="value">₦{{ number_format($totalRevenue) }}</h2>
                        <p class="sub-label">From {{ $totalOrders }} delivered orders</p>
                    </div>
                </div>
                <div class="card-wave"></div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="premium-stats-card stats-orders">
                <div class="card-content">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="icon-box">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="trend-badge positive">
                            <i class="fas fa-caret-up mr-1"></i> 8.2%
                        </div>
                    </div>
                    <div class="stats-data">
                        <span class="label">New Orders</span>
                        <h2 class="value">{{ number_format($totalOrders) }}</h2>
                        <p class="sub-label">Conversion rate: 3.4%</p>
                    </div>
                </div>
                <div class="card-wave"></div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="premium-stats-card stats-products">
                <div class="card-content">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="icon-box">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="trend-badge neutral">
                            Stable
                        </div>
                    </div>
                    <div class="stats-data">
                        <span class="label">Active Products</span>
                        <h2 class="value">{{ number_format($totalProducts) }}</h2>
                        <p class="sub-label">In your storefront catalog</p>
                    </div>
                </div>
                <div class="card-wave"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Revenue Chart -->
        <div class="col-md-8">
            <div class="dashboard-card border-0 shadow-sm h-100">
                <div class="dashboard-card-header bg-white border-0 py-4 d-flex justify-content-between align-items-center">
                    <h3 class="h5 font-bold mb-0">Revenue Growth Trend</h3>
                    <div class="chart-legend d-flex gap-3">
                        <div class="d-flex align-items-center"><span class="legend-dot bg-primary mr-2"></span> <small>Revenue</small></div>
                    </div>
                </div>
                <div class="dashboard-card-body pt-0">
                    <div style="height: 350px;">
                        <canvas id="revenueFlowChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="col-md-4">
            <div class="dashboard-card border-0 shadow-sm h-100">
                <div class="dashboard-card-header bg-white border-0 py-4">
                    <h3 class="h5 font-bold mb-0">Quick Insights</h3>
                </div>
                <div class="dashboard-card-body">
                    <div class="insight-item mb-4">
                        <div class="d-flex align-items-center mb-2">
                            <div class="insight-icon bg-warning-soft text-warning mr-3">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="insight-title font-bold text-secondary-900">Average Rating</div>
                        </div>
                        <div class="h3 mb-1">4.8 <small class="text-sm font-normal text-secondary-500">/ 5</small></div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-warning" style="width: 92%"></div>
                        </div>
                    </div>

                    <div class="insight-item mb-4">
                        <div class="d-flex align-items-center mb-2">
                            <div class="insight-icon bg-primary-soft text-primary mr-3">
                                <i class="fas fa-truck"></i>
                            </div>
                            <div class="insight-title font-bold text-secondary-900">Avg. Processing Time</div>
                        </div>
                        <div class="h3 mb-1">1.2 <small class="text-sm font-normal text-secondary-500">Days</small></div>
                        <p class="text-xs text-secondary-500">20% faster than last month</p>
                    </div>

                    <div class="insight-item">
                        <div class="ai-suggestion-box p-3 rounded-lg bg-primary-50 border border-primary-100">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-robot text-primary mr-2"></i>
                                <span class="font-bold text-primary text-sm uppercase letter-spacing-1">AI Recommendation</span>
                            </div>
                            <p class="text-xs text-secondary-700 mb-0">Based on your sales velocity, restocking "<strong>Electronics Item 2</strong>" by Wednesday could prevent a 15% revenue loss.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('revenueFlowChart').getContext('2d');
        const salesData = @json($salesData);
        
        const labels = salesData.map(d => {
            const date = new Date(d.date);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        });
        const data = salesData.map(d => d.total);

        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(0, 102, 255, 0.2)');
        gradient.addColorStop(1, 'rgba(0, 102, 255, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels.length ? labels : ['No Data'],
                datasets: [{
                    label: 'Revenue',
                    data: data.length ? data : [0],
                    borderColor: '#0066FF',
                    backgroundColor: gradient,
                    borderWidth: 4,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#0066FF',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 8,
                    pointHoverBackgroundColor: '#0066FF',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 4
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
                        backgroundColor: '#1e293b',
                        titleFont: { size: 13, weight: 'bold' },
                        bodyFont: { size: 12 },
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
                            drawBorder: false,
                            color: 'rgba(226, 232, 240, 0.5)',
                            borderDash: [5, 5]
                        },
                        ticks: {
                            callback: function(value) {
                                return '₦' + (value >= 1000 ? (value/1000).toFixed(1) + 'k' : value);
                            },
                            font: { size: 11, family: "'Inter', sans-serif" },
                            color: '#64748b'
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { 
                            font: { size: 11, family: "'Inter', sans-serif" },
                            color: '#64748b',
                            maxRotation: 0
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'nearest',
                }
            }
        });
    });
</script>
@endpush

<style>
    .analytics-container { max-width: 1200px; margin: 0 auto; }
    .font-bold { font-weight: 700; }
    .letter-spacing-1 { letter-spacing: 0.05em; }
    
    .premium-stats-card {
        position: relative;
        overflow: hidden;
        border-radius: 24px;
        padding: 28px;
        height: 100%;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }
    
    .premium-stats-card:hover { transform: translateY(-8px); box-shadow: 0 20px 35px -8px rgba(0, 0, 0, 0.08); }
    
    .stats-revenue { background: linear-gradient(135deg, #0066FF 0%, #004ecc 100%); color: white; }
    .stats-orders { background: #ffffff; border: 1px solid #e2e8f0; }
    .stats-products { background: #ffffff; border: 1px solid #e2e8f0; }
    
    .stats-revenue .icon-box { background: rgba(255, 255, 255, 0.2); color: white; }
    .stats-orders .icon-box { background: #eff6ff; color: #0066FF; }
    .stats-products .icon-box { background: #f0f9ff; color: #0284c7; }
    
    .icon-box { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
    
    .trend-badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    .trend-badge.positive { background: rgba(52, 211, 153, 0.15); color: #059669; }
    .stats-revenue .trend-badge.positive { background: rgba(255, 255, 255, 0.2); color: #a7f3d0; }
    .trend-badge.neutral { background: #f1f5f9; color: #64748b; }
    
    .stats-data .label { font-size: 13px; font-weight: 600; opacity: 0.7; letter-spacing: 0.02em; display: block; margin-bottom: 4px; }
    .stats-data .value { font-size: 32px; font-weight: 800; margin-bottom: 4px; }
    .stats-data .sub-label { font-size: 11px; opacity: 0.6; margin-bottom: 0; }
    
    .card-wave { position: absolute; bottom: 0; right: 0; width: 150px; height: 100px; background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%23ffffff' fill-opacity='0.05' d='M0,192L48,197.3C96,203,192,213,288,229.3C384,245,480,267,576,250.7C672,235,768,181,864,181.3C960,181,1056,235,1152,234.7C1248,235,1344,181,1392,154.7L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'%3E%3C/path%3E%3C/svg%3E") no-repeat; transform: scale(3) rotate(-5deg); pointer-events: none; }
    
    .legend-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
    .insight-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
    .progress-sm { height: 6px; border-radius: 10px; background: #f1f5f9; }
    
    .shadow-primary-200 { box-shadow: 0 4px 14px 0 rgba(0, 102, 255, 0.3); }
    .gap-3 { gap: 1rem; }
</style>
@endsection
