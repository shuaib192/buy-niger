{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    View: Shop Catalog
--}}
@extends('layouts.shop')

@section('title', 'Shop All Products')

@section('content')
    <div class="container py-5">
        <div class="shop-grid">
            <!-- Sidebar Filters -->
            <aside class="shop-sidebar">
                <div class="sidebar-block">
                    <h4>Categories</h4>
                    <ul class="filter-list">
                        <li><a href="{{ route('catalog') }}" class="{{ !request('category') ? 'active' : '' }}">All Categories</a></li>
                        @foreach($categories as $category)
                            <li>
                                <a href="{{ route('category', $category->slug) }}" class="{{ request('category') == $category->slug ? 'active' : '' }}">
                                    {{ $category->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="sidebar-block">
                    <h4>Price Range</h4>
                    <form action="{{ route('catalog') }}" method="GET">
                        @foreach(request()->except(['min_price', 'max_price', 'page']) as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        
                        <div class="price-inputs">
                            <div class="input-with-label">
                                <span>₦</span>
                                <input type="number" name="min_price" placeholder="Min" value="{{ request('min_price') }}">
                            </div>
                            <div class="input-with-label">
                                <span>₦</span>
                                <input type="number" name="max_price" placeholder="Max" value="{{ request('max_price') }}">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm btn-full mt-3">Apply Price</button>
                    </form>
                </div>

                <div class="sidebar-block">
                    <h4>Customer Rating</h4>
                    <ul class="filter-list">
                        @for($i = 4; $i >= 1; $i--)
                            <li>
                                <a href="{{ request()->fullUrlWithQuery(['rating' => $i, 'page' => null]) }}" class="{{ request('rating') == $i ? 'active' : '' }}">
                                    <div class="rating-filter-item">
                                        <div class="stars">
                                            @for($j = 1; $j <= 5; $j++)
                                                <i class="fas fa-star {{ $j <= $i ? 'text-warning' : 'text-gray' }}"></i>
                                            @endfor
                                        </div>
                                        <span>& Up</span>
                                    </div>
                                </a>
                            </li>
                        @endfor
                    </ul>
                </div>

                @if(request()->anyFilled(['category', 'search', 'min_price', 'max_price', 'rating']))
                    <a href="{{ route('catalog') }}" class="btn btn-secondary btn-sm btn-full mb-4">
                        <i class="fas fa-times mr-1"></i> Clear All Filters
                    </a>
                @endif
            </aside>

            <!-- Product Listing -->
            <div class="shop-main">
                <div class="shop-toolbar">
                    <div class="toolbar-left">
                        <p>Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} results</p>
                    </div>
                    <div class="toolbar-right">
                        <form action="{{ route('catalog') }}" method="GET" id="sortForm">
                            @foreach(request()->except('sort') as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <select name="sort" onchange="document.getElementById('sortForm').submit()">
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest Arrivals</option>
                                <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                                <option value="avg_rating" {{ request('sort') == 'avg_rating' ? 'selected' : '' }}>Average Rating</option>
                            </select>
                        </form>
                    </div>
                </div>

                <div class="product-grid">
                    @forelse($products as $product)
                        @include('shop.partials.product-card', ['product' => $product])
                    @empty
                        <div class="empty-state">
                            <i class="fas fa-search"></i>
                            <h3>No products found</h3>
                            <p>Try adjusting your filters or search terms.</p>
                            <a href="{{ route('catalog') }}" class="btn btn-primary">Clear All Filters</a>
                        </div>
                    @endforelse
                </div>

                <div class="pagination-wrapper">
                    {{ $products->appends(request()->all())->links() }}
                </div>
            </div>
        </div>
    </div>

    <style>
        .shop-grid {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: var(--spacing-2xl);
        }

        .sidebar-block {
            background: white;
            padding: var(--spacing-lg);
            border-radius: var(--radius-xl);
            margin-bottom: var(--spacing-lg);
            border: 1px solid var(--secondary-100);
        }

        .sidebar-block h4 {
            font-weight: 700;
            margin-bottom: var(--spacing-md);
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--secondary-100);
        }

        .filter-list li {
            margin-bottom: 0.5rem;
        }

        .filter-list a {
            color: var(--secondary-600);
            font-size: 0.9375rem;
            transition: color 0.2s;
        }

        .filter-list a:hover, .filter-list a.active {
            color: var(--primary-600);
            font-weight: 600;
        }

        .price-inputs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem;
        }

        .input-with-label {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-with-label span {
            position: absolute;
            left: 10px;
            color: var(--secondary-400);
            font-size: 0.875rem;
        }

        .input-with-label input {
            width: 100%;
            padding: 0.5rem 0.5rem 0.5rem 1.5rem;
            border: 1px solid var(--secondary-200);
            border-radius: var(--radius-md);
            outline: none;
            font-size: 0.875rem;
        }

        .rating-filter-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .rating-filter-item .stars {
            font-size: 0.875rem;
        }

        .rating-filter-item span {
            font-size: 0.8125rem;
            color: var(--secondary-500);
        }

        .text-warning { color: #fbbf24; }
        .text-gray { color: var(--secondary-200); }

        .shop-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--spacing-xl);
            background: white;
            padding: var(--spacing-md) var(--spacing-lg);
            border-radius: var(--radius-xl);
            border: 1px solid var(--secondary-100);
        }

        .toolbar-left p {
            color: var(--secondary-500);
            font-size: 0.875rem;
        }

        .shop-toolbar select {
            padding: 0.5rem 1rem;
            border: 1px solid var(--secondary-200);
            border-radius: var(--radius-md);
            outline: none;
            cursor: pointer;
        }

        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 80px 0;
            background: white;
            border-radius: var(--radius-2xl);
            border: 1px dashed var(--secondary-300);
        }

        .empty-state i {
            font-size: 3rem;
            color: var(--secondary-200);
            margin-bottom: var(--spacing-md);
        }

        .py-5 { padding: var(--spacing-2xl) 0; }

        @media (max-width: 1024px) {
            .shop-grid {
                grid-template-columns: 1fr;
            }
            .shop-sidebar {
                display: none; /* Hide sidebar on small screens for now or make it a drawer */
            }
        }
    </style>
@endsection
