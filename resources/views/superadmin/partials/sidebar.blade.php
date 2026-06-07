{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Partial: Super Admin / Admin Sidebar — Premium v2.0
--}}
@php
    if (request()->is('admin*')) {
        $prefix = 'admin.';
    } elseif (request()->is('superadmin*')) {
        $prefix = 'superadmin.';
    } else {
        $prefix = (auth()->user()->role_id == 1) ? 'superadmin.' : 'admin.';
    }
    $isSuperAdmin = ($prefix === 'superadmin.');
@endphp

<div class="nav-section">
    <div class="nav-section-title">Overview</div>
    <a href="{{ route($prefix.'dashboard') }}"
       class="nav-link {{ request()->routeIs($prefix.'dashboard') ? 'active' : '' }}">
        <i class="fas fa-gauge-high"></i>
        <span class="nav-label">Dashboard</span>
    </a>
</div>

<div class="nav-section">
    <div class="nav-section-title">Management</div>
    <a href="{{ route($prefix.'users') }}"
       class="nav-link {{ request()->routeIs($prefix.'users*') ? 'active' : '' }}">
        <i class="fas fa-users"></i>
        <span class="nav-label">Users</span>
    </a>
    <a href="{{ route($prefix.'vendors') }}"
       class="nav-link {{ request()->routeIs($prefix.'vendors*') ? 'active' : '' }}">
        <i class="fas fa-store"></i>
        <span class="nav-label">Vendors</span>
    </a>
    <a href="{{ route($prefix.'products') }}"
       class="nav-link {{ request()->routeIs($prefix.'products*') ? 'active' : '' }}">
        <i class="fas fa-box-open"></i>
        <span class="nav-label">Products</span>
    </a>
    <a href="{{ route($prefix.'orders') }}"
       class="nav-link {{ request()->routeIs($prefix.'orders*') ? 'active' : '' }}">
        <i class="fas fa-bag-shopping"></i>
        <span class="nav-label">Orders</span>
    </a>
    <a href="{{ route($prefix.'transactions') }}"
       class="nav-link {{ request()->routeIs($prefix.'transactions*') ? 'active' : '' }}">
        <i class="fas fa-arrow-right-arrow-left"></i>
        <span class="nav-label">Transactions</span>
    </a>
    @if($isSuperAdmin)
    <a href="{{ route('superadmin.payouts') }}"
       class="nav-link {{ request()->routeIs('superadmin.payouts*') ? 'active' : '' }}">
        <i class="fas fa-money-bill-transfer"></i>
        <span class="nav-label">Payouts</span>
    </a>
    @endif
    <a href="{{ route($prefix.'disputes') }}"
       class="nav-link {{ request()->routeIs($prefix.'disputes*') ? 'active' : '' }}">
        <i class="fas fa-scale-balanced"></i>
        <span class="nav-label">Disputes</span>
    </a>
    <a href="{{ route($prefix.'messages') }}"
       class="nav-link {{ request()->routeIs($prefix.'messages*') ? 'active' : '' }}">
        <i class="fas fa-envelope-open-text"></i>
        <span class="nav-label">Messages</span>
    </a>
</div>

@if($isSuperAdmin)
<div class="nav-section">
    <div class="nav-section-title">Intelligence</div>
    <a href="{{ route($prefix.'analytics') }}"
       class="nav-link {{ request()->routeIs($prefix.'analytics*') ? 'active' : '' }}">
        <i class="fas fa-chart-line"></i>
        <span class="nav-label">Analytics</span>
    </a>
    <a href="{{ route($prefix.'ai') }}"
       class="nav-link {{ request()->routeIs($prefix.'ai') ? 'active' : '' }}">
        <i class="fas fa-robot"></i>
        <span class="nav-label">AI Control</span>
    </a>
    <a href="{{ route($prefix.'ai.settings') }}"
       class="nav-link {{ request()->routeIs($prefix.'ai.settings*') ? 'active' : '' }}">
        <i class="fas fa-sliders"></i>
        <span class="nav-label">AI Configuration</span>
    </a>
    <a href="{{ route($prefix.'audit') }}"
       class="nav-link {{ request()->routeIs($prefix.'audit*') ? 'active' : '' }}">
        <i class="fas fa-clipboard-list"></i>
        <span class="nav-label">Audit Logs</span>
    </a>
</div>

<div class="nav-section">
    <div class="nav-section-title">Settings</div>
    <a href="{{ route($prefix.'settings.payments') }}"
       class="nav-link {{ request()->routeIs($prefix.'settings.payments*') ? 'active' : '' }}">
        <i class="fas fa-credit-card"></i>
        <span class="nav-label">Payments</span>
    </a>
    <a href="{{ route($prefix.'settings.email') }}"
       class="nav-link {{ request()->routeIs($prefix.'settings.email*') ? 'active' : '' }}">
        <i class="fas fa-at"></i>
        <span class="nav-label">Email Config</span>
    </a>
    <a href="{{ route($prefix.'settings') }}"
       class="nav-link {{ request()->routeIs($prefix.'settings') ? 'active' : '' }}">
        <i class="fas fa-gear"></i>
        <span class="nav-label">System Settings</span>
    </a>
</div>
@endif
