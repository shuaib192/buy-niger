{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    View: Vendor Profile Settings
--}}
@extends('layouts.app')

@section('title', 'My Profile')
@section('page_title', 'Personal Profile')

@section('sidebar')
    @include('vendor.partials.sidebar')
@endsection

@section('content')
@if(session('success'))
    <div class="alert alert-success mb-4">{{ session('success') }}</div>
@endif

<div class="row">
    <!-- Profile Info -->
    <div class="col-lg-8">
        <div class="dashboard-card border-0 shadow-sm mb-4">
            <div class="dashboard-card-header bg-white border-0 py-4">
                <h3 class="h5 font-bold mb-0">Account Information</h3>
            </div>
            <div class="dashboard-card-body">
                <form action="{{ route('vendor.profile.update') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-bold text-xs uppercase text-secondary-500">Full Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <span class="text-danger text-xs mt-1 d-block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-bold text-xs uppercase text-secondary-500">Email Address</label>
                            <input type="email" class="form-control bg-light" value="{{ $user->email }}" disabled>
                            <small class="text-muted text-xs mt-1 d-block">Email cannot be changed</small>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label font-bold text-xs uppercase text-secondary-500">Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="e.g. 08012345678">
                    </div>
                    <button type="submit" class="btn btn-primary px-5 shadow-sm">
                        <i class="fas fa-save mr-2"></i> Update Profile
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Password -->
    <div class="col-lg-4">
        <div class="dashboard-card border-0 shadow-sm mb-4">
            <div class="dashboard-card-header bg-white border-0 py-4">
                <h3 class="h5 font-bold mb-0">Security</h3>
            </div>
            <div class="dashboard-card-body">
                <p class="text-secondary-500 text-sm mb-3">Keep your account secure by using a strong password.</p>
                <a href="{{ route('customer.profile') }}#password" class="btn btn-outline-secondary btn-block">
                    <i class="fas fa-lock mr-2"></i> Change Password
                </a>
                <small class="text-secondary-400 d-block mt-3 text-xs">Note: Password changes are handled through your main account security settings.</small>
            </div>
        </div>
    </div>
</div>
@endsection
