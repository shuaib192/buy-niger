{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    View: About Us
--}}
@extends('layouts.shop')

@section('title', 'About BuyNiger')

@section('content')
<div class="container" style="padding-top:32px; padding-bottom:80px;">

    <!-- Hero Banner -->
    <div class="about-hero">
        <span class="about-badge"><i class="fas fa-heart"></i> Our Story</span>
        <h1>We're Building Nigeria's<br>Smartest Marketplace</h1>
        <p>BuyNiger connects vendors and buyers across Nigeria through an AI-powered, modern e-commerce platform — making buying and selling seamless, secure, and accessible to everyone.</p>
    </div>

    <!-- Stats -->
    <div class="about-stats">
        <div class="about-stat-card">
            <div class="stat-icon"><i class="fas fa-store"></i></div>
            <strong>{{ number_format($vendorCount) }}+</strong>
            <span>Trusted Vendors</span>
        </div>
        <div class="about-stat-card">
            <div class="stat-icon"><i class="fas fa-box-open"></i></div>
            <strong>{{ number_format($productCount) }}+</strong>
            <span>Products Listed</span>
        </div>
        <div class="about-stat-card">
            <div class="stat-icon"><i class="fas fa-shopping-cart"></i></div>
            <strong>{{ number_format($orderCount) }}+</strong>
            <span>Orders Placed</span>
        </div>
        <div class="about-stat-card">
            <div class="stat-icon"><i class="fas fa-headset"></i></div>
            <strong>24/7</strong>
            <span>Customer Support</span>
        </div>
    </div>

    <!-- Mission & Vision -->
    <div class="about-grid">
        <div class="about-card">
            <div class="about-card-icon" style="background:linear-gradient(135deg,#3b82f6,#1d4ed8);">
                <i class="fas fa-bullseye"></i>
            </div>
            <h3>Our Mission</h3>
            <p>To empower Nigerian businesses and entrepreneurs by providing a world-class digital marketplace that makes e-commerce simple, accessible, and rewarding for vendors and shoppers alike.</p>
        </div>
        <div class="about-card">
            <div class="about-card-icon" style="background:linear-gradient(135deg,#10b981,#059669);">
                <i class="fas fa-eye"></i>
            </div>
            <h3>Our Vision</h3>
            <p>To become Nigeria's most trusted online marketplace — where every vendor can grow their business and every shopper can find exactly what they need, with confidence and ease.</p>
        </div>
        <div class="about-card">
            <div class="about-card-icon" style="background:linear-gradient(135deg,#f59e0b,#d97706);">
                <i class="fas fa-gem"></i>
            </div>
            <h3>Our Values</h3>
            <p>Trust, innovation, and community. We believe in fair business, cutting-edge technology, and lifting every vendor — no matter how small — to reach their full potential.</p>
        </div>
    </div>

    <!-- Why BuyNiger -->
    <div class="about-features">
        <h2 class="section-title" style="text-align:center; margin-bottom:40px;">Why Choose BuyNiger?</h2>
        <div class="about-features-grid">
            <div class="feature-item">
                <i class="fas fa-robot"></i>
                <h4>AI-Powered</h4>
                <p>Smart recommendations, intelligent search, and AI-driven insights to help vendors sell more.</p>
            </div>
            <div class="feature-item">
                <i class="fas fa-shield-alt"></i>
                <h4>Secure Payments</h4>
                <p>Industry-standard encryption and trusted payment gateways protect every transaction.</p>
            </div>
            <div class="feature-item">
                <i class="fas fa-truck"></i>
                <h4>Flexible Delivery</h4>
                <p>Pickup or vendor shipping — choose what works best for you, with transparent pricing.</p>
            </div>
            <div class="feature-item">
                <i class="fas fa-handshake"></i>
                <h4>Vendor-First</h4>
                <p>Fair commission rates, fast payouts, and tools that help vendors manage their stores with ease.</p>
            </div>
            <div class="feature-item">
                <i class="fas fa-tags"></i>
                <h4>Best Prices</h4>
                <p>Direct from vendors means no middlemen — you get the best prices on quality Nigerian products.</p>
            </div>
            <div class="feature-item">
                <i class="fas fa-comments"></i>
                <h4>Direct Messaging</h4>
                <p>Chat directly with vendors to ask questions, negotiate, or get product details before buying.</p>
            </div>
        </div>
    </div>

    <!-- CTA -->
    <div class="about-cta">
        <h2>Ready to Start?</h2>
        <p>Join thousands of Nigerians already shopping smarter on BuyNiger.</p>
        <div class="about-cta-actions">
            <a href="{{ route('catalog') }}" class="btn btn-primary btn-lg"><i class="fas fa-shopping-bag"></i> Start Shopping</a>
            <a href="{{ route('register', ['role' => 3]) }}" class="btn btn-outline-dark btn-lg"><i class="fas fa-store"></i> Become a Vendor</a>
        </div>
    </div>
