{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    View: Customer Profile (Premium v2.0)
--}}
@extends('layouts.app')

@section('title', 'My Profile')
@section('page_title', 'Profile Settings')

@section('sidebar')
    @include('customer.partials.sidebar')
@endsection

@section('content')
<div class="profile-page">

    {{-- Success Alert --}}
    @if(session('success'))
    <div class="prof-alert prof-alert-ok" id="profileAlert">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
        <button onclick="this.parentElement.remove()" class="prof-alert-close">&times;</button>
    </div>
    @endif

    {{-- Page Header --}}
    <div class="prof-header">
        <div class="prof-avatar-section">
            <div class="prof-avatar-ring">
                <div class="prof-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                <div class="prof-avatar-status"></div>
            </div>
            <div>
                <h1 class="prof-name">{{ auth()->user()->name }}</h1>
                <p class="prof-email"><i class="fas fa-envelope"></i> {{ auth()->user()->email }}</p>
                <span class="prof-badge"><i class="fas fa-user-check"></i> Verified Customer</span>
            </div>
        </div>
    </div>

    <div class="prof-grid">
        {{-- Personal Information --}}
        <div class="prof-card prof-card-main">
            <div class="prof-card-header">
                <div class="prof-card-icon">
                    <i class="fas fa-user-edit"></i>
                </div>
                <div>
                    <h2>Personal Information</h2>
                    <p>Update your name and contact details</p>
                </div>
            </div>
            <form action="{{ route('customer.profile.update') }}" method="POST" class="prof-form">
                @csrf
                <div class="prof-form-grid">
                    <div class="prof-field">
                        <label for="prof_name">Full Name</label>
                        <div class="prof-input-wrap">
                            <i class="fas fa-user"></i>
                            <input type="text" id="prof_name" name="name" class="prof-input" 
                                   value="{{ old('name', $user->name) }}" required placeholder="Your full name">
                        </div>
                        @error('name')
                            <span class="prof-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                        @enderror
                    </div>
                    <div class="prof-field">
                        <label>Email Address</label>
                        <div class="prof-input-wrap">
                            <i class="fas fa-envelope"></i>
                            <input type="email" class="prof-input" value="{{ $user->email }}" disabled>
                            <span class="prof-lock-badge"><i class="fas fa-lock"></i></span>
                        </div>
                        <small class="prof-hint">Email cannot be changed for security reasons</small>
                    </div>
                </div>
                <div class="prof-field">
                    <label for="prof_phone">Phone Number</label>
                    <div class="prof-input-wrap">
                        <i class="fas fa-phone-alt"></i>
                        <input type="text" id="prof_phone" name="phone" class="prof-input" 
                               value="{{ old('phone', $user->phone) }}" placeholder="e.g. 08012345678">
                    </div>
                    @error('phone')
                        <span class="prof-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>
                <div class="prof-form-actions">
                    <button type="submit" class="prof-btn-primary">
                        <i class="fas fa-save"></i>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

        {{-- Change Password --}}
        <div class="prof-card">
            <div class="prof-card-header">
                <div class="prof-card-icon prof-icon-security">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div>
                    <h2>Security</h2>
                    <p>Change your password</p>
                </div>
            </div>
            <form action="{{ route('customer.password.update') }}" method="POST" class="prof-form">
                @csrf
                <div class="prof-field">
                    <label for="curr_pass">Current Password</label>
                    <div class="prof-input-wrap">
                        <i class="fas fa-key"></i>
                        <input type="password" id="curr_pass" name="current_password" class="prof-input" required placeholder="••••••••">
                    </div>
                    @error('current_password')
                        <span class="prof-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>
                <div class="prof-field">
                    <label for="new_pass">New Password</label>
                    <div class="prof-input-wrap">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="new_pass" name="password" class="prof-input" required placeholder="Min 8 characters">
                    </div>
                    @error('password')
                        <span class="prof-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>
                <div class="prof-field">
                    <label for="conf_pass">Confirm New Password</label>
                    <div class="prof-input-wrap">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="conf_pass" name="password_confirmation" class="prof-input" required placeholder="Repeat new password">
                    </div>
                </div>

                <div class="prof-password-tips">
                    <span class="pass-tip"><i class="fas fa-check"></i> At least 8 characters</span>
                    <span class="pass-tip"><i class="fas fa-check"></i> Mix letters & numbers</span>
                </div>

                <div class="prof-form-actions">
                    <button type="submit" class="prof-btn-security">
                        <i class="fas fa-lock"></i>
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.profile-page { animation: profFadeIn 0.35s ease; }
@keyframes profFadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

