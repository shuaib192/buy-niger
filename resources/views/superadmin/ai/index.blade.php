{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    View: Super Admin - AI Control Panel
--}}
@extends('layouts.app')

@section('title', 'AI Control Panel')
@section('page_title', 'AI Control Panel')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
@php
    $prefix = request()->is('admin*') ? 'admin.' : 'superadmin.';
@endphp
<div class="row mb-4">
    <div class="col-12">
        <div style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); border-radius: 16px; padding: 2rem; color: white; position: relative; overflow: hidden;">
            <div style="position: relative; z-index: 2;">
                <h1 style="font-weight: 800; font-size: 2rem; margin-bottom: 0.5rem; color: white;">AI Neural Core</h1>
                <p style="opacity: 0.9; font-size: 1.1rem; max-width: 600px;">
                    Monitor and control the autonomous agents managing your marketplace. 
                    Real-time oversight of content moderation, customer support, and fraud detection.
                </p>
                <div class="d-flex gap-3 mt-4">
                    <button class="btn btn-light text-primary fw-bold px-4">
                        <i class="fas fa-file-alt me-2"></i> View Logs
                    </button>
                    <button class="btn btn-outline-light fw-bold px-4">
                        <i class="fas fa-cog me-2"></i> Configure Agents
                    </button>
                </div>
            </div>
            <div style="position: absolute; right: -20px; top: -20px; opacity: 0.1; font-size: 15rem;">
                <i class="fas fa-robot"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Status Card -->
    <div class="col-md-4">
        <div class="dashboard-card h-100">
            <div class="dashboard-card-header">
                <h3>System Status</h3>
                <span class="badge badge-success">Operational</span>
            </div>
            <div class="dashboard-card-body">
                <div class="text-center py-4">
                    <div style="width: 120px; height: 120px; background: rgba(16, 185, 129, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                        <i class="fas fa-shield-alt text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h4>All Systems Normal</h4>
                    <p class="text-muted" style="font-size: 0.9rem;">The AI is actively monitoring 1,240 items and 850 users.</p>
                </div>
                
                <hr class="dropdown-divider my-3">
                
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">CPU Usage</span>
                    <span class="fw-bold small">12%</span>
                </div>
                <div class="progress mb-3" style="height: 6px;">
                    <div class="progress-bar bg-success" style="width: 12%"></div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">Memory Load</span>
                    <span class="fw-bold small">45%</span>
                </div>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-primary" style="width: 45%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Agents -->
    <div class="col-md-8">
        <div class="dashboard-card h-100">
            <div class="dashboard-card-header">
                <h3>Active Agents</h3>
                <button class="btn btn-sm btn-primary">Deploy New Agent</button>
            </div>
            <div class="dashboard-card-body">
                <div class="row g-3">
                    <!-- Agent Card 1 -->
                    <div class="col-md-6">
                        <div style="border: 1px solid var(--secondary-200); border-radius: 12px; padding: 1rem; transition: all 0.2s;">
                            <div class="d-flex justify-content-between mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-primary-light p-2 rounded-circle text-primary">
                                        <i class="fas fa-search"></i>
                                    </div>
                                    <div style="font-weight: 600;">Content Moderator</div>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" checked>
                                </div>
                            </div>
                            <p class="small text-muted mb-3">Scans new products for prohibited content and verifies categories.</p>
                            <div class="d-flex justify-content-between align-items-center small">
                                <span class="badge bg-light text-dark border">Shadow Mode</span>
                                <span class="text-success"><i class="fas fa-circle fa-xs me-1"></i> Running</span>
                            </div>
                        </div>
                    </div>

                    <!-- Agent Card 2 -->
                    <div class="col-md-6">
                        <div style="border: 1px solid var(--secondary-200); border-radius: 12px; padding: 1rem; transition: all 0.2s;">
                            <div class="d-flex justify-content-between mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-purple-light p-2 rounded-circle text-purple">
                                        <i class="fas fa-headset"></i>
                                    </div>
                                    <div style="font-weight: 600;">Support Bot</div>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" checked>
                                </div>
                            </div>
                            <p class="small text-muted mb-3">Handles Level 1 customer inquiries and routes complex cases.</p>
                            <div class="d-flex justify-content-between align-items-center small">
                                <span class="badge bg-light text-dark border">Active</span>
                                <span class="text-success"><i class="fas fa-circle fa-xs me-1"></i> Running</span>
                            </div>
                        </div>
                    </div>

                    <!-- Agent Card 3 -->
                    <div class="col-md-6">
                        <div style="border: 1px solid var(--secondary-200); border-radius: 12px; padding: 1rem; transition: all 0.2s;">
                            <div class="d-flex justify-content-between mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-orange-light p-2 rounded-circle text-orange">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                    <div style="font-weight: 600;">Pricing Assistant</div>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox">
                                </div>
                            </div>
                            <p class="small text-muted mb-3">Analyzes market trends to suggest optimal pricing for vendors.</p>
                            <div class="d-flex justify-content-between align-items-center small">
                                <span class="badge bg-light text-dark border">Paused</span>
                                <span class="text-secondary"><i class="fas fa-pause-circle fa-xs me-1"></i> Inactive</span>
                            </div>
                        </div>
                    </div>

                    <!-- Agent Card 4 -->
                    <div class="col-md-6">
                        <div style="border: 1px solid var(--secondary-200); border-radius: 12px; padding: 1rem; transition: all 0.2s;">
                            <div class="d-flex justify-content-between mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-success-light p-2 rounded-circle text-success">
                                        <i class="fas fa-shield-virus"></i>
                                    </div>
                                    <div style="font-weight: 600;">Fraud Guard</div>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" checked>
                                </div>
                            </div>
                            <p class="small text-muted mb-3">Detects suspicious order patterns and high-risk transactions.</p>
                            <div class="d-flex justify-content-between align-items-center small">
                                <span class="badge bg-light text-dark border">High Alert</span>
                                <span class="text-success"><i class="fas fa-circle fa-xs me-1"></i> Running</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Actions -->
    <div class="col-12">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3>Recent AI Actions</h3>
                <div class="dropdown">
                    <button class="btn btn-sm btn-icon btn-outline-secondary" data-bs-toggle="dropdown">
                        <i class="fas fa-filter"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">All Actions</a></li>
                        <li><a class="dropdown-item" href="#">Flagged Items</a></li>
                        <li><a class="dropdown-item" href="#">Auto-Replies</a></li>
                    </ul>
                </div>
            </div>
            <div class="dashboard-card-body">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>Agent</th>
                                <th>Action</th>
                                <th>Details</th>
                                <th>Impact</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-muted small">Just now</td>
                                <td>
                                    <span class="badge bg-primary-light text-primary">Content Moderator</span>
                                </td>
                                <td>Flagged Product</td>
                                <td>Found prohibited keywords in "Herbal Supplement X"</td>
                                <td>High</td>
                                <td><span class="badge badge-warning">Review Needed</span></td>
                            </tr>
                            <tr>
                                <td class="text-muted small">5 mins ago</td>
                                <td>
                                    <span class="badge bg-purple-light text-purple">Support Bot</span>
                                </td>
                                <td>Answered Inquiry</td>
                                <td>Provided shipping details to User #882</td>
                                <td>Low</td>
                                <td><span class="badge badge-success">Completed</span></td>
                            </tr>
                            <tr>
                                <td class="text-muted small">12 mins ago</td>
                                <td>
                                    <span class="badge bg-success-light text-success">Fraud Guard</span>
                                </td>
                                <td>Flagged Order</td>
                                <td>Suspicious velocity on User #991 (3 orders in 10 mins)</td>
                                <td>Critical</td>
                                <td><span class="badge badge-danger">Blocked</span></td>
                            </tr>
                            <tr>
                                <td class="text-muted small">1 hour ago</td>
                                <td>
                                    <span class="badge bg-primary-light text-primary">Content Moderator</span>
                                </td>
                                <td>Approved Product</td>
                                <td>"iPhone 15 Pro Max" passed all checks</td>
                                <td>Medium</td>
                                <td><span class="badge badge-success">Auto-Approved</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-primary-light { background-color: rgba(79, 70, 229, 0.1); }
    .bg-purple-light { background-color: rgba(147, 51, 234, 0.1); }
    .bg-orange-light { background-color: rgba(249, 115, 22, 0.1); }
    .bg-success-light { background-color: rgba(16, 185, 129, 0.1); }
    
    .text-purple { color: #9333ea; }
    .text-orange { color: #f97316; }
</style>
@endsection
