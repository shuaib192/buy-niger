{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    View: Customer Profile
--}}
@extends('layouts.app')

@section('title', 'My Profile')
@section('page_title', 'Profile Settings')

@section('sidebar')
    @include('customer.partials.sidebar')
@endsection

@section('content')
@if(session('success'))
    <div class="alert alert-success mb-4">{{ session('success') }}</div>
@endif

<div class="row">
    <!-- Profile Info -->
    <div class="col-lg-8 mb-4">
        <div class="dashboard-card h-100">
            <div class="dashboard-card-header">
                <h3>Personal Information</h3>
            </div>
            <div class="dashboard-card-body">
                <form action="{{ route('customer.profile.update') }}" method="POST">
                    @csrf
                    <div class="form-row">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" class="form-control" value="{{ $user->email }}" disabled>
                            <small class="help-text">Email cannot be changed</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="e.g. 08012345678">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Change Password -->
    <div class="col-lg-4 mb-4">
        <div class="dashboard-card h-100">
            <div class="dashboard-card-header">
                <h3>Change Password</h3>
            </div>
            <div class="dashboard-card-body">
                <form action="{{ route('customer.password.update') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" name="current_password" class="form-control" required>
                        @error('current_password')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="password" class="form-control" required>
                        @error('password')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-secondary btn-full">
                        <i class="fas fa-lock"></i> Update Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    .form-group {
        margin-bottom: 16px;
    }
    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        font-size: 14px;
    }
    .form-control {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid var(--secondary-200);
        border-radius: 10px;
        font-size: 14px;
    }
    .form-control:disabled {
        background: var(--secondary-50);
        cursor: not-allowed;
    }
    .help-text {
        font-size: 12px;
        color: var(--secondary-400);
        margin-top: 4px;
    }
    .error-text {
        font-size: 12px;
        color: var(--danger);
        margin-top: 4px;
    }
    .alert-success {
        background: #d1fae5;
        color: #047857;
        padding: 12px 16px;
        border-radius: 10px;
    }
    .mb-4 { margin-bottom: 1.5rem; }
    @media (max-width: 768px) {
        .form-row { grid-template-columns: 1fr; }
    }
</style>
@endsection