</div>

<style>
    .about-hero {
        text-align: center;
        padding: 64px 20px;
        background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #1e40af 100%);
        border-radius: 28px;
        color: white;
        margin-bottom: 48px;
    }
    .about-badge {
        display: inline-flex; align-items: center; gap: 8px;
        background: rgba(59,130,246,0.2); border: 1px solid rgba(59,130,246,0.3);
        padding: 8px 18px; border-radius: 50px; font-size: 13px; font-weight: 600;
        margin-bottom: 20px; color: #93c5fd;
    }
    .about-badge i { color: #f87171; }
    .about-hero h1 {
        font-size: clamp(2rem, 5vw, 3rem); font-weight: 800; margin-bottom: 16px;
        line-height: 1.1; letter-spacing: -0.025em;
    }
    .about-hero p {
        font-size: 1.1rem; opacity: 0.8; max-width: 640px; margin: 0 auto; line-height: 1.7;
    }

    .about-stats {
        display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 56px;
    }
    .about-stat-card {
        background: white; border-radius: 16px; padding: 28px 20px; text-align: center;
        border: 1px solid #e2e8f0; transition: transform 0.3s, box-shadow 0.3s;
    }
    .about-stat-card:hover { transform: translateY(-6px); box-shadow: 0 16px 40px rgba(0,0,0,0.08); }
    .stat-icon {
        width: 48px; height: 48px; background: linear-gradient(135deg, #eff6ff, #dbeafe);
        border-radius: 14px; display: flex; align-items: center; justify-content: center;
        color: #3b82f6; font-size: 18px; margin: 0 auto 14px;
    }
    .about-stat-card strong { display: block; font-size: 28px; font-weight: 800; color: #1e293b; }
    .about-stat-card span { font-size: 13px; color: #64748b; }

    .about-grid {
        display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 56px;
    }
    .about-card {
        background: white; border-radius: 20px; padding: 32px; border: 1px solid #e2e8f0;
        transition: transform 0.3s; text-align: center;
    }
    .about-card:hover { transform: translateY(-6px); }
    .about-card-icon {
        width: 56px; height: 56px; border-radius: 16px; display: flex;
        align-items: center; justify-content: center; color: white; font-size: 22px;
        margin: 0 auto 20px;
    }
    .about-card h3 { font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 10px; }
    .about-card p { font-size: 14px; color: #64748b; line-height: 1.7; }

    .about-features-grid {
        display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px;
    }
    .feature-item {
        background: white; border-radius: 16px; padding: 28px; text-align: center;
        border: 1px solid #e2e8f0; transition: all 0.3s;
    }
    .feature-item:hover { border-color: #3b82f6; transform: translateY(-4px); }
    .feature-item i { font-size: 28px; color: #3b82f6; margin-bottom: 14px; display: block; }
    .feature-item h4 { font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 8px; }
    .feature-item p { font-size: 13px; color: #64748b; line-height: 1.6; }

    .about-cta {
        text-align: center; background: linear-gradient(135deg, #f8fafc, #eff6ff);
        border-radius: 24px; padding: 56px 32px; margin-top: 56px;
        border: 1px solid #dbeafe;
    }
    .about-cta h2 { font-size: 2rem; font-weight: 800; color: #1e293b; margin-bottom: 10px; }
    .about-cta p { color: #64748b; font-size: 1.1rem; margin-bottom: 28px; }
    .about-cta-actions { display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; }

    .btn-outline-dark {
        border: 2px solid #1e293b; color: #1e293b; background: transparent;
        padding: 14px 28px; border-radius: 14px; font-weight: 600;
        transition: all 0.2s;
    }
    .btn-outline-dark:hover { background: #1e293b; color: white; }

    @media (max-width: 768px) {
        .about-stats { grid-template-columns: repeat(2, 1fr); }
        .about-grid { grid-template-columns: 1fr; }
        .about-features-grid { grid-template-columns: 1fr; }
        .about-cta-actions { flex-direction: column; }
        .about-cta-actions .btn { width: 100%; }
    }
</style>
@endsection
