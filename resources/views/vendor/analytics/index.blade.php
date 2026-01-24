{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    View: Vendor Analytics
--}}
@extends('layouts.app')

@section('title', 'Analytics')
@section('page_title', 'Analytics & Reports')

@section('sidebar')
    @include('vendor.partials.sidebar')
@endsection

@section('content')
<div class="row mb-4">
    <!-- Key Metrics -->
    <div class="col-md-3">
        <div class="dashboard-card text-center p-3">
            <div class="text-muted small text-uppercase font-bold">Total Revenue</div>
            <div class="h2 text-primary mt-2">₦{{ number_format($totalRevenue, 2) }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="dashboard-card text-center p-3">
            <div class="text-muted small text-uppercase font-bold">Total Orders</div>
            <div class="h2 mt-2">{{ number_format($totalOrders) }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="dashboard-card text-center p-3">
            <div class="text-muted small text-uppercase font-bold">Avg. Order Value</div>
            <div class="h2 mt-2">₦{{ number_format($averageOrderValue, 2) }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="dashboard-card text-center p-3">
            <div class="text-muted small text-uppercase font-bold">Customers</div>
            <div class="h2 mt-2">{{ number_format($totalCustomers) }}</div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Revenue Chart -->
    <div class="col-md-8">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3>Revenue Trend (Last 30 Days)</h3>
            </div>
            <div class="dashboard-card-body">
                <canvas id="revenueChart" height="150"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Products -->
    <div class="col-md-4">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3>Top Products</h3>
            </div>
            <div class="dashboard-card-body p-0">
                <table class="table table-hover mb-0">
                    <tbody>
                        @forelse($topProducts as $item)
                        <tr>
                            <td style="width: 50px;">
                                <img src="{{ $item->product->primary_image_url }}" style="width: 40px; height: 40px; border-radius: 4px; object-fit: cover;">
                            </td>
                            <td>
                                <div class="font-bold small">{{ Str::limit($item->product->name, 20) }}</div>
                                <div class="text-muted small">{{ $item->quantity_sold }} sold</div>
                            </td>
                            <td class="text-right font-bold">
                                ₦{{ number_format($item->revenue, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center p-3 text-muted">No sales data yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{
                label: 'Revenue (₦)',
                data: {!! json_encode($chartValues) !!},
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endsection
