{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    View: Vendor Registration Page
--}}
@extends('layouts.auth')

@section('title', 'Become a Vendor')

@section('promo_title', 'Sell on BuyNiger')
@section('promo_text', 'Join our growing community of successful vendors. Reach thousands of customers, manage your inventory with AI assistance, and grow your business.')

@section('content')
    <div class="auth-title">
        <h2>Become a Vendor</h2>
        <p>Start selling on Nigeria's AI-powered marketplace</p>
    </div>

    <form method="POST" action="{{ route('vendor.register.submit') }}" id="vendorForm">
        @csrf

        <!-- Personal Info Section -->
        <div style="margin-bottom: var(--spacing-md);">
            <h4 style="font-size: 0.875rem; color: var(--primary-600); text-transform: uppercase; letter-spacing: 0.05em;">Personal Information</h4>
        </div>

        <!-- Full Name -->
        <div class="form-group">
            <label class="form-label" for="name">Full Name</label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                class="form-input @error('name') error @enderror" 
                placeholder="Your full name"
                value="{{ old('name') }}"
                required
            >
            @error('name')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email & Phone Row -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--spacing-md);">
            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-input @error('email') error @enderror" 
                    placeholder="your@email.com"
                    value="{{ old('email') }}"
                    required
                >
                @error('email')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="phone">Phone</label>
                <input 
                    type="tel" 
                    id="phone" 
                    name="phone" 
                    class="form-input @error('phone') error @enderror" 
                    placeholder="08012345678"
                    value="{{ old('phone') }}"
                    required
                >
                @error('phone')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Store Info Section -->
        <div style="margin: var(--spacing-lg) 0 var(--spacing-md);">
            <h4 style="font-size: 0.875rem; color: var(--primary-600); text-transform: uppercase; letter-spacing: 0.05em;">Store Information</h4>
        </div>

        <!-- Store Name -->
        <div class="form-group">
            <label class="form-label" for="store_name">Store Name</label>
            <input 
                type="text" 
                id="store_name" 
                name="store_name" 
                class="form-input @error('store_name') error @enderror" 
                placeholder="Your store name"
                value="{{ old('store_name') }}"
                required
            >
            @error('store_name')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Store Description -->
        <div class="form-group">
            <label class="form-label" for="store_description">Store Description (Optional)</label>
            <textarea 
                id="store_description" 
                name="store_description" 
                class="form-input form-textarea" 
                placeholder="Tell customers about your store..."
                rows="3"
            >{{ old('store_description') }}</textarea>
        </div>

        <!-- Business Address -->
        <div class="form-group">
            <label class="form-label" for="business_address">Business Address</label>
            <input 
                type="text" 
                id="business_address" 
                name="business_address" 
                class="form-input @error('business_address') error @enderror" 
                placeholder="Street address"
                value="{{ old('business_address') }}"
                required
            >
            @error('business_address')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- City & State Row -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--spacing-md);">
            <div class="form-group">
                <label class="form-label" for="city">City</label>
                <input 
                    type="text" 
                    id="city" 
                    name="city" 
                    class="form-input @error('city') error @enderror" 
                    placeholder="e.g. Lagos"
                    value="{{ old('city') }}"
                    required
                >
                @error('city')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="state">State</label>
                <input 
                    type="text" 
                    id="state" 
                    name="state" 
                    class="form-input @error('state') error @enderror" 
                    placeholder="e.g. Lagos"
                    value="{{ old('state') }}"
                    required
                >
                @error('state')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Security Section -->
        <div style="margin: var(--spacing-lg) 0 var(--spacing-md);">
            <h4 style="font-size: 0.875rem; color: var(--primary-600); text-transform: uppercase; letter-spacing: 0.05em;">Account Security</h4>
        </div>

        <!-- Password -->
        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <div class="password-wrapper">
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-input @error('password') error @enderror" 
                    placeholder="Min. 8 characters"
                    required
                    minlength="8"
                >
                <button type="button" class="password-toggle" aria-label="Toggle password visibility">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            @error('password')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
            <label class="form-label" for="password_confirmation">Confirm Password</label>
            <div class="password-wrapper">
                <input 
                    type="password" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    class="form-input" 
                    placeholder="Confirm password"
                    required
                >
                <button type="button" class="password-toggle" aria-label="Toggle password visibility">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
        </div>

        <!-- Terms -->
        <div class="form-group">
            <label class="form-checkbox">
                <input type="checkbox" name="terms" required>
                <span>I agree to the <a href="#">Vendor Terms</a> and <a href="#">Commission Policy</a></span>
            </label>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary btn-lg btn-full" id="submitBtn">
            <span>Submit Application</span>
            <i class="fas fa-store"></i>
        </button>

        <p style="text-align: center; margin-top: var(--spacing-md); font-size: 0.8125rem; color: var(--secondary-500);">
            <i class="fas fa-info-circle"></i> 
            Your application will be reviewed within 24-48 hours
        </p>
    </form>

    <div class="auth-footer">
        <p>Already have an account? <a href="{{ route('login') }}">Sign In</a></p>
        <p style="margin-top: 0.5rem;">Just want to shop? <a href="{{ route('register') }}">Create Customer Account</a></p>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('vendorForm').addEventListener('submit', function(e) {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<div class="spinner"></div><span>Submitting application...</span>';
    });
</script>
@endpush
