{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin
    View: Admin — Vendor Details — Premium v2.0
--}}
@extends('layouts.app')

@section('title', 'Vendor Details')
@section('page_title', 'Vendor Details')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
@php
    $prefix = request()->is('admin*') ? 'admin.' : 'superadmin.';
@endphp

<div class="row g-4">
    <div class="col-lg-8">
        <!-- Store Information -->
        <div class="dashboard-card mb-4">
            <div class="dashboard-card-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle border overflow-hidden bg-light" style="width: 60px; height: 60px; flex-shrink: 0;">
                        <img src="{{ $vendor->logo_url }}" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div>
                        <h3 class="mb-0">{{ $vendor->store_name }}</h3>
                        <p class="text-muted small mb-0">Registered storefront on {{ $vendor->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
                
                @php
                    $statusClass = match($vendor->status) {
                        'approved' => 'badge-success',
                        'pending' => 'badge-warning',
                        'suspended' => 'badge-danger',
                        default => 'badge-secondary',
                    };
                @endphp
                <span class="badge {{ $statusClass }} px-3 py-2 rounded-pill">
                    <i class="fas fa-store me-1"></i> Account: {{ ucfirst($vendor->status) }}
                </span>
            </div>
            <div class="dashboard-card-body">
                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <label class="form-label text-muted small fw-bold uppercase" style="font-size: 10px; letter-spacing:0.05em;">Business Email Address</label>
                        <div class="text-dark fw-semibold small">{{ $vendor->business_email }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small fw-bold uppercase" style="font-size: 10px; letter-spacing:0.05em;">Business Telephone Contact</label>
                        <div class="text-dark fw-semibold small">{{ $vendor->business_phone }}</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label text-muted small fw-bold uppercase" style="font-size: 10px; letter-spacing:0.05em;">Registered Address Details</label>
                        <div class="text-dark fw-semibold small">{{ $vendor->business_address }}, {{ $vendor->city }}, {{ $vendor->state }}, {{ $vendor->country }}</div>
                    </div>
                     <div class="col-12">
                        <label class="form-label text-muted small fw-bold uppercase" style="font-size: 10px; letter-spacing:0.05em;">Storefront Bio / Description</label>
                        <div class="p-3 border bg-light rounded-3 text-dark small" style="line-height: 1.5; border-color: var(--border-color) !important;">{{ $vendor->store_description ?? 'No description provided.' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KYC Verification -->
        <div class="dashboard-card mb-4">
            <div class="dashboard-card-header">
                <div>
                    <h3 class="mb-1">KYC Credentials Verification</h3>
                    <p class="text-muted small mb-0">Legal business identity validation details and uploaded proof files.</p>
                </div>
                @php
                    $kycStatus = $vendor->kyc_status ?? 'not_submitted';
                    $kycBadge = match($kycStatus) {
                        'verified' => 'badge-success',
                        'pending' => 'badge-warning',
                        'rejected' => 'badge-danger',
                        default => 'badge-secondary',
                    };
                @endphp
                <span class="badge {{ $kycBadge }} px-3 py-2 rounded-pill">
                    <i class="fas fa-id-card me-1"></i> KYC: {{ ucfirst(str_replace('_', ' ', $kycStatus)) }}
                </span>
            </div>
            <div class="dashboard-card-body">
                @if($kycStatus === 'not_submitted')
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-triangle-exclamation fa-3x mb-3 text-muted"></i>
                        <h5 class="text-muted">No KYC Data Received</h5>
                        <p class="text-muted small">This vendor has not submitted identity or incorporation credentials yet.</p>
                    </div>
                @else
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <label class="form-label text-muted small fw-bold uppercase" style="font-size: 10px; letter-spacing: 0.05em;">ID Document Type</label>
                            <div class="text-dark fw-bold small">{{ ucwords(str_replace('_', ' ', $vendor->id_type)) }}</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-muted small fw-bold uppercase" style="font-size: 10px; letter-spacing: 0.05em;">ID Number Reference</label>
                            <div class="text-dark fw-bold small">{{ $vendor->id_number }}</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-muted small fw-bold uppercase" style="font-size: 10px; letter-spacing: 0.05em;">NIN (National ID Number)</label>
                            <div class="text-dark fw-bold small">{{ $vendor->nin ?? 'N/A' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-muted small fw-bold uppercase" style="font-size: 10px; letter-spacing: 0.05em;">BVN (Bank Verification Number)</label>
                            <div class="text-dark fw-bold small">{{ $vendor->bvn ?? 'N/A' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-muted small fw-bold uppercase" style="font-size: 10px; letter-spacing: 0.05em;">CAC Registration Number</label>
                            <div class="text-dark fw-bold small">{{ $vendor->cac_number ?? 'N/A' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-muted small fw-bold uppercase" style="font-size: 10px; letter-spacing: 0.05em;">Verification Timestamp</label>
                            <div class="text-dark fw-bold small">{{ $vendor->kyc_verified_at ? $vendor->kyc_verified_at->format('M d, Y H:i') : 'Pending Review' }}</div>
                        </div>
                    </div>

                    <h6 class="mb-3 border-bottom pb-2 text-dark fw-bold small">ATTACHED CREDENTIAL FILES</h6>
                    <div class="row g-3 mb-4">
                        @if($vendor->id_document_path)
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center p-3 border rounded-3 bg-light" style="border-color: var(--border-color) !important;">
                                <i class="fas fa-passport fa-2x text-primary me-3"></i>
                                <div>
                                    <h6 class="mb-1 text-dark fw-bold small">ID Proof File</h6>
                                    <a href="{{ Storage::url($vendor->id_document_path) }}" target="_blank" class="small text-indigo fw-semibold text-decoration-none"><i class="fas fa-arrow-up-right-from-square me-1"></i> Open Document</a>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if($vendor->cac_document_path)
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center p-3 border rounded-3 bg-light" style="border-color: var(--border-color) !important;">
                                <i class="fas fa-file-signature fa-2x text-info me-3"></i>
                                <div>
                                    <h6 class="mb-1 text-dark fw-bold small">CAC Document</h6>
                                    <a href="{{ Storage::url($vendor->cac_document_path) }}" target="_blank" class="small text-indigo fw-semibold text-decoration-none"><i class="fas fa-arrow-up-right-from-square me-1"></i> Open Certificate</a>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    @if($kycStatus === 'verified')
                    <div class="alert alert-success d-flex align-items-center gap-3 mb-0 rounded-3">
                        <i class="fas fa-circle-check fa-lg text-success"></i>
                        <div>
                            <strong class="text-success small d-block">KYC Verified</strong>
                            <span class="small" style="font-size:11px; color:#065f46;">Store identity credentials verified on {{ $vendor->kyc_verified_at ? $vendor->kyc_verified_at->format('M d, Y') : 'N/A' }}.</span>
                        </div>
                    </div>
                    @elseif($kycStatus === 'pending' || $kycStatus === 'rejected')
                    <h6 class="mb-3 text-dark fw-bold small">VERIFICATION REVIEW ACTION</h6>
                    <div class="d-flex gap-2">
                        <form action="{{ route($prefix.'vendors.kyc', $vendor) }}" method="POST" class="w-50">
                            @csrf
                            <input type="hidden" name="status" value="verified">
                            <button type="submit" class="btn btn-success w-100 rounded-pill" onclick="return confirm('Confirm identity validation approval? This activates the store.')">
                                <i class="fas fa-check-circle me-1"></i> Approve Identity
                            </button>
                        </form>
                        
                        <button type="button" class="btn btn-danger w-50 rounded-pill" onclick="document.getElementById('rejectKycForm').style.display = 'block'">
                            <i class="fas fa-times-circle me-1"></i> Reject Documents
                        </button>
                    </div>

                    <form id="rejectKycForm" action="{{ route($prefix.'vendors.kyc', $vendor) }}" method="POST" class="mt-3 p-3 border rounded-3 bg-light text-start" style="display: none; border-color: var(--border-color) !important;">
                        @csrf
                        <input type="hidden" name="status" value="rejected">
                        <div class="mb-3">
                            <label class="form-label text-dark small fw-semibold">Explanation Notes</label>
                            <textarea name="reason" class="form-control" rows="2" placeholder="Detail the reasons for document rejection..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3">Confirm Rejection</button>
                    </form>
                    @endif
                @endif
            </div>
        </div>

        <!-- Documents -->
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3>Additional Shared Documents</h3>
            </div>
            <div class="dashboard-card-body p-0">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="ps-4">Document Title</th>
                                <th>Category / Type</th>
                                <th>Uploaded Date</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vendor->documents as $doc)
                                <tr>
                                    <td class="ps-4">
                                        <span class="text-dark fw-semibold small">{{ $doc->name }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary text-uppercase" style="font-size:9px;">{{ $doc->type }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted small">{{ $doc->created_at->format('M d, Y') }}</span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ Storage::url($doc->path) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            <i class="fas fa-arrow-up-right-from-square me-1"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted small">No secondary documents uploaded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Sidebar -->
    <div class="col-lg-4">
        <div class="dashboard-card mb-4">
            <div class="dashboard-card-header">
                <h3>Storefront Moderation</h3>
            </div>
            <div class="dashboard-card-body d-flex flex-column gap-2">
                @if($vendor->status == 'pending')
                    <form action="{{ route($prefix.'vendors.status', $vendor) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="approved">
                        <button class="btn btn-success w-100 rounded-pill"><i class="fas fa-check-circle me-1"></i> Approve Store</button>
                    </form>
                    <form action="{{ route($prefix.'vendors.status', $vendor) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="rejected">
                        <button class="btn btn-danger w-100 rounded-pill"><i class="fas fa-times-circle me-1"></i> Reject Store</button>
                    </form>
                @endif

                @if($vendor->status == 'approved')
                    <form action="{{ route($prefix.'vendors.status', $vendor) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="suspended">
                        <button class="btn btn-warning w-100 rounded-pill"><i class="fas fa-circle-pause me-1"></i> Suspend Storefront</button>
                    </form>
                @endif
                
                @if($vendor->status == 'suspended')
                    <form action="{{ route($prefix.'vendors.status', $vendor) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="approved">
                        <button class="btn btn-success w-100 rounded-pill"><i class="fas fa-play me-1"></i> Reactivate Storefront</button>
                    </form>
                @endif

                <a href="mailto:{{ $vendor->user->email }}" class="btn btn-secondary w-100 rounded-pill">
                    <i class="fas fa-envelope me-1"></i> Send Private Email
                </a>
            </div>
        </div>
        
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3>Store Metrics</h3>
            </div>
            <div class="dashboard-card-body">
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Total Platform Sales</span>
                        <strong class="text-success" style="font-size:1.1rem;">₦{{ number_format($vendor->total_sales) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Moderated Items</span>
                        <strong class="text-dark">{{ $vendor->active_products_count }}</strong>
                    </div>
                     <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Aggregated Reviews</span>
                        <div class="d-flex align-items-center gap-1 text-warning">
                            <i class="fas fa-star"></i>
                            <strong class="text-dark small">{{ $vendor->rating }} / 5.0</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
