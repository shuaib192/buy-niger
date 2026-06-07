{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    View: Vendor Store Settings
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
                <div class="settings-card">
                    <div class="settings-card-header">
                        <i class="fas fa-store"></i>
                        <h3>Store Information</h3>
                    </div>
                    <div class="settings-card-body">
                        <div class="form-group mb-4">
                            <label class="form-label">Store Name <span class="text-danger">*</span></label>
                            <input type="text" name="store_name" class="form-control @error('store_name') is-invalid @enderror" value="{{ old('store_name', $vendor->store_name ?? '') }}" required>
                            @error('store_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label">Store Description</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Tell customers about your store...">{{ old('description', $vendor->description ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">City</label>
                                <input type="text" name="city" class="form-control" value="{{ old('city', $vendor->city ?? '') }}" placeholder="e.g. Niamey">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">State</label>
                                <input type="text" name="state" class="form-control" value="{{ old('state', $vendor->state ?? '') }}" placeholder="e.g. Niamey">
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2" placeholder="Your store or warehouse address">{{ old('address', $vendor->address ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Store Branding --}}
            <div class="col-lg-4">
                <div class="settings-card">
                    <div class="settings-card-header">
                        <i class="fas fa-palette"></i>
                        <h3>Store Branding</h3>
                    </div>
                    <div class="settings-card-body">
                        <div class="form-group mb-4">
                            <label class="form-label">Store Logo</label>
                            @if($vendor && $vendor->logo)
                                <img src="{{ Storage::url($vendor->logo) }}" alt="Store Logo" class="current-logo mb-2">
                            @endif
                            <input type="file" name="logo" accept="image/*" class="form-control">
                            <small class="form-text text-muted">Square image, max 2MB</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Store Banner</label>
                            @if($vendor && $vendor->banner)
                                <img src="{{ Storage::url($vendor->banner) }}" alt="Store Banner" class="current-banner mb-2">
                            @endif
                            <input type="file" name="banner" accept="image/*" class="form-control">
                            <small class="form-text text-muted">Wide image, max 4MB</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Delivery Settings --}}
            <div class="col-12">
                <div class="settings-card">
                    <div class="settings-card-header">
                        <i class="fas fa-truck"></i>
                        <h3>Delivery Settings</h3>
                    </div>
                    <div class="settings-card-body">
                        <div class="delivery-info-banner">
                            <i class="fas fa-info-circle"></i>
                            <div>
                                <strong>How delivery works:</strong> Customers can either <strong>pick up</strong> from your location (free) or choose <strong>Vendor Shipping</strong> where you waybill/ship the order. Set your shipping fee below — set ₦0 if you want to offer free shipping.
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-4">
                                <label class="form-label">Shipping Fee (₦)</label>
                                <div class="input-group">
                                    <span class="input-group-text">₦</span>
                                    <input type="number" name="delivery_fee" class="form-control" value="{{ old('delivery_fee', $vendor->delivery_fee ?? 0) }}" min="0" step="50" placeholder="0">
                                </div>
                                <small class="form-text text-muted">Applied when customer chooses Vendor Shipping</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bank Details --}}
            @php $bank = $vendor->bankDetails()->where('is_primary', true)->first(); @endphp
            <div class="col-12">
                <div class="settings-card">
                    <div class="settings-card-header">
                        <i class="fas fa-university"></i>
                        <h3>Bank Details</h3>
                        <span class="badge-info-text">For payouts</span>
                    </div>
                    <div class="settings-card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Bank Name</label>
                                <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $bank->bank_name ?? '') }}" placeholder="e.g. GTBank, Zenith">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Account Name</label>
                                <input type="text" name="account_name" class="form-control" value="{{ old('account_name', $bank->account_name ?? '') }}" placeholder="Account Holder Name">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Account Number</label>
                                <input type="text" name="account_number" class="form-control" value="{{ old('account_number', $bank->account_number ?? '') }}" placeholder="10-digit Account Number">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SEO & Social --}}
            <div class="col-12">
                <div class="settings-card">
                    <div class="settings-card-header">
                        <i class="fas fa-globe"></i>
                        <h3>SEO & Social Media</h3>
                    </div>
                    <div class="settings-card-body">
                        <h6 class="mb-3" style="color:#64748b;font-weight:600;">Social Profiles</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label"><i class="fab fa-facebook text-primary me-1"></i> Facebook</label>
                                <input type="text" name="facebook" class="form-control" value="{{ old('facebook', $vendor->facebook) }}" placeholder="https://facebook.com/...">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"><i class="fab fa-twitter text-info me-1"></i> Twitter</label>
                                <input type="text" name="twitter" class="form-control" value="{{ old('twitter', $vendor->twitter) }}" placeholder="https://twitter.com/...">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"><i class="fab fa-instagram text-danger me-1"></i> Instagram</label>
                                <input type="text" name="instagram" class="form-control" value="{{ old('instagram', $vendor->instagram) }}" placeholder="https://instagram.com/...">
                            </div>
                        </div>
                        
                        <h6 class="mb-3" style="color:#64748b;font-weight:600;">Search Engine Optimization</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Meta Title</label>
                                <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $vendor->meta_title) }}" placeholder="Page title for search engines">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Meta Description</label>
                                <textarea name="meta_description" class="form-control" rows="1" placeholder="Brief description for search results">{{ old('meta_description', $vendor->meta_description) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KYC Verification --}}
            <div class="col-12">
                <div class="settings-card" style="border: 2px solid {{ ($vendor->kyc_status ?? 'not_submitted') === 'verified' ? '#10b981' : (($vendor->kyc_status ?? 'not_submitted') === 'rejected' ? '#ef4444' : '#e2e8f0') }};">
                    <div class="settings-card-header">
                        <i class="fas fa-shield-alt"></i>
                        <h3>KYC — Identity Verification</h3>
                        @php
                            $kycStatus = $vendor->kyc_status ?? 'not_submitted';
                            $kycBadge = match($kycStatus) {
                                'verified' => ['bg' => '#dcfce7', 'color' => '#166534', 'icon' => 'check-circle', 'text' => 'Verified'],
                                'pending' => ['bg' => '#fef9c3', 'color' => '#854d0e', 'icon' => 'clock', 'text' => 'Under Review'],
                                'rejected' => ['bg' => '#fee2e2', 'color' => '#991b1b', 'icon' => 'times-circle', 'text' => 'Rejected'],
                                default => ['bg' => '#f1f5f9', 'color' => '#64748b', 'icon' => 'exclamation-circle', 'text' => 'Not Submitted'],
                            };
                        @endphp
                        <span style="display:inline-flex;align-items:center;gap:6px;padding:4px 12px;background:{{ $kycBadge['bg'] }};color:{{ $kycBadge['color'] }};border-radius:20px;font-size:12px;font-weight:700;">
                            <i class="fas fa-{{ $kycBadge['icon'] }}"></i> {{ $kycBadge['text'] }}
                        </span>
                    </div>
                    <div class="settings-card-body">
                        @if($kycStatus === 'rejected' && ($vendor->kyc_rejection_reason ?? false))
                        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:12px 16px;margin-bottom:20px;color:#991b1b;font-size:13px;">
                            <strong><i class="fas fa-exclamation-triangle"></i> Rejection Reason:</strong> {{ $vendor->kyc_rejection_reason }}
                        </div>
                        @endif

                        <div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:10px;padding:12px 16px;margin-bottom:24px;font-size:13px;color:#0c4a6e;">
                            <i class="fas fa-info-circle"></i> Complete your KYC to unlock higher withdrawal limits and build buyer trust. All documents are securely stored and never shared publicly.
                        </div>

                        <h6 class="mb-3" style="color:#64748b;font-weight:600;">Government-Issued ID</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">ID Type</label>
                                <select name="id_type" class="form-control">
                                    <option value="">Select ID type...</option>
                                    <option value="national_id" {{ old('id_type', $vendor->id_type ?? '') == 'national_id' ? 'selected' : '' }}>NIN Slip / National ID Card</option>
                                    <option value="drivers_license" {{ old('id_type', $vendor->id_type ?? '') == 'drivers_license' ? 'selected' : '' }}>Driver's License</option>
                                    <option value="international_passport" {{ old('id_type', $vendor->id_type ?? '') == 'international_passport' ? 'selected' : '' }}>International Passport</option>
                                    <option value="voters_card" {{ old('id_type', $vendor->id_type ?? '') == 'voters_card' ? 'selected' : '' }}>Voter's Card</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">ID Number</label>
                                <input type="text" name="id_number" class="form-control" value="{{ old('id_number', $vendor->id_number ?? '') }}" placeholder="e.g. A12345678">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Upload ID Document</label>
                                <input type="file" name="id_document" accept="image/*,.pdf" class="form-control">
                                @if($vendor->id_document_path ?? false)
                                    <small class="text-success"><i class="fas fa-check-circle"></i> Document uploaded</small>
                                @else
                                    <small class="text-muted">JPG, PNG, or PDF. Max 5MB</small>
                                @endif
                            </div>
                        </div>

                        <h6 class="mb-3" style="color:#64748b;font-weight:600;">Verification Numbers</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">NIN (National Identification Number)</label>
                                <input type="text" name="nin" class="form-control" value="{{ old('nin', $vendor->nin ?? '') }}" placeholder="11-digit NIN" maxlength="11">
                                <small class="text-muted">Your 11-digit National Identity Number</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">BVN (Bank Verification Number)</label>
                                <input type="text" name="bvn" class="form-control" value="{{ old('bvn', $vendor->bvn ?? '') }}" placeholder="11-digit BVN" maxlength="11">
                                <small class="text-muted">Your 11-digit Bank Verification Number</small>
                            </div>
                        </div>

                        <h6 class="mb-3" style="color:#64748b;font-weight:600;">Business Registration <span style="color:#94a3b8;font-weight:400;">(Optional)</span></h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">CAC Registration Number</label>
                                <input type="text" name="cac_number" class="form-control" value="{{ old('cac_number', $vendor->cac_number ?? '') }}" placeholder="RC/BN Number">
                                <small class="text-muted">Corporate Affairs Commission number, if registered</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Upload CAC Certificate</label>
                                <input type="file" name="cac_document" accept="image/*,.pdf" class="form-control">
                                @if($vendor->cac_document_path ?? false)
                                    <small class="text-success"><i class="fas fa-check-circle"></i> Certificate uploaded</small>
                                @else
                                    <small class="text-muted">JPG, PNG, or PDF. Max 5MB</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-lg px-5">
                    <i class="fas fa-save me-2"></i> Save Settings
                </button>
            </div>
        </div>
    </form>

    <style>
        .settings-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }

        .settings-card-header {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
            background: #f8fafc;
        }

        .settings-card-header i {
            color: #0066FF;
            font-size: 16px;
        }

        .settings-card-header h3 {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
        }

        .settings-card-body {
            padding: 20px;
        }

        .badge-info-text {
            margin-left: auto;
            font-size: 12px;
            color: #94a3b8;
            font-weight: 500;
        }

        .delivery-info-banner {
            display: flex;
            gap: 12px;
            padding: 14px 16px;
            background: #eff6ff;
            border-radius: 8px;
            font-size: 13px;
            color: #1e40af;
            line-height: 1.5;
        }

        .delivery-info-banner > i {
            color: #3b82f6;
            margin-top: 2px;
            flex-shrink: 0;
        }

        .current-logo {
            width: 72px;
            height: 72px;
            object-fit: cover;
            border-radius: 10px;
            display: block;
            border: 2px solid #e2e8f0;
        }

        .current-banner {
            width: 100%;
            height: 72px;
            object-fit: cover;
            border-radius: 10px;
            display: block;
            border: 2px solid #e2e8f0;
        }

        .form-label {
            font-weight: 600;
            font-size: 13px;
            color: #374151;
            margin-bottom: 6px;
        }

        .form-control, .input-group-text {
            font-size: 14px;
            border-radius: 8px;
        }

        .input-group .form-control {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

        .input-group-text {
            background: #f8fafc;
            border-right: none;
            color: #64748b;
            font-weight: 600;
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        .btn-primary.btn-lg {
            border-radius: 10px;
            font-weight: 700;
            font-size: 15px;
            padding: 12px 32px;
        }
    </style>
@endsection
