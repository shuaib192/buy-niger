{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin
    View: Vendor Store Settings — Premium v2.0
--}}
@extends('layouts.app')

@section('title', 'Store Settings')
@section('page_title', 'Store Settings')

@section('sidebar')
    @include('vendor.partials.sidebar')
@endsection

@section('content')
<form action="{{ route('vendor.settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-4">
        {{-- Store Information --}}
        <div class="col-lg-8">
            <div class="dashboard-card h-100">
                <div class="dashboard-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-store text-indigo"></i>
                        <h3>Storefront Profile Info</h3>
                    </div>
                </div>
                <div class="dashboard-card-body">
                    <div class="form-group mb-4">
                        <label class="form-label text-dark fw-semibold small">Store / Business Name <span class="text-danger">*</span></label>
                        <input type="text" name="store_name" class="form-control @error('store_name') is-invalid @enderror" value="{{ old('store_name', $vendor->store_name ?? '') }}" required>
                        @error('store_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label text-dark fw-semibold small">Store Description / Biography</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4" placeholder="Tell customers about your store, specialties, or history...">{{ old('description', $vendor->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-semibold small">City Location</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city', $vendor->city ?? '') }}" placeholder="e.g. Lagos, Abuja">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-semibold small">State Region</label>
                            <input type="text" name="state" class="form-control" value="{{ old('state', $vendor->state ?? '') }}" placeholder="e.g. Lagos State">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label text-dark fw-semibold small">Store physical Address</label>
                        <textarea name="address" class="form-control" rows="2" placeholder="Warehouse, office, or storefront pick-up address">{{ old('address', $vendor->address ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Store Branding --}}
        <div class="col-lg-4">
            <div class="dashboard-card h-100">
                <div class="dashboard-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-palette text-indigo"></i>
                        <h3>Branding & Media Assets</h3>
                    </div>
                </div>
                <div class="dashboard-card-body">
                    <div class="form-group mb-4">
                        <label class="form-label text-dark fw-semibold small">Store Logo Icon</label>
                        @if($vendor && $vendor->logo)
                            <div class="mb-2 rounded-3 border overflow-hidden bg-light" style="width: 72px; height: 72px;">
                                <img src="{{ Storage::url($vendor->logo) }}" alt="Store Logo" style="width:100%; height:100%; object-fit:cover;">
                            </div>
                        @endif
                        <input type="file" name="logo" accept="image/*" class="form-control">
                        <small class="text-muted small d-block mt-1">Recommended square dimension, Max 2MB.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label text-dark fw-semibold small">Store Banner Image</label>
                        @if($vendor && $vendor->banner)
                            <div class="mb-2 rounded-3 border overflow-hidden bg-light" style="width: 100%; height: 72px;">
                                <img src="{{ Storage::url($vendor->banner) }}" alt="Store Banner" style="width:100%; height:100%; object-fit:cover;">
                            </div>
                        @endif
                        <input type="file" name="banner" accept="image/*" class="form-control">
                        <small class="text-muted small d-block mt-1">Landscape aspect ratio, Max 4MB.</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Delivery Settings --}}
        <div class="col-12">
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-truck text-indigo"></i>
                        <h3>Fulfillment & Delivery Fees</h3>
                    </div>
                </div>
                <div class="dashboard-card-body">
                    <div class="p-3 mb-4 rounded-3 border d-flex gap-3 align-items-start" style="background: rgba(79, 70, 229, 0.02); border-color: rgba(79, 70, 229, 0.15) !important;">
                        <i class="fas fa-info-circle text-primary mt-1"></i>
                        <div class="small text-dark" style="line-height:1.5;">
                            <strong>Delivery Mechanics:</strong> Platform shoppers can choose <strong>Local Pickup</strong> (which resolves at ₦0) or <strong>Standard Shipping</strong>. Set your default shipping fee here. Enter <code>0</code> if you offer free shipping on all orders.
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label text-dark fw-semibold small">Flat-Rate Shipping Fee</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">₦</span>
                                <input type="number" name="delivery_fee" class="form-control" value="{{ old('delivery_fee', $vendor->delivery_fee ?? 0) }}" min="0" step="50" placeholder="0">
                            </div>
                            <small class="text-muted small d-block mt-1">Applied globally to all shipped items.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bank Details --}}
        @php $bank = $vendor->bankDetails()->where('is_primary', true)->first(); @endphp
        <div class="col-12">
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-university text-indigo"></i>
                        <h3>Settlement Account Details</h3>
                    </div>
                    <span class="badge badge-secondary">For Payout Withdrawals</span>
                </div>
                <div class="dashboard-card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label text-dark fw-semibold small">Bank Name</label>
                            <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $bank->bank_name ?? '') }}" placeholder="e.g. GTBank, Zenith Bank">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-dark fw-semibold small">Account Holder Name</label>
                            <input type="text" name="account_name" class="form-control" value="{{ old('account_name', $bank->account_name ?? '') }}" placeholder="Enter exact bank account name">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-dark fw-semibold small">Account Number</label>
                            <input type="text" name="account_number" class="form-control" value="{{ old('account_number', $bank->account_number ?? '') }}" placeholder="10-digit NUBAN number">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SEO & Social --}}
        <div class="col-12">
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-globe text-indigo"></i>
                        <h3>SEO & Social Integrations</h3>
                    </div>
                </div>
                <div class="dashboard-card-body">
                    <h6 class="text-dark fw-bold small mb-3 border-bottom pb-2">SOCIAL CHANNELS</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label text-dark small"><i class="fab fa-facebook text-primary me-1"></i> Facebook Username</label>
                            <input type="text" name="facebook" class="form-control" value="{{ old('facebook', $vendor->facebook) }}" placeholder="https://facebook.com/username">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-dark small"><i class="fab fa-twitter text-info me-1"></i> Twitter Username</label>
                            <input type="text" name="twitter" class="form-control" value="{{ old('twitter', $vendor->twitter) }}" placeholder="https://twitter.com/username">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-dark small"><i class="fab fa-instagram text-danger me-1"></i> Instagram Handle</label>
                            <input type="text" name="instagram" class="form-control" value="{{ old('instagram', $vendor->instagram) }}" placeholder="https://instagram.com/username">
                        </div>
                    </div>
                    
                    <h6 class="text-dark fw-bold small mb-3 border-bottom pb-2">SEARCH OPTIMIZATION</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-dark small">SEO Title Tag</label>
                            <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $vendor->meta_title) }}" placeholder="Page header title shown on Google search">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark small">SEO Meta Description</label>
                            <textarea name="meta_description" class="form-control" rows="1" placeholder="Brief 150-character summary shown in search results">{{ old('meta_description', $vendor->meta_description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KYC Verification --}}
        <div class="col-12">
            @php
                $kycStatus = $vendor->kyc_status ?? 'not_submitted';
                $kycBadge = match($kycStatus) {
                    'verified' => ['bg' => '#ecfdf5', 'color' => '#065f46', 'border' => '#a7f3d0', 'icon' => 'circle-check', 'text' => 'KYC Verified'],
                    'pending' => ['bg' => '#fffbeb', 'color' => '#92400e', 'border' => '#fde68a', 'icon' => 'clock', 'text' => 'Pending Verification Review'],
                    'rejected' => ['bg' => '#fef2f2', 'color' => '#991b1b', 'border' => '#fecaca', 'icon' => 'circle-xmark', 'text' => 'Documents Rejected'],
                    default => ['bg' => '#f8fafc', 'color' => '#475569', 'border' => '#cbd5e1', 'icon' => 'circle-exclamation', 'text' => 'KYC Not Completed'],
                };
            @endphp
            <div class="dashboard-card" style="border: 2px solid {{ $kycBadge['border'] }};">
                <div class="dashboard-card-header" style="background: {{ $kycBadge['bg'] }};">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-shield-halved text-dark"></i>
                        <h3 class="text-dark">Identity Verification (KYC)</h3>
                    </div>
                    <span class="badge" style="background: white; border: 1px solid {{ $kycBadge['border'] }}; color: {{ $kycBadge['color'] }}; font-weight:700;">
                        <i class="fas fa-{{ $kycBadge['icon'] }} me-1"></i> {{ $kycBadge['text'] }}
                    </span>
                </div>
                <div class="dashboard-card-body">
                    @if($kycStatus === 'rejected' && ($vendor->kyc_rejection_reason ?? false))
                    <div class="alert alert-danger d-flex align-items-center gap-2 mb-4 rounded-3">
                        <i class="fas fa-triangle-exclamation"></i>
                        <div><strong>Rejection Reason:</strong> {{ $vendor->kyc_rejection_reason }}</div>
                    </div>
                    @endif

                    <div class="p-3 mb-4 rounded-3 border d-flex gap-3 align-items-start bg-light" style="border-color: var(--border-color) !important;">
                        <i class="fas fa-circle-info text-muted mt-1"></i>
                        <div class="small text-muted" style="line-height:1.5;">
                            Completing identity validation is required to request payout withdrawals. Uploaded government identity proofs and numbers are securely stored and verified manually.
                        </div>
                    </div>

                    <h6 class="text-dark fw-bold small mb-3 border-bottom pb-2">GOVERNMENT IDENTIFICATION</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label text-dark small">ID Document Category</label>
                            <select name="id_type" class="form-select">
                                <option value="">Select ID card type...</option>
                                <option value="national_id" {{ old('id_type', $vendor->id_type ?? '') == 'national_id' ? 'selected' : '' }}>NIN Slip / National Identity Card</option>
                                <option value="drivers_license" {{ old('id_type', $vendor->id_type ?? '') == 'drivers_license' ? 'selected' : '' }}>Driver's License Card</option>
                                <option value="international_passport" {{ old('id_type', $vendor->id_type ?? '') == 'international_passport' ? 'selected' : '' }}>International Passport Booklet</option>
                                <option value="voters_card" {{ old('id_type', $vendor->id_type ?? '') == 'voters_card' ? 'selected' : '' }}>Voter's Card</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-dark small">ID Registration Number</label>
                            <input type="text" name="id_number" class="form-control" value="{{ old('id_number', $vendor->id_number ?? '') }}" placeholder="Enter card registration number">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-dark small">ID Document Upload File</label>
                            <input type="file" name="id_document" accept="image/*,.pdf" class="form-control">
                            @if($vendor->id_document_path ?? false)
                                <small class="text-success fw-bold d-block mt-1"><i class="fas fa-check-circle"></i> File uploaded successfully</small>
                            @else
                                <small class="text-muted small d-block mt-1">JPG, PNG, or PDF file. Max size 5MB.</small>
                            @endif
                        </div>
                    </div>

                    <h6 class="text-dark fw-bold small mb-3 border-bottom pb-2">VERIFICATION CREDENTIALS</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-dark small">NIN (11-digit Number)</label>
                            <input type="text" name="nin" class="form-control" value="{{ old('nin', $vendor->nin ?? '') }}" placeholder="Enter 11-digit NIN" maxlength="11">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark small">BVN (11-digit Number)</label>
                            <input type="text" name="bvn" class="form-control" value="{{ old('bvn', $vendor->bvn ?? '') }}" placeholder="Enter 11-digit BVN" maxlength="11">
                        </div>
                    </div>

                    <h6 class="text-dark fw-bold small mb-3 border-bottom pb-2">BUSINESS CERTIFICATE <span class="text-muted font-normal">(Optional)</span></h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-dark small">CAC Number</label>
                            <input type="text" name="cac_number" class="form-control" value="{{ old('cac_number', $vendor->cac_number ?? '') }}" placeholder="e.g. RC12345 or BN12345">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark small">Upload CAC Certificate Document</label>
                            <input type="file" name="cac_document" accept="image/*,.pdf" class="form-control">
                            @if($vendor->cac_document_path ?? false)
                                <small class="text-success fw-bold d-block mt-1"><i class="fas fa-check-circle"></i> Certificate uploaded</small>
                            @else
                                <small class="text-muted small d-block mt-1">JPG, PNG, or PDF. Max 5MB.</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="col-12 mt-2">
            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-4">
                <i class="fas fa-circle-check me-2"></i> Commit Settings Updates
            </button>
        </div>
    </div>
</form>
@endsection
