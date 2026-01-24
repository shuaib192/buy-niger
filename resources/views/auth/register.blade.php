{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    View: Customer Registration Page
--}}
@extends('layouts.auth')

@section('title', 'Create Account')

@section('promo_title', 'Start Shopping Today')
@section('promo_text', 'Join thousands of happy customers. Create your free account and enjoy personalized recommendations, order tracking, and exclusive deals.')

@section('content')
    <div class="auth-title">
        <h2>Create Account</h2>
        <p>Fill in your details to get started</p>
    </div>

    <form method="POST" action="{{ route('register') }}" id="registerForm">
        @csrf

        <!-- Full Name -->
        <div class="form-group">
            <label class="form-label" for="name">Full Name</label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                class="form-input @error('name') error @enderror" 
                placeholder="Enter your full name"
                value="{{ old('name') }}"
                required
                autofocus
            >
            @error('name')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email -->
        <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                class="form-input @error('email') error @enderror" 
                placeholder="Enter your email"
                value="{{ old('email') }}"
                required
            >
            @error('email')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Phone -->
        <div class="form-group">
            <label class="form-label" for="phone">Phone Number</label>
            <input 
                type="tel" 
                id="phone" 
                name="phone" 
                class="form-input @error('phone') error @enderror" 
                placeholder="e.g. 08012345678"
                value="{{ old('phone') }}"
                required
            >
            @error('phone')
                <div class="form-error">{{ $message }}</div>
            @enderror
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
                    placeholder="Create a strong password (min. 8 characters)"
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
                    placeholder="Confirm your password"
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
                <span>I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></span>
            </label>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary btn-lg btn-full" id="submitBtn">
            <span>Create Account</span>
            <i class="fas fa-arrow-right"></i>
        </button>
    </form>

    <div class="auth-footer">
        <p>Already have an account? <a href="{{ route('login') }}">Sign In</a></p>
        <p style="margin-top: 0.5rem;">Want to sell? <a href="{{ route('vendor.register') }}">Become a Vendor</a></p>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<div class="spinner"></div><span>Creating account...</span>';
    });
</script>
@endpush
