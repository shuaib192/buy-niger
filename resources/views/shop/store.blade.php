{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    View: Vendor Public Storefront
--}}
@extends('layouts.shop')

@section('title', $vendor->store_name)

@section('content')
    <!-- Store Header -->
    <div class="store-header">
        <div class="store-banner" style="background-image: url('{{ $vendor->banner ? Storage::url($vendor->banner) : '' }}');">
            <div class="store-banner-overlay"></div>
        </div>
        <div class="container">
            <div class="store-info-card">
                <div class="store-logo">
                    @if($vendor->logo)
                        <img src="{{ Storage::url($vendor->logo) }}" alt="{{ $vendor->store_name }}">
                    @else
                        <div class="store-logo-placeholder">
                            <i class="fas fa-store"></i>
                        </div>
                    @endif
                </div>
                <div class="store-details">
                    <h1 class="store-name">{{ $vendor->store_name }}</h1>
                    <div class="store-meta">
                        <div class="meta-item">
                            <i class="fas fa-star text-warning"></i>
                            <span>{{ number_format($vendor->rating, 1) }} ({{ $vendor->rating_count }} reviews)</span>
                        </div>
                        <div class="meta-item">
                            
                            <i class="fas fa-box"></i>
                            <span>{{ $products->total() }} Products</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>{{ $vendor->city ?? 'Nigeria' }}, {{ $vendor->state ?? '' }}</span>
                        </div>
                    </div>
                    @if($vendor->store_description)
                        <p class="store-description">{{ $vendor->store_description }}</p>
                    @endif
                </div>
                <div class="store-actions">
                    <form action="{{ route('customer.messages.start', $vendor->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-envelope"></i> Contact Seller
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Store Products -->
    <div class="container py-5">
        <div class="store-content">
            <!-- Sidebar Filters -->
            <aside class="store-sidebar">
                <div class="filter-card">
                    <h3>Categories</h3>
                    <ul class="category-list">
                        <li><a href="{{ route('store.show', $vendor->store_slug) }}" class="{{ !request('category') ? 'active' : '' }}">All Products</a></li>
                        @foreach($categories as $cat)
                            <li><a href="{{ route('store.show', $vendor->store_slug) }}?category={{ $cat->slug }}" class="{{ request('category') == $cat->slug ? 'active' : '' }}">{{ $cat->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </aside>

            <!-- Products Grid -->
            <div class="store-products">
                <div class="products-header">
                    <h2>{{ request('category') ? 'Filtered Products' : 'All Products' }}</h2>
                    <span class="product-count">{{ $products->total() }} items</span>
                </div>

                @if($products->count() > 0)
                    <div class="product-grid">
                        @foreach($products as $product)
                            @include('shop.partials.product-card', ['product' => $product])
                        @endforeach
                    </div>

                    <div class="pagination-wrapper mt-4">
                        {{ $products->links() }}
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-box-open"></i>
                        <h3>No Products Yet</h3>
                        <p>This store hasn't added any products yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .store-header {
            position: relative;
            margin-bottom: 0;
        }

        .store-banner {
            height: 250px;
            background: linear-gradient(135deg, var(--primary-600), var(--primary-800));
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .store-banner-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(0,0,0,0.2), rgba(0,0,0,0.6));
        }

        .store-info-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-top: -80px;
            position: relative;
            z-index: 10;
            display: flex;
            gap: 30px;
            align-items: flex-start;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }

        .store-logo {
            flex-shrink: 0;
        }

        .store-logo img {
            width: 120px;
            height: 120px;
            border-radius: 20px;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .store-logo-placeholder {
            width: 120px;
            height: 120px;
            border-radius: 20px;
            background: var(--primary-100);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: var(--primary-600);
        }

        .store-details {
            flex: 1;
        }

        .store-name {
            font-family: var(--font-display);
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .store-meta {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.9rem;
            color: var(--secondary-600);
        }

        .meta-item i {
            color: var(--secondary-400);
        }

        .store-description {
            color: var(--secondary-500);
            line-height: 1.6;
            max-width: 600px;
        }

        .store-actions {
            flex-shrink: 0;
        }

        .store-content {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 40px;
        }

        .store-sidebar {
            position: sticky;
            top: 100px;
            height: fit-content;
        }

        .filter-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }

        .filter-card h3 {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--secondary-900);
        }

        .category-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .category-list li a {
            display: block;
            padding: 10px 12px;
            border-radius: 8px;
            color: var(--secondary-600);
            transition: all 0.2s;
        }

        .category-list li a:hover,
        .category-list li a.active {
            background: var(--primary-50);
            color: var(--primary-600);
        }

        .products-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .products-header h2 {
            font-size: 1.25rem;
            font-weight: 700;
        }

        .product-count {
            color: var(--secondary-500);
            font-size: 0.9rem;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: var(--secondary-50);
            border-radius: 20px;
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--secondary-200);
            margin-bottom: 20px;
        }

        .empty-state h3 {
            font-size: 1.25rem;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: var(--secondary-500);
        }

        @media (max-width: 768px) {
            .store-info-card {
                flex-direction: column;
                text-align: center;
                align-items: center;
            }

            .store-meta {
                justify-content: center;
                flex-wrap: wrap;
            }

            .store-description {
                text-align: center;
            }

            .store-content {
                grid-template-columns: 1fr;
            }

            .store-sidebar {
                position: static;
            }
        }
    </style>
@endsection
