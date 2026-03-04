{{-- 
    BuyNiger - Vendor Application Form
    For logged-in customers to apply to become vendors
--}}
@extends('layouts.shop')

@section('title', 'Become a Vendor')

@section('content')
<div class="container py-5">
    <div class="apply-page">
        {{-- Header --}}
        <div class="apply-header">
            <div class="apply-icon"><i class="fas fa-store"></i></div>
            <h1>Become a Vendor</h1>
            <p>Hi <strong>{{ $user->name }}</strong>! Fill in your store details below to start selling on BuyNiger.</p>
        </div>

        {{-- Errors --}}
        @if($errors->any())
        <div class="apply-alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <div>
                <strong>Please fix the following:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        {{-- If already has an application, show status --}}
        @if($existingVendor)
        <div class="apply-status-card status-{{ $existingVendor->status }}">
            @if($existingVendor->status === 'pending')
                <div class="status-icon"><i class="fas fa-clock"></i></div>
                <h2>Application Under Review</h2>
                <p>Your store <strong>"{{ $existingVendor->store_name }}"</strong> is being reviewed by our team. You'll receive an email once approved.</p>
                <span class="status-badge pending">⏳ Pending Review</span>
            @elseif($existingVendor->status === 'rejected')
                <div class="status-icon rejected"><i class="fas fa-times-circle"></i></div>
                <h2>Application Rejected</h2>
                <p>Unfortunately, your store application was rejected.</p>
                @if($existingVendor->rejection_reason)
                    <p><strong>Reason:</strong> {{ $existingVendor->rejection_reason }}</p>
                @endif
                <span class="status-badge rejected">❌ Rejected</span>
                <a href="{{ route('contact') }}" class="btn btn-primary" style="margin-top: 16px;">
                    <i class="fas fa-envelope"></i> Contact Support
                </a>
            @else
                <div class="status-icon"><i class="fas fa-info-circle"></i></div>
                <h2>Application Status: {{ ucfirst($existingVendor->status) }}</h2>
                <p>Your vendor account is currently <strong>{{ $existingVendor->status }}</strong>. Please contact support if you need help.</p>
            @endif
        </div>
        @else
        <form action="{{ route('vendor.apply.submit') }}" method="POST" class="apply-form">
            @csrf

            {{-- Pre-filled info --}}
            <div class="form-section">
                <h3><i class="fas fa-user"></i> Your Info (from your account)</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" value="{{ $user->name }}" disabled class="form-input disabled">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" value="{{ $user->email }}" disabled class="form-input disabled">
                    </div>
                </div>
            </div>

            {{-- Store Details --}}
            <div class="form-section">
                <h3><i class="fas fa-store-alt"></i> Store Details</h3>
                <div class="form-group">
                    <label for="store_name">Store Name <span class="required">*</span></label>
                    <input type="text" name="store_name" id="store_name" value="{{ old('store_name') }}" placeholder="e.g. Amina's Fashion Hub" class="form-input" required>
                    <small>This is what customers will see</small>
                </div>

                <div class="form-group">
                    <label for="store_description">Store Description</label>
                    <textarea name="store_description" id="store_description" rows="3" placeholder="Tell customers what you sell..." class="form-input">{{ old('store_description') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="business_phone">Business Phone</label>
                    <input type="tel" name="business_phone" id="business_phone" value="{{ old('business_phone', $user->phone) }}" placeholder="e.g. 08012345678" class="form-input">
                </div>
            </div>

            {{-- Location --}}
            <div class="form-section">
                <h3><i class="fas fa-map-marker-alt"></i> Business Location</h3>
                <div class="form-group">
                    <label for="business_address">Business Address <span class="required">*</span></label>
                    <input type="text" name="business_address" id="business_address" value="{{ old('business_address') }}" placeholder="e.g. 15 Ibrahim Taiwo Road" class="form-input" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="city">City <span class="required">*</span></label>
                        <input type="text" name="city" id="city" value="{{ old('city') }}" placeholder="e.g. Lagos" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label for="state">State <span class="required">*</span></label>
                        <select name="state" id="state" class="form-input" required>
                            <option value="">Select State</option>
                            @php
                                $states = ['Abia','Adamawa','Akwa Ibom','Anambra','Bauchi','Bayelsa','Benue','Borno','Cross River','Delta','Ebonyi','Edo','Ekiti','Enugu','FCT','Gombe','Imo','Jigawa','Kaduna','Kano','Katsina','Kebbi','Kogi','Kwara','Lagos','Nasarawa','Niger','Ogun','Ondo','Osun','Oyo','Plateau','Rivers','Sokoto','Taraba','Yobe','Zamfara'];
                            @endphp
                            @foreach($states as $state)
                                <option value="{{ $state }}" {{ old('state') == $state ? 'selected' : '' }}>{{ $state }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-lg btn-full">
                    <i class="fas fa-rocket"></i> Submit Application
                </button>
                <p class="form-note">Your application will be reviewed within <strong>2 business days</strong>. You'll receive an email once approved.</p>
            </div>
        </form>
        @endif
    </div>
</div>

<style>
    .apply-page {
        max-width: 640px;
        margin: 0 auto;
    }

    .apply-header {
        text-align: center;
        margin-bottom: 32px;
    }

    .apply-icon {
        width: 64px;
        height: 64px;
        background: linear-gradient(135deg, var(--primary-500, #3b82f6), var(--primary-600, #2563eb));
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        font-size: 28px;
        color: white;
        box-shadow: 0 8px 24px rgba(59,130,246,0.3);
    }

    .apply-header h1 {
        font-size: 24px;
        font-weight: 800;
        color: var(--secondary-900, #0f172a);
        margin: 0 0 8px;
    }

    .apply-header p {
        color: var(--secondary-500, #64748b);
        font-size: 14px;
        margin: 0;
        line-height: 1.5;
    }

    .apply-alert {
        display: flex;
        gap: 12px;
        padding: 14px 16px;
        border-radius: 14px;
        margin-bottom: 24px;
        font-size: 13px;
    }
    .alert-error {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #b91c1c;
    }
    .alert-error ul { margin: 4px 0 0 16px; }

    /* Status Card (pending/rejected) */
    .apply-status-card {
        background: white;
        border: 1px solid var(--secondary-100, #f1f5f9);
        border-radius: 20px;
        padding: 40px 24px;
        text-align: center;
    }
    .status-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #fef3c7;
        color: #f59e0b;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        font-size: 24px;
    }
    .status-icon.rejected {
        background: #fef2f2;
        color: #ef4444;
    }
    .apply-status-card h2 {
        font-size: 20px;
        font-weight: 800;
        color: var(--secondary-900, #0f172a);
        margin: 0 0 8px;
    }
    .apply-status-card p {
        font-size: 14px;
        color: var(--secondary-500, #64748b);
        margin: 0 0 16px;
        line-height: 1.6;
    }
    .status-badge {
        display: inline-block;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
    }
    .status-badge.pending {
        background: #fef3c7;
        color: #92400e;
    }
    .status-badge.rejected {
        background: #fef2f2;
        color: #b91c1c;
    }

    .apply-form {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .form-section {
        background: white;
        border: 1px solid var(--secondary-100, #f1f5f9);
        border-radius: 18px;
        padding: 24px;
    }

    .form-section h3 {
        font-size: 15px;
        font-weight: 700;
        color: var(--secondary-900, #0f172a);
        margin: 0 0 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-section h3 i {
        color: var(--primary-500, #3b82f6);
        font-size: 14px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    .form-group {
        margin-bottom: 14px;
    }

    .form-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: var(--secondary-700, #334155);
        margin-bottom: 6px;
    }

    .required { color: #ef4444; }

    .form-input {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid var(--secondary-200, #e2e8f0);
        border-radius: 12px;
        font-size: 14px;
        color: var(--secondary-900, #0f172a);
        background: white;
        transition: all 0.2s;
        outline: none;
    }

    .form-input:focus {
        border-color: var(--primary-400, #60a5fa);
        box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
    }

    .form-input.disabled {
        background: var(--secondary-50, #f8fafc);
        color: var(--secondary-500, #64748b);
        cursor: not-allowed;
    }

    textarea.form-input {
        resize: vertical;
        min-height: 80px;
    }

    select.form-input {
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
        padding-right: 36px;
    }

    .form-group small {
        display: block;
        margin-top: 4px;
        font-size: 11px;
        color: var(--secondary-400, #94a3b8);
    }

    .form-actions {
        text-align: center;
    }

    .btn-full {
        width: 100%;
    }

    .form-note {
        margin-top: 12px;
        font-size: 12px;
        color: var(--secondary-400, #94a3b8);
    }

    @media (max-width: 480px) {
        .form-row { grid-template-columns: 1fr; }
        .form-section { padding: 18px 16px; border-radius: 14px; }
        .apply-header h1 { font-size: 20px; }
    }
</style>
@endsection
