@extends('layouts.app')

@section('title', 'Platform Analytics')
@section('page_title', 'Analytics')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
<div class="row g-4">
    <!-- Revenue Chart Placeholder -->
    <div class="col-8">
        <div class="dashboard-card h-100">
            <div class="dashboard-card-header">
                <h3>Revenue Overview</h3>
                <select class="form-select form-select-sm" style="width: auto;">
                    <option>This Year</option>
                    <option>Last Year</option>
                </select>
            </div>
            <div class="dashboard-card-body d-flex align-items-center justify-content-center" style="min-height: 300px; background: var(--bg-surface);">
                <div class="text-center text-muted">
                    <i class="fas fa-chart-area fa-3x mb-3"></i>
                    <p>Revenue Chart Visualization</p>
                    <small>Requires Chart.js integration</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Traffic Stats -->
    <div class="col-4">
        <div class="dashboard-card h-100">
            <div class="dashboard-card-header">
                <h3>Traffic Sources</h3>
            </div>
            <div class="dashboard-card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span>Direct</span>
                    <span class="fw-bold">45%</span>
                </div>
                <div class="progress mb-4" style="height: 6px;">
                    <div class="progress-bar bg-primary" style="width: 45%"></div>
                </div>

                <div class="d-flex justify-content-between mb-3">
                    <span>Social Media</span>
                    <span class="fw-bold">30%</span>
                </div>
                <div class="progress mb-4" style="height: 6px;">
                    <div class="progress-bar bg-info" style="width: 30%"></div>
                </div>

                <div class="d-flex justify-content-between mb-3">
                    <span>Organic Search</span>
                    <span class="fw-bold">25%</span>
                </div>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-success" style="width: 25%"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
