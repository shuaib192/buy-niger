{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    Partial: Super Admin Sidebar
--}}
@php
    if (request()->is('admin*')) {
        $prefix = 'admin.';
    } elseif (request()->is('superadmin*')) {
        $prefix = 'superadmin.';
    } else {
        // Fallback: use role to determine prefix
        $prefix = (auth()->user()->role_id == 1) ? 'superadmin.' : 'admin.';
    }
@endphp
<div class="nav-section">
    <div class="nav-section-title">Main</div>
    <a href="{{ route($prefix.'dashboard') }}" class="nav-link {{ request()->routeIs($prefix.'dashboard') ? 'active' : '' }}">
        <i class="fas fa-th-large"></i>
        <span class="nav-label">Dashboard</span>
    </a>
    {{-- 
    <a href="#" class="nav-link">
        <i class="fas fa-chart-line"></i>
        <span class="nav-label">Analytics</span>
    </a>
    --}}
</div>

<div class="nav-section">
    <div class="nav-section-title">Management</div>
    <a href="{{ route($prefix.'users') }}" class="nav-link {{ request()->routeIs($prefix.'users*') ? 'active' : '' }}">
        <i class="fas fa-users"></i>
        <span class="nav-label">Users</span>
    </a>
    <a href="{{ route($prefix.'vendors') }}" class="nav-link {{ request()->routeIs($prefix.'vendors*') ? 'active' : '' }}">
        <i class="fas fa-store"></i>
        <span class="nav-label">Vendors</span>
    </a>
    <a href="{{ route($prefix.'products') }}" class="nav-link {{ request()->routeIs($prefix.'products*') ? 'active' : '' }}">
        <i class="fas fa-box"></i>
        <span class="nav-label">Products</span>
    </a>
    <a href="{{ route($prefix.'orders') }}" class="nav-link {{ request()->routeIs($prefix.'orders*') ? 'active' : '' }}">
        <i class="fas fa-shopping-cart"></i>
        <span class="nav-label">Orders</span>
    </a>
    @if(auth()->user()->role_id == 1)
    <a href="{{ route('superadmin.payouts') }}" class="nav-link {{ request()->routeIs('superadmin.payouts*') ? 'active' : '' }}">
        <i class="fas fa-money-bill-wave"></i>
        <span class="nav-label">Payouts</span>
    </a>
    @endif
    <a href="{{ route($prefix.'transactions') }}" class="nav-link {{ request()->routeIs($prefix.'transactions*') ? 'active' : '' }}">
        <i class="fas fa-history"></i>
        <span class="nav-label">Transactions</span>
    </a>
    <a href="{{ route($prefix.'disputes') }}" class="nav-link {{ request()->routeIs($prefix.'disputes*') ? 'active' : '' }}">
        <i class="fas fa-exclamation-circle"></i>
        <span class="nav-label">Disputes</span>
    </a>
    <a href="{{ route($prefix.'messages') }}" class="nav-link {{ request()->routeIs($prefix.'messages*') ? 'active' : '' }}">
        <i class="fas fa-envelope-open-text"></i>
        <span class="nav-label">Messages</span>
    </a>
</div>

@if($prefix === 'superadmin.')
<div class="nav-section">
    <div class="nav-section-title">AI System</div>
    <a href="{{ route($prefix.'ai') }}" class="nav-link {{ request()->routeIs($prefix.'ai') ? 'active' : '' }}">
        <i class="fas fa-robot"></i>
        <span class="nav-label">AI Control</span>
    </a>

    <a href="{{ route($prefix.'ai.settings') }}" class="nav-link {{ request()->routeIs($prefix.'ai.settings*') ? 'active' : '' }}">
        <i class="fas fa-robot"></i>
        <span>AI Configuration</span>
    </a>
    {{-- 
    <a href="#" class="nav-link">
        <i class="fas fa-brain"></i>
        <span class="nav-label">Simulations</span>
    </a>
    <a href="#" class="nav-link">
        <i class="fas fa-shield-alt"></i>
        <span class="nav-label">Permissions</span>
    </a>
    --}}
</div>
@endif

<div class="nav-section">
    <div class="nav-section-title">Settings</div>
    
@if($prefix === 'superadmin.')
    <a href="{{ route($prefix.'analytics') }}" class="nav-link {{ request()->routeIs($prefix.'analytics*') ? 'active' : '' }}">
        <i class="fas fa-chart-line"></i>
        <span>Analytics</span>
    </a>
    <a href="{{ route($prefix.'settings.payments') }}" class="nav-link {{ request()->routeIs($prefix.'settings.payments*') ? 'active' : '' }}">
        <i class="fas fa-credit-card"></i>
        <span>Payments</span>
    </a>
    <a href="{{ route($prefix.'settings.email') }}" class="nav-link {{ request()->routeIs($prefix.'settings.email*') ? 'active' : '' }}">
        <i class="fas fa-envelope"></i>
        <span>Email Config</span>
    </a>
    <a href="{{ route($prefix.'audit') }}" class="nav-link {{ request()->routeIs($prefix.'audit*') ? 'active' : '' }}">
        <i class="fas fa-history"></i>
        <span>Audit Logs</span>
    </a>
    <a href="{{ route($prefix.'settings') }}" class="nav-link {{ request()->routeIs($prefix.'settings') ? 'active' : '' }}">
        <i class="fas fa-cog"></i>
        <span class="nav-label">System Settings</span>
    </a>
@endif
</div>