/* Alert */
.prof-alert {
    display: flex; align-items: center; gap: 12px;
    padding: 14px 18px; border-radius: 14px; font-size: 14px; font-weight: 600;
    margin-bottom: 24px; animation: profFadeIn 0.3s ease;
}
.prof-alert-ok { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
.prof-alert-close { margin-left: auto; background: none; border: none; font-size: 20px; cursor: pointer; color: currentColor; opacity: 0.6; line-height: 1; }
.prof-alert-close:hover { opacity: 1; }

/* Header */
.prof-header {
    background: white;
    border: 1px solid #f1f5f9;
    border-radius: 22px;
    padding: 28px;
    margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
}
.prof-avatar-section { display: flex; align-items: center; gap: 22px; flex-wrap: wrap; }
.prof-avatar-ring {
    width: 80px; height: 80px; position: relative; flex-shrink: 0;
    border-radius: 24px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6, #ec4899);
    padding: 3px;
    box-shadow: 0 8px 24px rgba(99,102,241,0.3);
}
.prof-avatar {
    width: 100%; height: 100%; border-radius: 21px;
    background: white;
    display: flex; align-items: center; justify-content: center;
    font-size: 28px; font-weight: 900;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}
.prof-avatar-status {
    position: absolute; bottom: 2px; right: 2px;
    width: 16px; height: 16px; background: #22c55e;
    border: 3px solid white; border-radius: 50%;
}
.prof-name { font-size: 22px; font-weight: 900; color: #0f172a; margin: 0 0 4px; letter-spacing: -0.02em; }
.prof-email { font-size: 13px; color: #64748b; margin: 0 0 8px; display: flex; align-items: center; gap: 6px; }
.prof-badge {
    display: inline-flex; align-items: center; gap: 6px;
    background: linear-gradient(135deg, #eef2ff, #f0f9ff);
    color: #6366f1; font-size: 11px; font-weight: 700;
    padding: 4px 12px; border-radius: 20px;
    border: 1px solid #c7d2fe;
}

/* Grid */
.prof-grid {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 20px;
    align-items: start;
}

/* Cards */
.prof-card {
    background: white;
    border: 1px solid #f1f5f9;
    border-radius: 22px;
    padding: 28px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
}
.prof-card-header {
    display: flex; align-items: center; gap: 16px;
    margin-bottom: 24px; padding-bottom: 18px;
    border-bottom: 1px solid #f8fafc;
}
.prof-card-icon {
    width: 48px; height: 48px; border-radius: 14px;
    background: #eef2ff; color: #6366f1;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; flex-shrink: 0;
}
.prof-icon-security { background: #fff7ed; color: #f59e0b; }
.prof-card-header h2 { font-size: 16px; font-weight: 800; color: #0f172a; margin: 0 0 2px; }
.prof-card-header p { font-size: 12px; color: #94a3b8; margin: 0; }

/* Form */
.prof-form { display: flex; flex-direction: column; }
.prof-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.prof-field { margin-bottom: 18px; display: flex; flex-direction: column; gap: 7px; }
.prof-field label { font-size: 12px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; }
.prof-input-wrap { position: relative; }
.prof-input-wrap > i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 13px; pointer-events: none; z-index: 1; }
.prof-input {
    width: 100%; padding: 12px 14px 12px 40px;
    border: 2px solid #e8edf5;
    border-radius: 12px;
    font-size: 14px; font-weight: 500;
    color: #0f172a; background: #fafbfc;
    transition: all 0.2s; outline: none;
    box-sizing: border-box;
}
.prof-input:focus { border-color: #6366f1; background: white; box-shadow: 0 0 0 4px rgba(99,102,241,0.08); }
.prof-input:disabled { background: #f8fafc; color: #94a3b8; cursor: not-allowed; opacity: 1; }
.prof-lock-badge { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: #f1f5f9; color: #94a3b8; font-size: 11px; padding: 3px 8px; border-radius: 6px; pointer-events: none; }
.prof-hint { font-size: 11px; color: #94a3b8; }
.prof-error { font-size: 12px; color: #ef4444; display: flex; align-items: center; gap: 5px; }

/* Password Tips */
.prof-password-tips { display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }
.pass-tip { font-size: 11px; color: #94a3b8; display: flex; align-items: center; gap: 5px; font-weight: 500; }
.pass-tip i { color: #22c55e; font-size: 10px; }

/* Actions */
.prof-form-actions { display: flex; justify-content: flex-end; margin-top: 4px; }
.prof-btn-primary, .prof-btn-security {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 12px 24px; border: none; border-radius: 14px;
    font-size: 14px; font-weight: 700; cursor: pointer; transition: all 0.2s;
}
.prof-btn-primary {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    box-shadow: 0 4px 14px rgba(99,102,241,0.3);
}
.prof-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(99,102,241,0.4); }
.prof-btn-security {
    background: linear-gradient(135deg, #f59e0b, #ef4444);
    color: white; width: 100%; justify-content: center;
    box-shadow: 0 4px 14px rgba(245,158,11,0.25);
}
.prof-btn-security:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(245,158,11,0.35); }

/* Responsive */
@media (max-width: 1024px) { .prof-grid { grid-template-columns: 1fr; } }
@media (max-width: 768px) { 
    .prof-form-grid { grid-template-columns: 1fr; }
    .prof-avatar-section { gap: 16px; }
    .prof-name { font-size: 18px; }
}
</style>
@endsection
