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
<div class="row">
    <div class="col-8">
        <div class="dashboard-card mb-4">
            <div class="dashboard-card-header">
                <h3>Store Information</h3>
                <span class="badge badge-{{ $vendor->status == 'approved' ? 'success' : ($vendor->status == 'pending' ? 'warning' : 'danger') }}">
                    {{ ucfirst($vendor->status) }}
                </span>
            </div>
            <div class="dashboard-card-body">
                <div class="d-flex align-items-center mb-4">
                    <img src="{{ $vendor->logo_url }}" class="rounded-circle border" width="80" height="80">
                    <div class="ms-3">
                        <h4 class="mb-1">{{ $vendor->store_name }}</h4>
                        <p class="text-muted mb-0">Joined {{ $vendor->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
                
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label text-muted">Business Email</label>
                        <p class="fw-bold">{{ $vendor->business_email }}</p>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted">Business Phone</label>
                        <p class="fw-bold">{{ $vendor->business_phone }}</p>
                    </div>
                    <div class="col-12">
                        <label class="form-label text-muted">Address</label>
                        <p class="fw-bold">{{ $vendor->business_address }}, {{ $vendor->city }}, {{ $vendor->state }}, {{ $vendor->country }}</p>
                    </div>
                     <div class="col-12">
                        <label class="form-label text-muted">Description</label>
                        <p>{{ $vendor->store_description }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-card mb-4">
            <div class="dashboard-card-header">
                <h3>KYC Verification</h3>
                @php
                    $kycStatus = $vendor->kyc_status ?? 'not_submitted';
                    $kycBadge = match($kycStatus) {
                        'verified' => 'success',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        default => 'secondary',
                    };
                @endphp
                <span class="badge badge-{{ $kycBadge }}">
                    {{ ucfirst(str_replace('_', ' ', $kycStatus)) }}
                </span>
            </div>
            <div class="dashboard-card-body">
                @if($kycStatus === 'not_submitted')
                    <p class="text-muted text-center py-3">Vendor has not submitted KYC details yet.</p>
                @else
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted">ID Type</label>
                            <p class="fw-bold">{{ ucwords(str_replace('_', ' ', $vendor->id_type)) }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">ID Number</label>
                            <p class="fw-bold">{{ $vendor->id_number }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">NIN</label>
                            <p class="fw-bold">{{ $vendor->nin ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">BVN</label>
                            <p class="fw-bold">{{ $vendor->bvn ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">CAC Number</label>
                            <p class="fw-bold">{{ $vendor->cac_number ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Verification Date</label>
                            <p class="fw-bold">{{ $vendor->kyc_verified_at ? $vendor->kyc_verified_at->format('M d, Y H:i') : 'Pending' }}</p>
                        </div>
                    </div>

                    <h6 class="mb-3 border-bottom pb-2">Uploaded Documents</h6>
                    <div class="row g-3 mb-4">
                        @if($vendor->id_document_path)
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3 border rounded bg-light">
                                <i class="fas fa-id-card fa-2x text-primary me-3"></i>
                                <div>
                                    <h6 class="mb-0">ID Document</h6>
                                    <a href="{{ Storage::url($vendor->id_document_path) }}" target="_blank" class="small text-decoration-none">View Document</a>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if($vendor->cac_document_path)
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3 border rounded bg-light">
                                <i class="fas fa-file-contract fa-2x text-info me-3"></i>
                                <div>
                                    <h6 class="mb-0">CAC Certificate</h6>
                                    <a href="{{ Storage::url($vendor->cac_document_path) }}" target="_blank" class="small text-decoration-none">View Certificate</a>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    @if($kycStatus === 'verified')
                    <hr>
                    <div class="alert alert-success d-flex align-items-center gap-2 mb-0">
                        <i class="fas fa-check-circle fa-lg"></i>
                        <div>
                            <strong>KYC Verified</strong> — This vendor's identity has been verified on {{ $vendor->kyc_verified_at ? $vendor->kyc_verified_at->format('M d, Y') : 'N/A' }}.
                        </div>
                    </div>
                    @elseif($kycStatus === 'pending' || $kycStatus === 'rejected')
                    <hr>
                    <h6 class="mb-3">Verification Actions</h6>
                    <div class="d-flex gap-2">
                        <form action="{{ route($prefix.'vendors.kyc', $vendor) }}" method="POST" class="w-50">
                            @csrf
                            <input type="hidden" name="status" value="verified">
                            <button type="submit" class="btn btn-success w-100" onclick="return confirm('Are you sure you want to verify this vendor\'s identity? This will also approve their account if pending.')">
                                <i class="fas fa-check-circle me-1"></i> Approve KYC
                            </button>
                        </form>
                        
                        <button type="button" class="btn btn-danger w-50" onclick="document.getElementById('rejectKycForm').style.display = 'block'">
                            <i class="fas fa-times-circle me-1"></i> Reject KYC
                        </button>
                    </div>

                    <form id="rejectKycForm" action="{{ route($prefix.'vendors.kyc', $vendor) }}" method="POST" class="mt-3" style="display: none;">
                        @csrf
                        <input type="hidden" name="status" value="rejected">
                        <div class="form-group mb-2">
                            <label>Rejection Reason</label>
                            <textarea name="reason" class="form-control" rows="2" placeholder="Explain why the KYC documents were rejected..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger btn-sm">Confirm Rejection</button>
                    </form>
                    @endif
                @endif
            </div>
        </div>

        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3>Documents</h3>
            </div>
            <div class="dashboard-card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Document Name</th>
                                <th>Type</th>
                                <th>Uploaded</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vendor->documents as $doc)
                                <tr>
                                    <td>{{ $doc->name }}</td>
                                    <td>{{ $doc->type }}</td>
                                    <td>{{ $doc->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ Storage::url($doc->path) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">No documents uploaded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-4">
        <div class="dashboard-card mb-4">
            <div class="dashboard-card-header">
                <h3>Quick Actions</h3>
            </div>
            <div class="dashboard-card-body d-flex flex-column gap-2">
                @if($vendor->status == 'pending')
                    <form action="{{ route($prefix.'vendors.status', $vendor) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="approved">
                        <button class="btn btn-success w-100">Approve Vendor</button>
                    </form>
                    <form action="{{ route($prefix.'vendors.status', $vendor) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="rejected">
                        <button class="btn btn-danger w-100">Reject Vendor</button>
                    </form>
                @endif

                @if($vendor->status == 'approved')
                    <form action="{{ route($prefix.'vendors.status', $vendor) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="suspended">
                        <button class="btn btn-warning w-100">Suspend Account</button>
                    </form>
                @endif
                
                @if($vendor->status == 'suspended')
                    <form action="{{ route($prefix.'vendors.status', $vendor) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="approved">
                        <button class="btn btn-success w-100">Reactivate Account</button>
                    </form>
                @endif

                <a href="mailto:{{ $vendor->user->email }}" class="btn btn-secondary w-100">Contact Vendor</a>
            </div>
        </div>
        
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3>Performance</h3>
            </div>
            <div class="dashboard-card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Sales</span>
                    <strong>₦{{ number_format($vendor->total_sales) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Active Products</span>
                    <strong>{{ $vendor->products->where('status', 'active')->count() }}</strong>
                </div>
                 <div class="d-flex justify-content-between">
                    <span>Rating</span>
                    <strong>{{ $vendor->rating }} / 5.0</strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
