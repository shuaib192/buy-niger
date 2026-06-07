{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin
    View: Admin — Platform Analytics — Premium v2.0
--}}
@extends('layouts.app')

@section('title', 'Platform Analytics')
@section('page_title', 'Analytics')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@push('styles')
<style>
.analytics-hero {
    background: linear-gradient(135deg, #1e1b4b 0%, #3730a3 50%, #4f46e5 100%);
    border-radius: 18px;
    padding: 28px 32px;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
}
.analytics-hero::before {
    content: '';
    position: absolute;
    top: -60px; right: -40px;
    width: 220px; height: 220px;
    background: rgba(165,180,252,.08);
    border-radius: 50%;
}
.analytics-hero::after {
    content: '';
    position: absolute;
    bottom: -80px; left: 40%;
    width: 160px; height: 160px;
    background: rgba(129,140,248,.06);
    border-radius: 50%;
}
.ah-content { position: relative; z-index: 1; }
.ah-content h2 {
    color: white; font-size: 1.375rem; font-weight: 800;
    font-family: 'Outfit', sans-serif; margin-bottom: 4px;
}
.ah-content p { color: rgba(255,255,255,.6); font-size: .875rem; margin: 0; }
.ah-period-select {
    position: relative; z-index: 1;
    padding: 8px 16px; border-radius: 10px;
    background: rgba(255,255,255,.15); color: white;
    border: 1.5px solid rgba(255,255,255,.25);
    font-size: .85rem; font-weight: 600; cursor: pointer;
    appearance: none; min-width: 140px;
}
.ah-period-select option { background: #3730a3; color: white; }

.analytics-kpis { display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 24px; }
.kpi-card {
    flex: 1; min-width: 170px;
    background: var(--surface);
    border: 1.5px solid var(--border-color);
    border-radius: 16px;
    padding: 20px;
    transition: all .2s;
    position: relative;
    overflow: hidden;
}
.kpi-card:hover { transform: translateY(-3px); box-shadow: 0 8px 28px rgba(0,0,0,.09); }
.kpi-card::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0; right: 0; height: 3px;
    border-radius: 0 0 14px 14px;
}
.kpi-card.indigo::after { background: linear-gradient(90deg, #4f46e5, #8b5cf6); }
.kpi-card.green::after  { background: linear-gradient(90deg, #10b981, #059669); }
.kpi-card.blue::after   { background: linear-gradient(90deg, #0ea5e9, #0284c7); }
.kpi-card.amber::after  { background: linear-gradient(90deg, #f59e0b, #d97706); }
.kpi-icon {
    width: 44px; height: 44px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem; margin-bottom: 14px;
}
.kpi-icon.indigo { background: rgba(79,70,229,.1);  color: #4338ca; }
.kpi-icon.green  { background: rgba(16,185,129,.1); color: #059669; }
.kpi-icon.blue   { background: rgba(14,165,233,.1); color: #0284c7; }
.kpi-icon.amber  { background: rgba(245,158,11,.1); color: #d97706; }
.kpi-label { font-size: .72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: .05em; }
.kpi-value { font-size: 1.5rem; font-weight: 800; color: var(--text-primary); margin: 6px 0 4px; font-family: 'Outfit', sans-serif; }
.kpi-change { font-size: .78rem; font-weight: 600; display: flex; align-items: center; gap: 4px; }
.kpi-change.up   { color: #059669; }
.kpi-change.down { color: #be123c; }

/* Charts layout */
.analytics-charts {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 20px;
    align-items: start;
}
@media (max-width: 960px) { .analytics-charts { grid-template-columns: 1fr; } }

.chart-card {
    background: var(--surface);
    border: 1.5px solid var(--border-color);
    border-radius: 20px; overflow: hidden;
    margin-bottom: 20px;
    transition: box-shadow .2s;
}
.chart-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.06); }
.chart-card:last-child { margin-bottom: 0; }
.chart-card-header {
    padding: 18px 22px;
    border-bottom: 1px solid var(--border-color);
    display: flex; align-items: center; justify-content: space-between; gap: 12px;
}
.chart-card-title { font-size: .9rem; font-weight: 800; color: var(--text-primary); font-family: 'Outfit', sans-serif; }
.chart-card-desc  { font-size: .73rem; color: var(--text-muted); margin-top: 2px; }
.chart-period-pill {
    padding: 5px 12px; border-radius: 20px;
    background: rgba(79,70,229,.08); color: #4338ca;
    font-size: .78rem; font-weight: 600;
    border: 1px solid rgba(79,70,229,.15);
}
.chart-card-body { padding: 20px 22px; }

/* Source bars */
.source-bar-item { margin-bottom: 18px; }
.source-bar-item:last-child { margin-bottom: 0; }
.source-bar-header { display: flex; justify-content: space-between; margin-bottom: 6px; }
.source-bar-label  { font-size: .8125rem; font-weight: 600; color: var(--text-primary); }
.source-bar-value  { font-size: .8125rem; font-weight: 800; color: var(--text-primary); }
.source-bar-track  { height: 8px; background: #f1f5f9; border-radius: 99px; overflow: hidden; }
.source-bar-fill   { height: 100%; border-radius: 99px; transition: width .6s ease; }
.sbf-indigo { background: linear-gradient(90deg, #4f46e5, #8b5cf6); }
.sbf-purple { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }
.sbf-green  { background: linear-gradient(90deg, #10b981, #34d399); }

.conversion-box {
    margin-top: 20px; padding: 16px;
    background: linear-gradient(135deg, rgba(79,70,229,.06), rgba(139,92,246,.06));
    border: 1.5px solid rgba(79,70,229,.12);
    border-radius: 14px; text-align: center;
}
.conv-label { font-size: .73rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: .04em; margin-bottom: 4px; }
.conv-value { font-size: 1.75rem; font-weight: 800; color: #4f46e5; font-family: 'Outfit', sans-serif; }
.conv-change { font-size: .78rem; color: #059669; font-weight: 600; margin-top: 3px; }
</style>
@endpush

@section('content')

{{-- ═══ HERO ═══ --}}
<div class="analytics-hero">
    <div class="ah-content">
        <h2><i class="fas fa-chart-line" style="margin-right:10px;opacity:.8;"></i>Platform Analytics</h2>
        <p>Monitor revenue, orders, sessions and acquisition insights across the platform.</p>
    </div>
    <select class="ah-period-select">
        <option>This Week</option>
        <option>Last 30 Days</option>
        <option>This Quarter</option>
        <option>This Year</option>
    </select>
</div>

{{-- ═══ KPI CARDS ═══ --}}
<div class="analytics-kpis">
    <div class="kpi-card indigo">
        <div class="kpi-icon indigo"><i class="fas fa-wallet"></i></div>
        <div class="kpi-label">Gross Volume</div>
        <div class="kpi-value">₦12.4M</div>
        <div class="kpi-change up"><i class="fas fa-caret-up"></i> +18.2% this week</div>
    </div>
    <div class="kpi-card green">
        <div class="kpi-icon green"><i class="fas fa-bag-shopping"></i></div>
        <div class="kpi-label">Total Orders</div>
        <div class="kpi-value">8,490</div>
        <div class="kpi-change up"><i class="fas fa-caret-up"></i> +7.4% this week</div>
    </div>
    <div class="kpi-card blue">
        <div class="kpi-icon blue"><i class="fas fa-users-viewfinder"></i></div>
        <div class="kpi-label">Daily Sessions</div>
        <div class="kpi-value">1,248</div>
        <div class="kpi-change up"><i class="fas fa-caret-up"></i> +12% vs last week</div>
    </div>
    <div class="kpi-card amber">
        <div class="kpi-icon amber"><i class="fas fa-store"></i></div>
        <div class="kpi-label">Active Vendors</div>
        <div class="kpi-value">{{ \App\Models\Vendor::where('status','approved')->count() }}</div>
        <div class="kpi-change up"><i class="fas fa-caret-up"></i> Growing</div>
    </div>
</div>

{{-- ═══ CHARTS ═══ --}}
<div class="analytics-charts">

    {{-- Revenue Chart --}}
    <div>
        <div class="chart-card">
            <div class="chart-card-header">
                <div>
                    <div class="chart-card-title">Revenue Stream</div>
                    <div class="chart-card-desc">Weekly volume flow of purchases and commissions.</div>
                </div>
                <span class="chart-period-pill">This Week</span>
            </div>
            <div class="chart-card-body">
                <div style="position:relative;height:300px;width:100%;">
                    <canvas id="revenueFlowChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Acquisition Sources --}}
    <div>
        <div class="chart-card">
            <div class="chart-card-header">
                <div>
                    <div class="chart-card-title">Acquisition Sources</div>
                    <div class="chart-card-desc">Customer landing origins breakdown.</div>
                </div>
            </div>
            <div class="chart-card-body">
                <div class="source-bar-item">
                    <div class="source-bar-header">
                        <span class="source-bar-label">Direct Navigation</span>
                        <span class="source-bar-value">45%</span>
                    </div>
                    <div class="source-bar-track">
                        <div class="source-bar-fill sbf-indigo" style="width:45%;"></div>
                    </div>
                </div>
                <div class="source-bar-item">
                    <div class="source-bar-header">
                        <span class="source-bar-label">Social Media</span>
                        <span class="source-bar-value">30%</span>
                    </div>
                    <div class="source-bar-track">
                        <div class="source-bar-fill sbf-purple" style="width:30%;"></div>
                    </div>
                </div>
                <div class="source-bar-item">
                    <div class="source-bar-header">
                        <span class="source-bar-label">Organic Search</span>
                        <span class="source-bar-value">25%</span>
                    </div>
                    <div class="source-bar-track">
                        <div class="source-bar-fill sbf-green" style="width:25%;"></div>
                    </div>
                </div>

                <div class="conversion-box">
                    <div class="conv-label">Conversion Rate</div>
                    <div class="conv-value">3.48%</div>
                    <div class="conv-change"><i class="fas fa-caret-up"></i> +0.4% this week</div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('revenueFlowChart').getContext('2d');

    const grad = ctx.createLinearGradient(0, 0, 0, 300);
    grad.addColorStop(0, 'rgba(79, 70, 229, 0.18)');
    grad.addColorStop(1, 'rgba(79, 70, 229, 0.0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Gross Volume (₦)',
                data: [1200000, 1850000, 1400000, 2200000, 1900000, 2800000, 3100000],
                borderColor: '#4f46e5',
                borderWidth: 3,
                backgroundColor: grad,
                fill: true,
                tension: 0.45,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#4f46e5',
                pointBorderWidth: 2.5,
                pointRadius: 5,
                pointHoverRadius: 8,
                pointHoverBackgroundColor: '#4f46e5',
                pointHoverBorderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e1b4b',
                    titleColor: 'rgba(255,255,255,.7)',
                    bodyColor: '#fff',
                    padding: 12,
                    cornerRadius: 10,
                    callbacks: {
                        label: function(c) { return ' ₦' + (c.raw / 1000000).toFixed(2) + 'M'; }
                    }
                }
            },
            scales: {
                y: {
                    grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false },
                    border: { display: false },
                    ticks: {
                        callback: v => '₦' + (v/1000) + 'k',
                        font: { size: 11 },
                        color: '#94a3b8'
                    }
                },
                x: {
                    grid: { display: false },
                    border: { display: false },
                    ticks: { font: { size: 11 }, color: '#94a3b8' }
                }
            }
        }
    });
});
</script>
@endpush
