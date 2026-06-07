{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    Partial: Customer Sidebar
--}}
<div class="nav-section">
    <div class="nav-section-title">Account</div>
    <a href="{{ route('customer.dashboard') }}" class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
        <i class="fas fa-th-large"></i>
        <span class="nav-label">Dashboard</span>
    </a>
    <a href="{{ route('orders.index') }}" class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">
        <i class="fas fa-shopping-bag"></i>
        <span class="nav-label">My Orders</span>
    </a>
    <a href="{{ route('wishlist.index') }}" class="nav-link {{ request()->routeIs('wishlist.*') ? 'active' : '' }}">
        <i class="fas fa-heart"></i>
        <span class="nav-label">My Wishlist</span>
    </a>
    <a href="{{ route('customer.messages.index') }}" class="nav-link {{ request()->routeIs('customer.messages.*') ? 'active' : '' }}">
        <i class="fas fa-envelope"></i>
        <span class="nav-label">Messages</span>
    </a>
</div>

<div class="nav-section">
    <div class="nav-section-title">Settings</div>
    <a href="{{ route('customer.profile') }}" class="nav-link {{ request()->routeIs('customer.profile') ? 'active' : '' }}">
        <i class="fas fa-user"></i>
        <span class="nav-label">Profile</span>
    </a>
    <a href="{{ route('customer.addresses') }}" class="nav-link {{ request()->routeIs('customer.addresses') ? 'active' : '' }}">
        <i class="fas fa-map-marker-alt"></i>
        <span class="nav-label">Addresses</span>
    </a>
</div>

<div class="nav-section">
    <a href="{{ route('home') }}" class="nav-link">
        <i class="fas fa-arrow-left"></i>
        <span class="nav-label">Back to Shop</span>
    </a>
</div>
