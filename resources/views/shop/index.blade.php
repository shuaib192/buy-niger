{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    View: Shop Home - Premium Design
--}}
@extends('layouts.shop')

@section('title', 'Welcome to BuyNiger')

@section('content')
    <!-- Hero Section -->
    <div class="container">
        <div class="hero-section">
            <div class="hero-content">
                <h1 class="hero-title">Experience AI-First Shopping in Nigeria</h1>
                <p class="hero-subtitle">Discover premium Nigerian products with smart recommendations and seamless vendor connections.</p>
                <div class="hero-actions">
                    <a href="{{ route('catalog') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-shopping-bag mr-2"></i> Shop Now
                    </a>
                    <a href="{{ route('register', ['role' => 3]) }}" class="btn btn-outline-white btn-lg">
                        <i class="fas fa-store mr-2"></i> Start Selling
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Categories -->
    @if($featuredCategories->count() > 0)
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Shop by Category</h2>
            <a href="{{ route('catalog') }}" class="text-link">View All</a>
        </div>
        <div class="category-grid">
            @foreach($featuredCategories as $category)
                <a href="{{ route('category', $category->slug) }}" class="category-card">
                    <div class="category-icon">
                        <i class="{{ $category->icon ?? 'fas fa-tags' }}"></i>
                    </div>
                    <h3>{{ $category->name }}</h3>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Featured Products -->
    @if($featuredProducts->count() > 0)
    <div class="container mt-5">
        <div class="section-header">
            <h2 class="section-title">Featured Products</h2>
            <a href="{{ route('catalog') }}" class="text-link">Shop More</a>
        </div>
        <div class="product-grid">
            @foreach($featuredProducts as $product)
                @include('shop.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
    @endif

    <!-- Latest Products -->
    @if($latestProducts->count() > 0)
    <div class="container mt-5 mb-5">
        <div class="section-header">
            <h2 class="section-title">New Arrivals</h2>
            <a href="{{ route('catalog') }}" class="text-link">Browse All</a>
        </div>
        <div class="product-grid">
            @foreach($latestProducts as $product)
                @include('shop.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
    @endif

    <!-- Empty State if no products -->
    @if($featuredProducts->count() == 0 && $latestProducts->count() == 0)
    <div class="container mt-5 mb-5">
        <div style="text-align: center; padding: 80px 20px; background: white; border-radius: 24px;">
            <i class="fas fa-store" style="font-size: 4rem; color: #e2e8f0; margin-bottom: 24px;"></i>
            <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 12px;">Marketplace is Launching Soon</h2>
            <p style="color: #64748b; margin-bottom: 24px;">Be the first to list your products on BuyNiger.</p>
            <a href="{{ route('register', ['role' => 3]) }}" class="btn btn-primary">Become a Vendor</a>
        </div>
    </div>
    @endif
@endsection
