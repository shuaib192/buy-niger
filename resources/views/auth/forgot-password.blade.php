{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    View: Forgot Password (OTP Method Selection)
--}}
@extends('layouts.shop')

@section('title', 'Forgot Password')

@section('content')
<div class="container" style="padding-top:60px; padding-bottom:80px;">
    <div style="max-width:480px; margin:0 auto;">
        
        <div class="auth-card">
            <div class="auth-header">
                <h2>Forgot Password?</h2>
                <p>No worries! How would you like to receive your reset code?</p>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('info'))
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> {{ session('info') }}
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

            <form action="{{ route('password.email') }}" method="POST" id="forgotForm">
                @csrf
                
                <!-- Method Toggle -->
                <div class="method-toggle">
                    <input type="radio" name="method" value="email" id="methodEmail" checked onchange="toggleMethod()">
                    <label for="methodEmail" class="method-btn">
                        <i class="fas fa-envelope"></i> Email Address
                    </label>

                    <input type="radio" name="method" value="whatsapp" id="methodWhatsapp" onchange="toggleMethod()">
                    <label for="methodWhatsapp" class="method-btn">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </label>
                </div>

                <!-- Email Input -->
                <div class="form-group" id="emailGroup">
                    <label>Email Address</label>
                    <div class="input-icon-wrapper">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" name="email" class="form-control" placeholder="you@example.com" value="{{ old('email') }}">
                    </div>
                </div>

                <!-- Phone Input (Hidden by default) -->
                <div class="form-group" id="phoneGroup" style="display:none;">
                    <label>WhatsApp Number</label>
                    <div class="input-icon-wrapper">
                        <i class="fab fa-whatsapp input-icon"></i>
                        <input type="text" name="phone" class="form-control" placeholder="08012345678" value="{{ old('phone') }}">
                    </div>
                    <small class="text-muted">Currently unavailable. Please use email reset.</small>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:24px;">
                    Send Reset Code
                </button>
            </form>

            <div class="auth-footer">
                <p>Remember your password? <a href="{{ route('login') }}">Sign In</a></p>
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

    /* Method Toggle */
    .method-toggle {
        display: flex; background: #f1f5f9; padding: 4px; border-radius: 12px; margin-bottom: 24px;
    }
    .method-toggle input { display: none; }
    .method-btn {
        flex: 1; text-align: center; padding: 10px; border-radius: 10px;
        font-size: 14px; font-weight: 600; color: #64748b; cursor: pointer;
        transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .method-toggle input:checked + .method-btn {
        background: white; color: #3b82f6; box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 8px; }
    
    .input-icon-wrapper { position: relative; }
    .input-icon { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8; }
    .form-control {
        width: 100%; padding: 12px 16px 12px 44px; border: 1px solid #cbd5e1; border-radius: 12px;
        font-size: 14px; transition: all 0.2s;
    }
    .form-control:focus { border-color: #3b82f6; outline: none; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }

    .btn-block { width: 100%; }
    .btn-lg { padding: 14px; font-size: 16px; }

    .alert { padding: 12px 16px; border-radius: 10px; margin-bottom: 24px; font-size: 14px; display: flex; align-items: start; gap: 10px; }
    .alert-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
    .alert-danger { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
    .alert-info { background: #eff6ff; color: #1e40af; border: 1px solid #dbeafe; }

    .auth-footer { text-align: center; margin-top: 24px; font-size: 14px; color: #64748b; }
    .auth-footer a { color: #3b82f6; text-decoration: none; font-weight: 600; }
</style>

<script>
    function toggleMethod() {
        const isEmail = document.getElementById('methodEmail').checked;
        document.getElementById('emailGroup').style.display = isEmail ? 'block' : 'none';
        document.getElementById('phoneGroup').style.display = isEmail ? 'none' : 'block';
    }
</script>
@endsection
