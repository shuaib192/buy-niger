{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    View: All Stores Listing
--}}
@extends('layouts.shop')

@section('title', 'Vendor Stores')

@section('content')
<div class="container" style="padding-top:32px; padding-bottom:60px;">
    
    <!-- Page Header -->
    <div class="stores-hero">
        <h1>Discover Stores</h1>
        <p>Browse trusted vendors and find amazing products from Nigeria's best sellers.</p>
        <form action="{{ route('stores') }}" method="GET" class="stores-search">
            <i class="fas fa-search"></i>
            <input type="text" name="q" placeholder="Search stores by name, location..." value="{{ request('q') }}">
            <button type="submit">Search</button>
        </form>
    </div>

    <!-- Stores Grid -->
    @if($stores->count() > 0)
    <div class="stores-grid">
        @foreach($stores as $store)
        <a href="{{ route('store.show', $store->store_slug) }}" class="store-card">
            <div class="store-card-banner" style="background: linear-gradient(135deg, {{ ['#3b82f6,#1d4ed8','#10b981,#059669','#8b5cf6,#6d28d9','#f59e0b,#d97706','#ef4444,#dc2626','#06b6d4,#0891b2'][array_rand(['#3b82f6,#1d4ed8','#10b981,#059669','#8b5cf6,#6d28d9','#f59e0b,#d97706','#ef4444,#dc2626','#06b6d4,#0891b2'])] }});">
                <div class="store-avatar">
                    @if($store->store_logo)
                        <img src="{{ asset('storage/' . $store->store_logo) }}" alt="{{ $store->store_name }}">
                    @else
                        <span>{{ strtoupper(substr($store->store_name, 0, 2)) }}</span>
                    @endif
                </div>
            </div>
            <div class="store-card-body">
                <h3>{{ $store->store_name }}</h3>
                @if($store->city || $store->state)
                    <p class="store-location"><i class="fas fa-map-marker-alt"></i> {{ $store->city }}{{ $store->state ? ', ' . $store->state : '' }}</p>
                @endif
                <p class="store-desc">{{ Str::limit($store->store_description, 80) ?: 'Quality products at great prices.' }}</p>
                <div class="store-meta">
                    <span><i class="fas fa-box"></i> {{ $store->products_count ?? 'N/A' }} Products</span>
                    <span class="store-visit">Visit Store <i class="fas fa-arrow-right"></i></span>
                </div>
            </div>
        </a>
        @endforeach
    </div>

    <div style="margin-top:32px;">
        {{ $stores->links() }}
    </div>
    @else
    <div style="text-align:center; padding:80px 20px; background:white; border-radius:24px; margin-top:24px;">
        <i class="fas fa-store" style="font-size:4rem; color:#e2e8f0; margin-bottom:24px;"></i>
        @if(request('q'))
            <h2 style="font-size:1.5rem; font-weight:700; margin-bottom:12px;">No stores found</h2>
            <p style="color:#64748b; margin-bottom:24px;">Try a different search term.</p>
            <a href="{{ route('stores') }}" class="btn btn-primary">View All Stores</a>
        @else
            <h2 style="font-size:1.5rem; font-weight:700; margin-bottom:12px;">Be the First Vendor!</h2>
            <p style="color:#64748b; margin-bottom:24px;">Start selling on BuyNiger today.</p>
            <a href="{{ route('register', ['role' => 3]) }}" class="btn btn-primary">Become a Vendor</a>
        @endif
    </div>
    @endif
</div>

<style>
    .stores-hero {
        text-align:center; padding:48px 24px; margin-bottom:36px;
        background:linear-gradient(135deg,#0f172a,#1e40af); border-radius:24px; color:white;
    }
    .stores-hero h1 { font-size:2.2rem; font-weight:800; margin-bottom:8px; }
    .stores-hero p { color:rgba(255,255,255,0.7); font-size:15px; margin-bottom:28px; }
    .stores-search {
        display:flex; max-width:480px; margin:0 auto; background:rgba(255,255,255,0.12);
        border:1px solid rgba(255,255,255,0.2); border-radius:14px; overflow:hidden; backdrop-filter:blur(8px);
    }
    .stores-search i { padding:14px 0 14px 16px; color:rgba(255,255,255,0.5); }
    .stores-search input {
        flex:1; padding:14px 12px; background:transparent; border:none; outline:none;
        color:white; font-size:14px;
    }
    .stores-search input::placeholder { color:rgba(255,255,255,0.4); }
    .stores-search button {
        padding:14px 24px; background:#3b82f6; color:white; border:none;
        font-weight:700; font-size:14px; cursor:pointer;
    }

    .stores-grid {
        display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr));
        gap:24px;
    }
    .store-card {
        background:white; border-radius:20px; overflow:hidden; text-decoration:none; color:#1e293b;
        border:1px solid #e2e8f0; transition:all 0.3s; display:block;
    }
    .store-card:hover { transform:translateY(-4px); box-shadow:0 12px 40px rgba(0,0,0,0.1); }

    .store-card-banner {
        height:100px; position:relative; display:flex; align-items:flex-end; justify-content:center;
    }
    .store-avatar {
        width:60px; height:60px; border-radius:16px; background:white; border:3px solid white;
        display:flex; align-items:center; justify-content:center; position:absolute; bottom:-28px;
        box-shadow:0 4px 12px rgba(0,0,0,0.1); overflow:hidden;
    }
    .store-avatar img { width:100%; height:100%; object-fit:cover; }
    .store-avatar span { font-size:18px; font-weight:800; color:#3b82f6; }

    .store-card-body { padding:38px 20px 20px; }
    .store-card-body h3 { font-size:16px; font-weight:700; margin-bottom:4px; }
    .store-location { color:#64748b; font-size:12px; margin-bottom:8px; }
    .store-location i { color:#3b82f6; margin-right:4px; }
    .store-desc { color:#94a3b8; font-size:13px; line-height:1.5; margin-bottom:16px; }

    .store-meta {
        display:flex; justify-content:space-between; align-items:center;
        border-top:1px solid #f1f5f9; padding-top:12px; font-size:12px;
    }
    .store-meta span { color:#64748b; }
    .store-meta i { margin-right:4px; }
    .store-visit { color:#3b82f6; font-weight:700; }

    @media(max-width:640px) {
        .stores-grid { grid-template-columns:1fr; }
        .stores-hero h1 { font-size:1.6rem; }
    }
</style>
@endsection
