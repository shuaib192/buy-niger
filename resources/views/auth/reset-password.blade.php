{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    View: Reset Password (OTP Verification)
--}}
@extends('layouts.shop')

@section('title', 'Reset Password')

@section('content')
<div class="container" style="padding-top:60px; padding-bottom:80px;">
    <div style="max-width:480px; margin:0 auto;">
        
        <div class="auth-card">
            <div class="auth-header">
                <h2>Set New Password</h2>
                <p>Enter the OTP sent to <strong>{{ $email }}</strong> and choose a new password.</p>
            </div>

            <!-- Messages -->
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin:0; padding-left:20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">

                <!-- OTP Input -->
                <div class="form-group">
                    <label>Enter 6-Digit Code</label>
                    <div class="otp-input-wrapper">
                        <input type="text" name="otp" class="form-control otp-field" placeholder="123456" maxlength="6" autocomplete="off" autofocus required>
                    </div>
                    <small class="text-muted">Check your email inbox (and spam folder).</small>
                </div>

                <!-- New Password -->
                <div class="form-group">
                    <label>New Password</label>
                    <div class="input-icon-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label>Confirm Password</label>
                    <div class="input-icon-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:16px;">
                    Reset Password
                </button>
            </form>

            <div class="auth-footer">
                <p>Didn't receive code? <a href="{{ route('password.request') }}">Resend OTP</a></p>
                <p><a href="{{ route('login') }}" style="color:#64748b; font-weight:normal;">Back to Login</a></p>
            </div>
        </div>
    </div>
</div>

<style>
    .auth-card {
        background: white; border-radius: 24px; padding: 40px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;
    }
    .auth-header { text-align: center; margin-bottom: 32px; }
    .auth-header h2 { font-size: 24px; font-weight: 800; color: #1e293b; margin-bottom: 8px; }
    .auth-header p { color: #64748b; font-size: 14px; }

    .otp-field {
        font-size: 24px; letter-spacing: 8px; text-align: center; font-weight: 700; color: #1e293b;
        padding: 12px; height: 60px;
    }

    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 8px; }
    
    .input-icon-wrapper { position: relative; }
    .input-icon { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8; }
    .form-control {
        width: 100%; padding: 12px 16px 12px 44px; border: 1px solid #cbd5e1; border-radius: 12px;
        font-size: 14px; transition: all 0.2s;
    }
    .otp-field { padding: 12px; } /* Override for centered OTP */
    
    .form-control:focus { border-color: #3b82f6; outline: none; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }

    .btn-block { width: 100%; }
    .btn-lg { padding: 14px; font-size: 16px; }

    .alert { padding: 12px 16px; border-radius: 10px; margin-bottom: 24px; font-size: 14px; }
    .alert-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
    .alert-danger { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

    .auth-footer { text-align: center; margin-top: 24px; font-size: 14px; color: #64748b; }
    .auth-footer a { color: #3b82f6; text-decoration: none; font-weight: 600; }
</style>
@endsection
