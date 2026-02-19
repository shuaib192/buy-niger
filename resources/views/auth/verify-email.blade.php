{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    View: Email Verification Page
--}}
@extends('layouts.auth')

@section('title', 'Verify Your Email')

@section('promo_title', 'Almost There!')
@section('promo_text', 'We sent a 6-digit verification code to your email. Enter it below to confirm your account and start shopping.')

@section('content')
    <div class="auth-title">
        <div style="width:64px;height:64px;background:linear-gradient(135deg,#22c55e,#16a34a);border-radius:50%;margin:0 auto 16px;display:flex;align-items:center;justify-content:center;">
            <i class="fas fa-envelope-open-text" style="color:white;font-size:24px;"></i>
        </div>
        <h2>Verify Your Email</h2>
        <p>We sent a 6-digit code to <strong>{{ $email }}</strong></p>
    </div>

    @if(session('success'))
        <div class="alert-box success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('info'))
        <div class="alert-box info">
            <i class="fas fa-info-circle"></i>
            {{ session('info') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert-box error">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('verification.verify') }}" id="verifyForm">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">

        <!-- OTP Input -->
        <div class="form-group">
            <label class="form-label" for="otp">Verification Code</label>
            <div class="otp-input-wrapper">
                <input 
                    type="text" 
                    id="otp" 
                    name="otp" 
                    class="form-input otp-input @error('otp') error @enderror" 
                    placeholder="0 0 0 0 0 0"
                    maxlength="6"
                    pattern="[0-9]{6}"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    required
                    autofocus
                    style="text-align:center;font-size:16px;font-weight:600;letter-spacing:6px;padding:12px;"
                >
            </div>
            @error('otp')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Submit -->
        <button type="submit" class="btn btn-primary btn-lg btn-full" id="submitBtn">
            <span>Verify Email</span>
            <i class="fas fa-check-circle"></i>
        </button>
    </form>

    <!-- Resend -->
    <div style="text-align:center;margin-top:24px;">
        <p style="color:#64748b;font-size:14px;margin-bottom:12px;">Didn't receive the code? Check your spam folder or</p>
        <form method="POST" action="{{ route('verification.resend') }}" style="display:inline;">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <button type="submit" class="btn btn-outline btn-sm" id="resendBtn">
                <i class="fas fa-redo"></i> Resend Code
            </button>
        </form>
    </div>

    <div class="auth-footer">
        <p>Wrong email? <a href="{{ route('register') }}">Go back</a></p>
    </div>

    <style>
        .alert-box {
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: 500;
        }
        .alert-box.success {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        .alert-box.info {
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }
        .alert-box.error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        .otp-input:focus {
            border-color: #0066FF;
            box-shadow: 0 0 0 4px rgba(0, 102, 255, 0.1);
        }
        .btn-outline {
            background: transparent;
            border: 2px solid #e2e8f0;
            color: #475569;
            cursor: pointer;
            padding: 8px 20px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-outline:hover {
            border-color: #0066FF;
            color: #0066FF;
            background: #eff6ff;
        }
    </style>
@endsection

@push('scripts')
<script>
    // Only allow digits in OTP input
    document.getElementById('otp').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    document.getElementById('verifyForm').addEventListener('submit', function(e) {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<div class="spinner"></div><span>Verifying...</span>';
    });
</script>
@endpush
