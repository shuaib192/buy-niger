{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    View: Forgot Password Page
--}}
@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('promo_title', 'Reset Your Password')
@section('promo_text', 'No worries! Enter your email and we\'ll send you a secure link to reset your password.')

@section('content')
    <div class="auth-title">
        <h2>Forgot Password?</h2>
        <p>Enter your email to receive a password reset link</p>
    </div>

    <form method="POST" action="{{ route('password.email') }}" id="forgotForm">
        @csrf

        <!-- Email -->
        <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                class="form-input @error('email') error @enderror" 
                placeholder="Enter your registered email"
                value="{{ old('email') }}"
                required
                autofocus
            >
            @error('email')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary btn-lg btn-full" id="submitBtn">
            <span>Send Reset Link</span>
            <i class="fas fa-paper-plane"></i>
        </button>
    </form>

    <div class="auth-footer">
        <p>Remember your password? <a href="{{ route('login') }}">Back to Sign In</a></p>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('forgotForm').addEventListener('submit', function(e) {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<div class="spinner"></div><span>Sending...</span>';
    });
</script>
@endpush
