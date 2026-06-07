{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Partial: Customer Sidebar — Premium v2.0
--}}
<div class="nav-section">
    <div class="nav-section-title">My Account</div>
    <a href="{{ route('customer.dashboard') }}"
       class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
        <i class="fas fa-gauge-high"></i>
        <span class="nav-label">Dashboard</span>
    </a>
    <a href="{{ route('orders.index') }}"
       class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">
        <i class="fas fa-bag-shopping"></i>
        <span class="nav-label">My Orders</span>
    </a>
    <a href="{{ route('wishlist.index') }}"
       class="nav-link {{ request()->routeIs('wishlist.*') ? 'active' : '' }}">
        <i class="fas fa-heart"></i>
        <span class="nav-label">My Wishlist</span>
    </a>
    <a href="{{ route('customer.reviews.index') }}"
       class="nav-link {{ request()->routeIs('customer.reviews.*') ? 'active' : '' }}">
        <i class="fas fa-star"></i>
        <span class="nav-label">My Reviews</span>
    </a>
    <a href="{{ route('customer.messages.index') }}"
       class="nav-link {{ request()->routeIs('customer.messages.*') ? 'active' : '' }}">
        <i class="fas fa-comments"></i>
        <span class="nav-label">Messages</span>
    </a>
</div>

<div class="nav-section">
    <div class="nav-section-title">Settings</div>
    <a href="{{ route('customer.profile') }}"
       class="nav-link {{ request()->routeIs('customer.profile') ? 'active' : '' }}">
        <i class="fas fa-user-pen"></i>
        <span class="nav-label">Profile</span>
    </a>
    <a href="{{ route('customer.addresses') }}"
       class="nav-link {{ request()->routeIs('customer.addresses') ? 'active' : '' }}">
        <i class="fas fa-location-dot"></i>
        <span class="nav-label">Addresses</span>
    </a>
    <a href="{{ route('notifications.index') }}"
       class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
        <i class="fas fa-bell"></i>
        <span class="nav-label">Notifications</span>
    </a>
</div>

<div class="nav-section">
    <a href="{{ route('catalog') }}" class="nav-link">
        <i class="fas fa-shop"></i>
        <span class="nav-label">Browse Shop</span>
    </a>
    <a href="{{ route('home') }}" class="nav-link">
        <i class="fas fa-arrow-left"></i>
        <span class="nav-label">Back to Home</span>
    </a>
</div>
