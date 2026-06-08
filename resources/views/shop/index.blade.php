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
            <div class="hero-image-bg">
                <img src="https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=1200&q=80" alt="Nigerian marketplace" class="hero-bg-img">
            </div>

            <div class="hero-grid">
                <div>
                    <span class="hero-badge"><i class="fas fa-bolt"></i> AI-Powered Shopping</span>
                    <h1 class="hero-title">Shop <span class="text-highlight">Smarter</span>,<br>Live <span class="text-highlight">Better</span>.</h1>
                    <p class="hero-subtitle">Discover thousands of premium Nigerian products from trusted vendors — all in one marketplace.</p>
                    <div class="hero-actions">
                        <a href="{{ route('catalog') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-shopping-bag"></i> Explore Products
                        </a>
                        @auth
                            <a href="{{ route('vendor.apply') }}" class="btn btn-outline-white btn-lg">
                        @else
                            <a href="{{ route('vendor.register') }}" class="btn btn-outline-white btn-lg">
                        @endauth
                            <i class="fas fa-store"></i> Sell on BuyNiger
                        </a>
                    </div>
                    <div class="hero-links">
                        <a href="{{ route('about') }}"><i class="fas fa-info-circle"></i> About Us</a>
                        <a href="{{ route('contact') }}"><i class="fas fa-headset"></i> Contact</a>
                        <a href="{{ route('catalog') }}"><i class="fas fa-th-large"></i> All Products</a>
                    </div>
                </div>
                <div class="hero-visuals">
                    <img src="https://images.unsplash.com/photo-1531123897727-8f129e1688ce?w=400&q=80" alt="" class="hero-person hero-person-1">
                    <img src="https://images.unsplash.com/photo-1506277886164-e25aa3f4ef7f?w=400&q=80" alt="" class="hero-person hero-person-2">
                    <div class="hero-floating-card card-1">
                        <i class="fas fa-truck"></i>
                        <span>Fast Delivery</span>
                    </div>
                    <div class="hero-floating-card card-2">
                        <i class="fas fa-shield-alt"></i>
                        <span>Secure Payments</span>
                    </div>
                    <div class="hero-floating-card card-3">
                        <i class="fas fa-star"></i>
                        <span>Top Rated</span>
                    </div>
                </div>
            </div>

            <div class="hero-stats-bar">
                <div class="hero-stat">
                    <strong>500+</strong>
                    <span>Products</span>
                </div>
                <div class="hero-stat-divider"></div>
                <div class="hero-stat">
                    <strong>50+</strong>
                    <span>Vendors</span>
                </div>
                <div class="hero-stat-divider"></div>
                <div class="hero-stat">
                    <strong>24/7</strong>
                    <span>Support</span>
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
        <div class="product-grid carousel-mobile">
            @foreach($featuredProducts as $product)
                @include('shop.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
    @endif

    <!-- Latest Products (Carousel) -->
    @if($latestProducts->count() > 0)
    <div class="container mt-5 mb-5">
        <div class="section-header">
            <h2 class="section-title">New Arrivals</h2>
            <a href="{{ route('catalog') }}" class="text-link">Browse All</a>
        </div>
        <div class="product-grid carousel-mobile">
            @foreach($latestProducts as $product)
                @include('shop.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
    @endif

    <!-- Best Sellers (Normal 2-col grid on mobile) -->
    @if($bestSellers->count() > 0)
    <div class="container mt-5 mb-5">
        <div class="section-header">
            <h2 class="section-title">🔥 Best Sellers</h2>
            <a href="{{ route('catalog', ['sort' => 'popular']) }}" class="text-link">View All</a>
        </div>
        <div class="product-grid">
            @foreach($bestSellers as $product)
                @include('shop.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
    @endif

    <!-- Top Stores -->
    @if($topStores->count() > 0)
    <div class="container mt-5 mb-5">
        <div class="section-header">
            <h2 class="section-title">🏪 Top Stores</h2>
            <a href="{{ route('catalog') }}" class="text-link">Browse All</a>
        </div>
        <div class="stores-grid">
            @foreach($topStores as $vendor)
            <a href="{{ url('/store/' . $vendor->store_slug) }}" class="store-card">
                <img src="{{ $vendor->logo_url }}" alt="{{ $vendor->store_name }}" class="store-logo">
                <div class="store-info">
                    <h4>{{ Str::limit($vendor->store_name, 20) }}</h4>
                    <div class="store-meta">
                        <span class="store-rating"><i class="fas fa-star"></i> {{ number_format($vendor->rating ?? 0, 1) }}</span>
                        <span class="store-products">{{ $vendor->total_products ?? 0 }} products</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Become a Vendor CTA -->
    <div class="container mt-5 mb-5">
        <div class="vendor-cta">
            <div class="vendor-cta-content">
                <div class="vendor-cta-icon"><i class="fas fa-store"></i></div>
                <h2>Start Selling on BuyNiger</h2>
                <p>Join hundreds of vendors earning daily. Set up your store in minutes — it's free!</p>
                @auth
                    <a href="{{ route('vendor.apply') }}" class="btn btn-primary btn-lg">
                @else
                    <a href="{{ route('vendor.register') }}" class="btn btn-primary btn-lg">
                @endauth
                    <i class="fas fa-rocket"></i> Become a Vendor
                </a>
            </div>
        </div>
    </div>

    <!-- Empty State -->
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

    <style>
        /* Page-specific: Homepage hero */
        .hero-section {
            position: relative;
            border-radius: 28px;
            color: white;
            margin: 24px 0 48px;
            overflow: hidden;
            min-height: 520px;
            display: flex;
            flex-direction: column;
            padding: 0;
            text-align: left;
        }

        .hero-image-bg {
            position: absolute;
            inset: 0;
            z-index: 0;
        }

        .hero-image-bg::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(15,23,42,0.94) 0%, rgba(30,58,95,0.80) 45%, rgba(30,64,175,0.70) 100%);
            z-index: 1;
        }

        .hero-bg-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center 35%;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 48px;
            align-items: center;
            flex: 1;
            position: relative;
            z-index: 2;
            padding: 72px 56px 32px;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(59,130,246,0.20);
            border: 1px solid rgba(59,130,246,0.30);
            padding: 8px 18px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #93c5fd;
        }

        .hero-badge i { color: #fbbf24; }

        .hero-title {
            font-family: var(--font-display);
            font-size: clamp(2.4rem, 5vw, 3.75rem);
            font-weight: 800;
            margin-bottom: 16px;
            line-height: 1.08;
            letter-spacing: -0.03em;
            background: linear-gradient(to bottom, #ffffff 30%, #cbd5e1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: titleFade 0.8s ease-out forwards;
        }

        @keyframes titleFade {
            from { opacity: 0; transform: translateY(24px); filter: blur(12px); }
            to { opacity: 1; transform: translateY(0); filter: blur(0); }
        }

        .text-highlight {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: inline-block;
        }

        .hero-subtitle {
            font-size: clamp(1rem, 1.8vw, 1.15rem);
            opacity: 0.80;
            margin-bottom: 32px;
            line-height: 1.7;
            max-width: 480px;
        }

        .hero-actions {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        .hero-actions .btn-primary {
            box-shadow: 0 8px 28px rgba(59,130,246,0.45);
        }

        .hero-links {
            display: flex;
            gap: 24px;
            margin-top: 24px;
        }

        .hero-links a {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: rgba(255,255,255,0.65);
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
        }

        .hero-links a:hover { color: white; }
        .hero-links a i { font-size: 12px; }

        /* Stats Bar */
        .hero-stats-bar {
            display: flex;
            align-items: center;
            gap: 36px;
            position: relative;
            z-index: 2;
            padding: 16px 56px 32px;
        }

        .hero-stat strong {
            display: block;
            font-size: 24px;
            font-weight: 800;
            color: white;
            letter-spacing: -0.02em;
        }

        .hero-stat span {
            font-size: 12px;
            color: rgba(255,255,255,0.50);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-weight: 600;
        }

        .hero-stat-divider {
            width: 1px;
            height: 36px;
            background: rgba(255,255,255,0.12);
        }

        /* Visuals column */
        .hero-visuals {
            position: relative;
            height: 380px;
        }

        .hero-person {
            border-radius: 20px;
            object-fit: cover;
            border: 3px solid rgba(255,255,255,0.20);
            box-shadow: 0 20px 60px rgba(0,0,0,0.35);
            position: absolute;
        }

        .hero-person-1 {
            width: 210px;
            height: 280px;
            left: 5%;
            top: 10px;
            z-index: 2;
            animation: personFloat 6s ease-in-out infinite;
        }

        .hero-person-2 {
            width: 180px;
            height: 240px;
            right: 8%;
            bottom: 0;
            z-index: 1;
            animation: personFloat 7s ease-in-out infinite reverse;
        }

        @keyframes personFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }

        .hero-floating-card {
            position: absolute;
            background: rgba(255,255,255,0.10);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.18);
            border-radius: 14px;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            z-index: 3;
            animation: cardBounce 5s ease-in-out infinite;
            box-shadow: 0 8px 32px rgba(0,0,0,0.20);
        }

        .hero-floating-card i { font-size: 16px; color: #93c5fd; }

        .card-1 { top: 0; right: -5px; animation-delay: 0s; }
        .card-2 { top: 50%; left: -25px; animation-delay: 1.5s; }
        .card-3 { bottom: 15px; right: 15px; animation-delay: 3s; }

        @keyframes cardBounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        @media (max-width: 900px) {
            .hero-grid {
                grid-template-columns: 1fr;
                gap: 0;
                padding: 48px 28px 20px;
                text-align: center;
            }
            .hero-subtitle { max-width: 100%; margin-left: auto; margin-right: auto; }
            .hero-actions { justify-content: center; }
            .hero-actions .btn { width: 100%; }
            .hero-links { justify-content: center; flex-wrap: wrap; gap: 16px; }
            .hero-visuals { display: none; }
            .hero-stats-bar { justify-content: center; padding: 8px 28px 28px; gap: 24px; }
        }

        @media (max-width: 480px) {
            .hero-section { border-radius: 20px; margin: 16px 0 32px; min-height: auto; }
            .hero-grid { padding: 32px 20px 16px; }
            .hero-stats-bar { gap: 16px; flex-wrap: wrap; justify-content: center; }
            .hero-stat strong { font-size: 20px; }
        }

        /* ===== Top Stores ===== */
        .stores-grid {
            display: flex;
            gap: 12px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 8px;
            scrollbar-width: none;
        }
        .stores-grid::-webkit-scrollbar { display: none; }

        .store-card {
            flex-shrink: 0;
            width: 160px;
            background: white;
            border-radius: 16px;
            padding: 16px;
            text-align: center;
            border: 1px solid var(--secondary-100, #f1f5f9);
            transition: all 0.2s;
            text-decoration: none;
            color: inherit;
        }
        .store-card:hover {
            border-color: var(--primary-200, #bfdbfe);
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
            transform: translateY(-4px);
        }

        .store-logo {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 10px;
            display: block;
            border: 2px solid var(--secondary-100, #f1f5f9);
        }

        .store-info h4 {
            font-size: 13px;
            font-weight: 700;
            color: var(--secondary-900, #0f172a);
            margin: 0 0 6px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .store-meta {
            display: flex;
            flex-direction: column;
            gap: 2px;
            align-items: center;
        }

        .store-rating {
            font-size: 11px;
            font-weight: 700;
            color: #f59e0b;
        }
        .store-rating i { font-size: 10px; }

        .store-products {
            font-size: 10px;
            color: var(--secondary-400, #94a3b8);
            font-weight: 500;
        }

        @media (min-width: 769px) {
            .stores-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
                overflow-x: visible;
            }
            .store-card { width: auto; }
        }

        /* ===== Vendor CTA ===== */
        .vendor-cta {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #1e40af 100%);
            border-radius: 24px;
            padding: 48px 24px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .vendor-cta::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 30% 70%, rgba(59,130,246,0.15) 0%, transparent 50%);
        }
        .vendor-cta-content {
            position: relative;
            z-index: 2;
        }
        .vendor-cta-icon {
            width: 60px;
            height: 60px;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 24px;
            color: #93c5fd;
        }
        .vendor-cta h2 {
            color: white;
            font-size: clamp(1.25rem, 4vw, 1.75rem);
            font-weight: 800;
            margin-bottom: 8px;
        }
        .vendor-cta p {
            color: rgba(255,255,255,0.7);
            font-size: 14px;
            margin-bottom: 24px;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
        }

        @media (max-width: 480px) {
            .vendor-cta { padding: 36px 20px; border-radius: 20px; }
            .store-card { width: 130px; padding: 12px; }
            .store-logo { width: 44px; height: 44px; }
            .store-info h4 { font-size: 12px; }
        }
    </style>
@endsection
