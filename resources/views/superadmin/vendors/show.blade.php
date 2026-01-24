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
