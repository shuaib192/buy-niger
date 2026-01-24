{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    View: Login Page
--}}
@extends('layouts.auth')

@section('title', 'Login')

@section('promo_title', 'Welcome Back!')
@section('promo_text', 'Sign in to access your account, manage your orders, and discover amazing deals powered by AI recommendations.')

@section('content')
    <div class="auth-title">
        <h2>Sign In</h2>
        <p>Enter your credentials to access your account</p>
    </div>

    <form method="POST" action="{{ route('login') }}" id="loginForm">
        @csrf

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
                autofocus
            >
            @error('email')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-group">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-sm);">
                <label class="form-label" for="password" style="margin-bottom: 0;">Password</label>
                <a href="{{ route('password.request') }}" style="font-size: 0.8125rem;">Forgot Password?</a>
            </div>
            <div class="password-wrapper">
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-input @error('password') error @enderror" 
                    placeholder="Enter your password"
                    required
                >
                <button type="button" class="password-toggle" aria-label="Toggle password visibility">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            @error('password')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="form-group">
            <label class="form-checkbox">
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                <span>Remember me for 30 days</span>
            </label>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary btn-lg btn-full" id="submitBtn">
            <span>Sign In</span>
            <i class="fas fa-arrow-right"></i>
        </button>
    </form>

    <div class="auth-divider">or</div>

    <!-- Social Login (Optional) -->
    <div style="display: flex; gap: var(--spacing-md);">
        <button type="button" class="btn btn-secondary btn-full" disabled style="opacity: 0.5;">
            <i class="fab fa-google"></i>
            <span>Google</span>
        </button>
        <button type="button" class="btn btn-secondary btn-full" disabled style="opacity: 0.5;">
            <i class="fab fa-facebook-f"></i>
            <span>Facebook</span>
        </button>
    </div>

    <div class="auth-footer">
        <p>Don't have an account? <a href="{{ route('register') }}">Create Account</a></p>
        <p style="margin-top: 0.5rem;">Want to sell? <a href="{{ route('vendor.register') }}">Become a Vendor</a></p>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<div class="spinner"></div><span>Signing in...</span>';
    });
</script>
@endpush
