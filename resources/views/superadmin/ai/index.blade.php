{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin
    View: Super Admin - AI Control Panel — Premium v2.0
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
        <div style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); border-radius: 16px; padding: 2.5rem 2rem; color: white; position: relative; overflow: hidden; box-shadow: var(--shadow-card-hover);">
            <div style="position: relative; z-index: 2; max-width: 650px;">
                <span class="badge bg-white text-indigo fw-bold mb-2 uppercase px-3 py-1 text-xs" style="letter-spacing:0.05em;"><i class="fas fa-brain me-1"></i> COGNITIVE LAYER</span>
                <h1 style="font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 2.25rem; margin-bottom: 0.5rem; color: white;">AI Neural Core</h1>
                <p style="opacity: 0.9; font-size: 1.1rem; line-height: 1.6;">
                    Monitor and coordinate the autonomous agents managing your marketplace. 
                    Real-time oversight of category verification, automated level 1 customer support, and financial fraud prevention.
                </p>
                <div class="d-flex gap-3 mt-4">
                    <a href="{{ route($prefix.'audit') }}" class="btn btn-light text-indigo fw-bold px-4 rounded-pill">
                        <i class="fas fa-file-waveform me-2"></i> Audit Logs
                    </a>
                    <a href="{{ route($prefix.'ai.settings') }}" class="btn btn-outline-light fw-bold px-4 rounded-pill">
                        <i class="fas fa-sliders me-2"></i> Configure AI Providers
                    </a>
                </div>
            </div>
            <div style="position: absolute; right: 20px; top: 50%; transform: translateY(-50%); opacity: 0.08; font-size: 16rem; pointer-events: none;">
                <i class="fas fa-robot"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Status Card -->
    <div class="col-lg-4">
        <div class="dashboard-card h-100">
            <div class="dashboard-card-header">
                <h3>System Telemetry</h3>
                <span class="badge badge-success"><i class="fas fa-circle-nodes me-1"></i> Operational</span>
            </div>
            <div class="dashboard-card-body">
                <div class="text-center py-4">
                    <div style="width: 110px; height: 110px; background: rgba(16, 185, 129, 0.08); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; border: 2px dashed rgba(16, 185, 129, 0.3);">
                        <i class="fas fa-microchip text-success" style="font-size: 2.5rem;"></i>
                    </div>
                    <h4 class="fw-bold text-dark">All Systems Active</h4>
                    <p class="text-muted small px-3">Autonomous guardrails are actively monitoring new items, feedback comments, and order sequences.</p>
                </div>
                
                <hr class="my-4" style="border-color: var(--border-color);">
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted small fw-medium">Core Compute Load</span>
                        <span class="fw-bold small text-dark">12%</span>
                    </div>
                    <div class="progress" style="height: 6px; border-radius: 99px;">
                        <div class="progress-bar bg-success" style="width: 12%; border-radius: 99px;"></div>
                    </div>
                </div>

                <div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted small fw-medium">Memory Allocation</span>
                        <span class="fw-bold small text-dark">45%</span>
                    </div>
                    <div class="progress" style="height: 6px; border-radius: 99px;">
                        <div class="progress-bar bg-primary" style="width: 45%; border-radius: 99px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Agents -->
    <div class="col-lg-8">
        <div class="dashboard-card h-100">
            <div class="dashboard-card-header">
                <h3>Autonomous Agent Fleet</h3>
                <span class="text-muted small fw-semibold">4 Micro-Agents Configured</span>
            </div>
            <div class="dashboard-card-body">
                <div class="row g-3">
                    <!-- Agent Card 1 -->
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-light" style="border-color: var(--border-color) !important; transition: all 0.2s;">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-indigo-subtle p-2 rounded-3 text-indigo" style="background: rgba(79, 70, 229, 0.1); width: 34px; height: 34px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-eye-slash"></i>
                                    </div>
                                    <div class="fw-bold text-dark">Content Moderator</div>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" checked style="cursor:pointer;">
                                </div>
                            </div>
                            <p class="small text-muted mb-3" style="min-height: 40px; font-size: 12px; line-height: 1.5;">Scans newly created product details and images for prohibited keywords and policy violations.</p>
                            <div class="d-flex justify-content-between align-items-center small pt-2 border-top">
                                <span class="badge badge-secondary">Shadow Mode</span>
                                <span class="text-success fw-bold" style="font-size: 11px;"><i class="fas fa-circle fa-xs me-1"></i> Running</span>
                            </div>
                        </div>
                    </div>

                    <!-- Agent Card 2 -->
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-light" style="border-color: var(--border-color) !important; transition: all 0.2s;">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-purple-subtle p-2 rounded-3 text-purple" style="background: rgba(139, 92, 246, 0.1); width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; color: var(--purple) !important;">
                                        <i class="fas fa-comment-dots"></i>
                                    </div>
                                    <div class="fw-bold text-dark">Support Assistant</div>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" checked style="cursor:pointer;">
                                </div>
                            </div>
                            <p class="small text-muted mb-3" style="min-height: 40px; font-size: 12px; line-height: 1.5;">Engages customers via the chatbot, answering questions and escalating cases to support teams.</p>
                            <div class="d-flex justify-content-between align-items-center small pt-2 border-top">
                                <span class="badge badge-primary">Active Responder</span>
                                <span class="text-success fw-bold" style="font-size: 11px;"><i class="fas fa-circle fa-xs me-1"></i> Running</span>
                            </div>
                        </div>
                    </div>

                    <!-- Agent Card 3 -->
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-light" style="border-color: var(--border-color) !important; transition: all 0.2s;">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-warning-subtle p-2 rounded-3 text-warning" style="background: rgba(245, 158, 11, 0.1); width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; color: var(--amber) !important;">
                                        <i class="fas fa-tags"></i>
                                    </div>
                                    <div class="fw-bold text-dark">Pricing Analyst</div>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" style="cursor:pointer;">
                                </div>
                            </div>
                            <p class="small text-muted mb-3" style="min-height: 40px; font-size: 12px; line-height: 1.5;">Analyzes competitor listing prices and suggests optimization recommendations to vendor portals.</p>
                            <div class="d-flex justify-content-between align-items-center small pt-2 border-top">
                                <span class="badge badge-secondary">Offline</span>
                                <span class="text-muted fw-bold" style="font-size: 11px;"><i class="fas fa-pause-circle fa-xs me-1"></i> Paused</span>
                            </div>
                        </div>
                    </div>

                    <!-- Agent Card 4 -->
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-light" style="border-color: var(--border-color) !important; transition: all 0.2s;">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-danger-subtle p-2 rounded-3 text-danger" style="background: rgba(244, 63, 94, 0.1); width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; color: var(--rose) !important;">
                                        <i class="fas fa-user-shield"></i>
                                    </div>
                                    <div class="fw-bold text-dark">Fraud Guard</div>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" checked style="cursor:pointer;">
                                </div>
                            </div>
                            <p class="small text-muted mb-3" style="min-height: 40px; font-size: 12px; line-height: 1.5;">Monitors high-velocity order sequences and checks geographical consistency to intercept card fraud.</p>
                            <div class="d-flex justify-content-between align-items-center small pt-2 border-top">
                                <span class="badge badge-danger">High Alert</span>
                                <span class="text-success fw-bold" style="font-size: 11px;"><i class="fas fa-circle fa-xs me-1"></i> Running</span>
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
                <div>
                    <h3 class="mb-1">Recent AI Neural Decisions</h3>
                    <p class="text-muted small mb-0">Chronological list of audit decisions executed autonomously by active agents.</p>
                </div>
            </div>
            <div class="dashboard-card-body p-0">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="ps-4">Timestamp</th>
                                <th>Agent</th>
                                <th>Action Taken</th>
                                <th>Decision Context / Details</th>
                                <th>Risk Impact</th>
                                <th class="text-end pe-4">Resolution Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="ps-4 text-muted small">Just now</td>
                                <td>
                                    <span class="badge badge-primary"><i class="fas fa-eye-slash me-1"></i> Content Moderator</span>
                                </td>
                                <td><span class="fw-semibold text-dark">Flagged Product Listing</span></td>
                                <td>
                                    <span class="text-dark small">Detected prohibited medical statements in product "Herbal Remedy X"</span>
                                </td>
                                <td><span class="badge badge-warning">High Risk</span></td>
                                <td class="text-end pe-4">
                                    <span class="badge badge-warning"><i class="fas fa-circle-question me-1"></i> Review Needed</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-4 text-muted small">5 mins ago</td>
                                <td>
                                    <span class="badge badge-secondary" style="color: var(--purple); background: rgba(139, 92, 246, 0.1);"><i class="fas fa-comment-dots me-1"></i> Support Assistant</span>
                                </td>
                                <td><span class="fw-semibold text-dark">Dispatched Auto-Response</span></td>
                                <td>
                                    <span class="text-dark small">Sent tracking information to Customer #882 for Order #39281</span>
                                </td>
                                <td><span class="badge badge-secondary">Low Risk</span></td>
                                <td class="text-end pe-4">
                                    <span class="badge badge-success"><i class="fas fa-circle-check me-1"></i> Auto-Closed</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-4 text-muted small">12 mins ago</td>
                                <td>
                                    <span class="badge badge-danger"><i class="fas fa-user-shield me-1"></i> Fraud Guard</span>
                                </td>
                                <td><span class="fw-semibold text-dark">Intercepted Card Checkout</span></td>
                                <td>
                                    <span class="text-dark small">Blocked transaction velocity spike on User Account #991 (3 tries in 60s)</span>
                                </td>
                                <td><span class="badge badge-danger">Critical</span></td>
                                <td class="text-end pe-4">
                                    <span class="badge badge-danger"><i class="fas fa-ban me-1"></i> Action Blocked</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-4 text-muted small">1 hour ago</td>
                                <td>
                                    <span class="badge badge-primary"><i class="fas fa-eye-slash me-1"></i> Content Moderator</span>
                                </td>
                                <td><span class="fw-semibold text-dark">Auto-Approved Listing</span></td>
                                <td>
                                    <span class="text-dark small">"iPhone 15 Pro Max" listing scanned and approved automatically</span>
                                </td>
                                <td><span class="badge badge-secondary">Medium Risk</span></td>
                                <td class="text-end pe-4">
                                    <span class="badge badge-success"><i class="fas fa-circle-check me-1"></i> Auto-Approved</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
