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
    <form action="{{ route('vendor.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <!-- Store Info -->
            <div class="col-lg-8">
                <div class="dashboard-card border-0 shadow-sm mb-4">
                    <div class="dashboard-card-header bg-white border-0 py-4">
                        <h3 class="h5 font-bold mb-0">Store Information</h3>
                    </div>
                    <div class="dashboard-card-body">
                        <div class="form-group mb-4">
                            <label class="form-label">Store Name *</label>
                            <input type="text" name="store_name" class="form-control @error('store_name') is-invalid @enderror" value="{{ old('store_name', $vendor->store_name ?? '') }}" required>
                            @error('store_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label">Store Description</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description', $vendor->description ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">City</label>
                                <input type="text" name="city" class="form-control" value="{{ old('city', $vendor->city ?? '') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">State</label>
                                <input type="text" name="state" class="form-control" value="{{ old('state', $vendor->state ?? '') }}">
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2">{{ old('address', $vendor->address ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Store Branding -->
            <div class="col-lg-4">
                <div class="dashboard-card border-0 shadow-sm mb-4">
                    <div class="dashboard-card-header bg-white border-0 py-4">
                        <h3 class="h5 font-bold mb-0">Store Branding</h3>
                </div>
                <div class="dashboard-card-body">
                    <div class="form-group mb-4">
                        <label class="form-label">Store Logo</label>
                        <div class="logo-upload">
                            @if($vendor && $vendor->logo)
                                <img src="{{ Storage::url($vendor->logo) }}" alt="Store Logo" class="current-logo">
                            @endif
                            <input type="file" name="logo" id="logo" accept="image/*" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Store Banner</label>
                        <div class="banner-upload">
                            @if($vendor && $vendor->banner)
                                <img src="{{ Storage::url($vendor->banner) }}" alt="Store Banner" class="current-banner">
                            @endif
                            <input type="file" name="banner" id="banner" accept="image/*" class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bank Details -->
            @php $bank = $vendor->bankDetails()->where('is_primary', true)->first(); @endphp
            <div class="dashboard-card col-12 mt-4">
                <div class="dashboard-card-header">
                    <h3>Bank Details (for Payouts)</h3>
                </div>
                <div class="dashboard-card-body">
                    <div class="settings-row">
                        <div class="form-group">
                            <label class="form-label">Bank Name</label>
                            <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $bank->bank_name ?? '') }}" placeholder="e.g. GTBank, Zenith">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Account Name</label>
                            <input type="text" name="account_name" class="form-control" value="{{ old('account_name', $bank->account_name ?? '') }}" placeholder="Account Holder Name">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Account Number</label>
                            <input type="text" name="account_number" class="form-control" value="{{ old('account_number', $bank->account_number ?? '') }}" placeholder="10-digit Account Number">
                        </div>
                    </div>
                </div>
            </div>

                </div>
            </div>

            <!-- Social Links & SEO -->
            <div class="dashboard-card col-12 mt-4">
                <div class="dashboard-card-header">
                    <h3>SEO & Social Media</h3>
                </div>
                <div class="dashboard-card-body">
                    <h5 class="mb-3">Social Profiles</h5>
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Facebook</label>
                            <input type="text" name="facebook" class="form-control" value="{{ old('facebook', $vendor->facebook) }}" placeholder="https://facebook.com/...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Twitter</label>
                            <input type="text" name="twitter" class="form-control" value="{{ old('twitter', $vendor->twitter) }}" placeholder="https://twitter.com/...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Instagram</label>
                            <input type="text" name="instagram" class="form-control" value="{{ old('instagram', $vendor->instagram) }}" placeholder="https://instagram.com/...">
                        </div>
                    </div>
                    
                    <h5 class="mb-3">Search Engine Optimization</h5>
                    <div class="form-group mb-3">
                         <label class="form-label">Meta Title</label>
                         <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $vendor->meta_title) }}" placeholder="Page Title">
                    </div>
                    <div class="form-group">
                         <label class="form-label">Meta Description</label>
                         <textarea name="meta_description" class="form-control" rows="2" placeholder="Description for search results">{{ old('meta_description', $vendor->meta_description) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Settings
                </button>
            </div>
        </div>
    </form>

    <style>
        .settings-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--spacing-md);
        }

        .current-logo {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: var(--radius-md);
            margin-bottom: var(--spacing-sm);
            display: block;
        }

        .current-banner {
            width: 100%;
            height: 80px;
            object-fit: cover;
            border-radius: var(--radius-md);
            margin-bottom: var(--spacing-sm);
            display: block;
        }

        @media (max-width: 768px) {
            .settings-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection
