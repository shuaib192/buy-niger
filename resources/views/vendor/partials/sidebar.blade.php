{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    Partial: Vendor Sidebar
--}}
<div class="nav-section">
    <div class="nav-section-title">Main</div>
    <a href="{{ route('vendor.dashboard') }}" class="nav-link {{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}">
        <i class="fas fa-th-large"></i>
        <span class="nav-label">Dashboard</span>
    </a>
    <a href="{{ route('vendor.analytics') }}" class="nav-link {{ request()->routeIs('vendor.analytics') ? 'active' : '' }}">
        <i class="fas fa-chart-line"></i>
        <span class="nav-label">Analytics</span>
    </a>
    @if(Auth::user()->vendor && Auth::user()->vendor->slug)
    <a href="{{ route('store.show', Auth::user()->vendor->slug) }}" class="nav-link" target="_blank">
        <i class="fas fa-external-link-alt"></i>
        <span class="nav-label">View Store</span>
    </a>
    @endif
</div>

<div class="nav-section">
    <div class="nav-section-title">Store Management</div>
    <a href="{{ route('vendor.products') }}" class="nav-link {{ request()->routeIs('vendor.products*') ? 'active' : '' }}">
        <i class="fas fa-box"></i>
        <span class="nav-label">Products</span>
    </a>
    <a href="{{ route('vendor.inventory') }}" class="nav-link {{ request()->routeIs('vendor.inventory*') ? 'active' : '' }}">
        <i class="fas fa-warehouse"></i>
        <span class="nav-label">Inventory</span>
    </a>
    <a href="{{ route('vendor.orders') }}" class="nav-link {{ request()->routeIs('vendor.orders*') ? 'active' : '' }}">
        <i class="fas fa-shopping-cart"></i>
        <span class="nav-label">Orders</span>
    </a>
    <a href="{{ route('vendor.coupons') }}" class="nav-link {{ request()->routeIs('vendor.coupons*') ? 'active' : '' }}">
        <i class="fas fa-ticket-alt"></i>
        <span class="nav-label">Coupons</span>
    </a>
</div>

<div class="nav-section">
    <div class="nav-section-title">Communication</div>
    <a href="{{ route('vendor.messages.index') }}" class="nav-link {{ request()->routeIs('vendor.messages.*') ? 'active' : '' }}">
        <i class="fas fa-envelope"></i>
        <span class="nav-label">Messages</span>
    </a>
</div>

<div class="nav-section">
    <div class="nav-section-title">Finances</div>
    <a href="{{ route('vendor.finances') }}" class="nav-link {{ request()->routeIs('vendor.finances*') ? 'active' : '' }}">
        <i class="fas fa-wallet"></i>
        <span class="nav-label">Finances & Payouts</span>
    </a>
</div>

<div class="nav-section">
    <div class="nav-section-title">Settings</div>
    <a href="{{ route('vendor.settings') }}" class="nav-link {{ request()->routeIs('vendor.settings*') ? 'active' : '' }}">
        <i class="fas fa-store"></i>
        <span class="nav-label">Store Settings</span>
    </a>
    <a href="{{ route('vendor.profile') }}" class="nav-link {{ request()->routeIs('vendor.profile*') ? 'active' : '' }}">
        <i class="fas fa-user-cog"></i>
        <span class="nav-label">Account Security</span>
    </a>
</div>
