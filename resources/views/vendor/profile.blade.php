{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin
    View: Vendor — Profile Settings — Premium v2.0
--}}
@extends('layouts.app')

@section('title', 'My Profile')
@section('page_title', 'Personal Profile')

@section('sidebar')
    @include('vendor.partials.sidebar')
@endsection

@push('styles')
<style>
.profile-hero {
    background: linear-gradient(135deg, #3730a3 0%, #4f46e5 50%, #7c3aed 100%);
    border-radius: 20px;
    padding: 32px;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    gap: 24px;
    flex-wrap: wrap;
}
.profile-hero::before {
    content: '';
    position: absolute;
    top: -60px; right: -40px;
    width: 200px; height: 200px;
    background: rgba(255,255,255,.07);
    border-radius: 50%;
}
.profile-hero::after {
    content: '';
    position: absolute;
    bottom: -70px; right: 160px;
    width: 160px; height: 160px;
    background: rgba(255,255,255,.05);
    border-radius: 50%;
}
.profile-hero-avatar-wrap { position: relative; z-index: 1; flex-shrink: 0; }
.profile-hero-avatar {
    width: 88px; height: 88px; border-radius: 22px;
    background: rgba(255,255,255,.2);
    border: 3px solid rgba(255,255,255,.4);
    display: flex; align-items: center; justify-content: center;
    font-size: 2rem; font-weight: 800; color: white;
    font-family: 'Outfit', sans-serif;
    overflow: hidden;
}
.profile-hero-avatar img { width: 100%; height: 100%; object-fit: cover; }
.profile-hero-info { position: relative; z-index: 1; flex: 1; }
.profile-hero-name {
    font-size: 1.4rem; font-weight: 800; color: white;
    font-family: 'Outfit', sans-serif; margin-bottom: 4px;
}
.profile-hero-email { color: rgba(255,255,255,.7); font-size: .875rem; margin-bottom: 12px; }
.profile-hero-badges { display: flex; gap: 8px; flex-wrap: wrap; }
.hero-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 12px; border-radius: 20px;
    background: rgba(255,255,255,.15); color: white;
    font-size: .78rem; font-weight: 600; border: 1px solid rgba(255,255,255,.2);
}
.hero-badge .dot { width: 7px; height: 7px; border-radius: 50%; background: #4ade80; }
.profile-hero-actions { position: relative; z-index: 1; }

/* Profile layout */
.profile-grid {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 20px;
    align-items: start;
}
@media (max-width: 900px) {
    .profile-grid { grid-template-columns: 1fr; }
}

/* Form section cards */
.profile-section-card {
    background: var(--surface);
    border: 1.5px solid var(--border-color);
    border-radius: 20px;
    overflow: hidden;
    margin-bottom: 20px;
    transition: box-shadow .2s;
}
.profile-section-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.06); }
.profile-section-header {
    padding: 18px 24px;
    border-bottom: 1px solid var(--border-color);
    display: flex; align-items: center; gap: 12px;
    background: linear-gradient(135deg, rgba(79,70,229,.03), rgba(139,92,246,.03));
}
.psh-icon {
    width: 38px; height: 38px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: .9rem; flex-shrink: 0;
}
.psh-icon.indigo { background: rgba(79,70,229,.1); color: #4338ca; }
.psh-icon.rose   { background: rgba(244,63,94,.1);  color: #be123c; }
.psh-icon.amber  { background: rgba(245,158,11,.1); color: #d97706; }
.psh-icon.teal   { background: rgba(20,184,166,.1); color: #0f766e; }
.psh-title { font-size: .9rem; font-weight: 800; color: var(--text-primary); font-family: 'Outfit', sans-serif; }
.psh-desc  { font-size: .75rem; color: var(--text-muted); margin-top: 1px; }
.profile-section-body { padding: 24px; }

/* Fields */
.field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
@media (max-width: 640px) { .field-row { grid-template-columns: 1fr; } }
.field-group { margin-bottom: 16px; }
.field-group:last-child { margin-bottom: 0; }
.field-label {
    display: block; margin-bottom: 6px;
    font-size: .78rem; font-weight: 700; color: var(--text-secondary);
    text-transform: uppercase; letter-spacing: .04em;
}
.field-input {
    width: 100%; padding: 11px 14px;
    border: 1.5px solid var(--border-color);
    border-radius: 12px; font-size: .875rem;
    color: var(--text-primary); background: white;
    transition: all .15s; box-sizing: border-box;
}
.field-input:focus {
    outline: none; border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79,70,229,.08);
}
.field-input.disabled-field {
    background: rgba(100,116,139,.05);
    color: var(--text-muted); cursor: not-allowed;
}
.field-hint { font-size: .72rem; color: var(--text-muted); margin-top: 4px; }
.field-error { font-size: .75rem; color: #be123c; margin-top: 4px; }

.save-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 11px 24px; border-radius: 12px; font-size: .9rem;
    font-weight: 700; background: linear-gradient(135deg, #4f46e5, #7c3aed);
    color: white; border: none; cursor: pointer; transition: all .2s;
}
.save-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(79,70,229,.3); }

/* Side panel */
.side-panel { display: flex; flex-direction: column; gap: 20px; }

/* Security card */
.security-levels { display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px; }
.sec-level-item {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 14px; border-radius: 11px;
    background: rgba(100,116,139,.05); border: 1px solid var(--border-color);
}
.sec-level-icon {
    width: 32px; height: 32px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: .8rem; flex-shrink: 0;
}
.sec-level-icon.green  { background: rgba(16,185,129,.1); color: #059669; }
.sec-level-icon.blue   { background: rgba(14,165,233,.1); color: #0284c7; }
.sec-level-icon.orange { background: rgba(245,158,11,.1); color: #d97706; }
.sec-level-label { font-size: .8rem; font-weight: 600; color: var(--text-primary); }
.sec-level-status { font-size: .72rem; color: var(--text-muted); }
.change-pass-btn {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    padding: 11px 20px; border-radius: 12px; font-size: .875rem;
    font-weight: 700; width: 100%;
    background: white; color: #4f46e5;
    border: 1.5px solid #c4b5fd; cursor: pointer; transition: all .2s;
    text-decoration: none;
}
.change-pass-btn:hover { background: #ede9fe; color: #4338ca; }

/* Activity card */
.activity-item {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 10px 0; border-bottom: 1px solid var(--border-color);
}
.activity-item:last-child { border-bottom: none; padding-bottom: 0; }
.activity-dot {
    width: 8px; height: 8px; border-radius: 50%;
    background: #4f46e5; flex-shrink: 0; margin-top: 5px;
}
.activity-text { font-size: .8125rem; color: var(--text-secondary); }
.activity-time { font-size: .72rem; color: var(--text-muted); margin-top: 2px; }

/* Auth note */
.auth-note {
    padding: 14px 16px; border-radius: 12px;
    background: rgba(245,158,11,.06);
    border: 1px solid rgba(245,158,11,.2);
    display: flex; align-items: flex-start; gap: 10px;
}
.auth-note-icon { color: #d97706; font-size: .9rem; flex-shrink: 0; margin-top: 1px; }
.auth-note-text { font-size: .78rem; color: var(--text-secondary); line-height: 1.5; }
</style>
@endpush

@section('content')

{{-- ═══ PROFILE HERO ═══ --}}
@php $user = Auth::user(); @endphp
<div class="profile-hero">
    <div class="profile-hero-avatar-wrap">
        <div class="profile-hero-avatar">
            @if($user->avatar_url && !str_contains($user->avatar_url, 'ui-avatars'))
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}">
            @else
                {{ strtoupper(substr($user->name, 0, 1)) }}
            @endif
        </div>
    </div>
    <div class="profile-hero-info">
        <div class="profile-hero-name">{{ $user->name }}</div>
        <div class="profile-hero-email">{{ $user->email }}</div>
        <div class="profile-hero-badges">
            <div class="hero-badge"><i class="fas fa-store" style="font-size:.7rem;"></i> Vendor Account</div>
            @if($user->is_active)
                <div class="hero-badge"><div class="dot"></div> Active</div>
            @endif
            <div class="hero-badge"><i class="fas fa-calendar" style="font-size:.7rem;"></i> Joined {{ $user->created_at->format('M Y') }}</div>
        </div>
    </div>
</div>

{{-- ═══ MAIN GRID ═══ --}}
<div class="profile-grid">

    {{-- ─── LEFT: Forms ─── --}}
    <div>

        {{-- Personal Info --}}
        <div class="profile-section-card">
            <div class="profile-section-header">
                <div class="psh-icon indigo"><i class="fas fa-user"></i></div>
                <div>
                    <div class="psh-title">Personal Information</div>
                    <div class="psh-desc">Update your name and contact details.</div>
                </div>
            </div>
            <div class="profile-section-body">
                @if(session('success'))
                    <div class="alert alert-success" style="margin-bottom:16px;">
                        <i class="fas fa-check-circle"></i> <span>{{ session('success') }}</span>
                    </div>
                @endif
                <form action="{{ route('vendor.profile.update') }}" method="POST">
                    @csrf
                    <div class="field-row">
                        <div class="field-group">
                            <label class="field-label">Full Legal Name</label>
                            <input type="text" name="name" class="field-input @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="field-group">
                            <label class="field-label">Email Address</label>
                            <input type="email" class="field-input disabled-field" value="{{ $user->email }}" disabled>
                            <div class="field-hint">Login email cannot be changed here.</div>
                        </div>
                    </div>
                    <div class="field-group">
                        <label class="field-label">Primary Phone Number</label>
                        <input type="text" name="phone" class="field-input @error('phone') is-invalid @enderror"
                               value="{{ old('phone', $user->phone) }}" placeholder="e.g. 08012345678">
                        @error('phone')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div style="padding-top:8px;border-top:1px solid var(--border-color);margin-top:8px;display:flex;justify-content:flex-end;">
                        <button type="submit" class="save-btn">
                            <i class="fas fa-floppy-disk"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Password Change --}}
        <div class="profile-section-card">
            <div class="profile-section-header">
                <div class="psh-icon rose"><i class="fas fa-lock"></i></div>
                <div>
                    <div class="psh-title">Change Password</div>
                    <div class="psh-desc">Use a strong password to protect your account.</div>
                </div>
            </div>
            <div class="profile-section-body">
                <form action="{{ route('customer.profile') }}" method="POST">
                    @csrf
                    <div class="field-group">
                        <label class="field-label">Current Password</label>
                        <input type="password" name="current_password" class="field-input" placeholder="Enter current password">
                    </div>
                    <div class="field-row">
                        <div class="field-group">
                            <label class="field-label">New Password</label>
                            <input type="password" name="password" class="field-input" placeholder="Min. 8 characters">
                        </div>
                        <div class="field-group">
                            <label class="field-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="field-input" placeholder="Repeat new password">
                        </div>
                    </div>
                    <div style="padding-top:8px;border-top:1px solid var(--border-color);margin-top:8px;display:flex;justify-content:flex-end;">
                        <button type="submit" class="save-btn" style="background:linear-gradient(135deg, #be123c, #e11d48);">
                            <i class="fas fa-key"></i> Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    {{-- ─── RIGHT: Side Panel ─── --}}
    <div class="side-panel">

        {{-- Account Security Status --}}
        <div class="profile-section-card">
            <div class="profile-section-header">
                <div class="psh-icon amber"><i class="fas fa-shield-halved"></i></div>
                <div>
                    <div class="psh-title">Security Status</div>
                    <div class="psh-desc">Your account security overview.</div>
                </div>
            </div>
            <div class="profile-section-body">
                <div class="security-levels">
                    <div class="sec-level-item">
                        <div class="sec-level-icon green"><i class="fas fa-check"></i></div>
                        <div>
                            <div class="sec-level-label">Email Verified</div>
                            <div class="sec-level-status">{{ $user->email_verified_at ? $user->email_verified_at->format('d M Y') : 'Not verified' }}</div>
                        </div>
                    </div>
                    <div class="sec-level-item">
                        <div class="sec-level-icon blue"><i class="fas fa-phone"></i></div>
                        <div>
                            <div class="sec-level-label">Phone Added</div>
                            <div class="sec-level-status">{{ $user->phone ? $user->phone : 'Not provided' }}</div>
                        </div>
                    </div>
                    <div class="sec-level-item">
                        <div class="sec-level-icon orange"><i class="fas fa-calendar-check"></i></div>
                        <div>
                            <div class="sec-level-label">Account Created</div>
                            <div class="sec-level-status">{{ $user->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                </div>
                <div class="auth-note">
                    <i class="fas fa-circle-info auth-note-icon"></i>
                    <div class="auth-note-text">
                        Vendor credentials are shared with your buyer profile. Any password change applies globally to both accounts.
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Links --}}
        <div class="profile-section-card">
            <div class="profile-section-header">
                <div class="psh-icon teal"><i class="fas fa-bolt"></i></div>
                <div>
                    <div class="psh-title">Quick Links</div>
                    <div class="psh-desc">Jump to related sections.</div>
                </div>
            </div>
            <div class="profile-section-body" style="padding:16px;">
                <div style="display:flex;flex-direction:column;gap:8px;">
                    <a href="{{ route('vendor.settings') }}" style="display:flex;align-items:center;gap:10px;padding:10px 13px;border-radius:10px;background:rgba(79,70,229,.05);border:1px solid rgba(79,70,229,.12);color:#4f46e5;font-weight:600;font-size:.85rem;text-decoration:none;transition:all .15s;">
                        <i class="fas fa-store"></i> Store Settings
                        <i class="fas fa-chevron-right" style="margin-left:auto;font-size:.7rem;color:var(--text-muted);"></i>
                    </a>
                    <a href="{{ route('vendor.finances') }}" style="display:flex;align-items:center;gap:10px;padding:10px 13px;border-radius:10px;background:rgba(16,185,129,.05);border:1px solid rgba(16,185,129,.12);color:#059669;font-weight:600;font-size:.85rem;text-decoration:none;transition:all .15s;">
                        <i class="fas fa-wallet"></i> My Finances
                        <i class="fas fa-chevron-right" style="margin-left:auto;font-size:.7rem;color:var(--text-muted);"></i>
                    </a>
                    <a href="{{ route('vendor.dashboard') }}" style="display:flex;align-items:center;gap:10px;padding:10px 13px;border-radius:10px;background:rgba(245,158,11,.05);border:1px solid rgba(245,158,11,.12);color:#d97706;font-weight:600;font-size:.85rem;text-decoration:none;transition:all .15s;">
                        <i class="fas fa-chart-pie"></i> Dashboard
                        <i class="fas fa-chevron-right" style="margin-left:auto;font-size:.7rem;color:var(--text-muted);"></i>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
