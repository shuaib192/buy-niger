{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    View: Product Detail
--}}
@extends('layouts.shop')

@section('title', $product->name)

@section('content')
    <div class="container" style="padding-top:0;padding-bottom:40px;">
        <div class="product-detail-grid">
            <!-- Product Images -->
            <div class="product-gallery">
                <div class="main-image-container">
                    <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" id="currentImage">
                </div>
                @if($product->images->count() > 1)
                <div class="thumb-grid">
                    @foreach($product->images as $image)
                        <div class="thumb-item {{ $image->is_primary ? 'active' : '' }}" onclick="updateCurrentImage('{{ Storage::url($image->image_path) }}', this)">
                            <img src="{{ Storage::url($image->image_path) }}" alt="Thumb">
                        </div>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Product Info -->
            <div class="product-info-panel">
                <nav class="breadcrumb">
                    <a href="{{ route('home') }}">Home</a>
                    <i class="fas fa-chevron-right"></i>
                    <a href="{{ route('category', $product->category->slug) }}">{{ $product->category->name }}</a>
                </nav>

                <h1 class="p-name">{{ $product->name }}</h1>
                
                <div class="p-meta">
                    <div class="p-rating">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= $product->rating ? 'text-warning' : 'text-gray' }}"></i>
                        @endfor
                        <span>({{ $product->rating_count }} Reviews)</span>
                    </div>
                    <div class="p-sku">SKU: {{ $product->sku }}</div>
                </div>

                <div class="p-price-block" style="flex-direction:column; align-items:flex-start; gap:8px;">
                    @if($product->sale_price && $product->sale_price < $product->price)
                        <div style="display:flex; align-items:flex-end; gap:16px; flex-wrap:wrap;">
                            <div>
                                <div style="font-size:0.7rem; text-transform:uppercase; letter-spacing:1px; font-weight:800; color:#dc2626;">🔥 Sale — You Pay</div>
                                <span class="curr-price">₦{{ number_format($product->sale_price) }}</span>
                            </div>
                            <div>
                                <div style="font-size:0.7rem; text-transform:uppercase; letter-spacing:1px; font-weight:700; color:var(--secondary-400);">Original Price</div>
                                <span class="old-price" style="text-decoration:line-through; font-size:1.2rem;">₦{{ number_format($product->price) }}</span>
                            </div>
                        </div>
                        <span class="save-badge">You save ₦{{ number_format($product->price - $product->sale_price) }} &mdash; {{ round((($product->price - $product->sale_price) / $product->price) * 100) }}% off</span>
                    @else
                        <div>
                            <div style="font-size:0.7rem; text-transform:uppercase; letter-spacing:1px; font-weight:700; color:var(--secondary-400);">Price</div>
                            <span class="curr-price">₦{{ number_format($product->price) }}</span>
                        </div>
                    @endif
                </div>

                <div class="p-short-desc">
                    {{ $product->short_description }}
                </div>

                @if($product->variants->count() > 0)
                <div class="p-variants mb-4">
                    <label class="d-block mb-2 font-weight-bold" style="font-size: 0.875rem; color: var(--secondary-700);">Select Option</label>
                    <div class="variant-grid" style="display: flex; flex-wrap: wrap; gap: 10px;">
                        @foreach($product->variants as $variant)
                            <div class="variant-item">
                                <input type="radio" name="product_variant_id" id="v-{{ $variant->id }}" value="{{ $variant->id }}" class="variant-input" onchange="updateVariantPrice({{ $variant->price }}, '{{ $variant->name }}')" {{ $loop->first ? 'checked' : '' }}>
                                <label for="v-{{ $variant->id }}" class="variant-label">
                                    {{ $variant->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="p-actions">
                    <div class="quantity-picker">
                        <button type="button" onclick="decrementQty()"><i class="fas fa-minus"></i></button>
                        <input type="number" id="qty" value="1" min="1" max="{{ $product->quantity }}">
                        <button type="button" onclick="incrementQty()"><i class="fas fa-plus"></i></button>
                    </div>
                    <button type="button" class="btn btn-primary btn-lg flex-1" id="addToCartBtn" data-product-id="{{ $product->id }}">
                        <i class="fas fa-shopping-cart mr-2"></i> Add to Cart
                    </button>
                </div>

                <div class="p-trust mt-3">
                    <div class="vendor-card">
                        @if($product->vendor->logo)
                            <img src="{{ Storage::url($product->vendor->logo) }}" alt="{{ $product->vendor->store_name }}">
                        @else
                            <div style="width: 48px; height: 48px; border-radius: 50%; background: var(--primary-100); display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-store" style="color: var(--primary-600);"></i>
                            </div>
                        @endif
                        <div class="vendor-info">
                            <span>Sold by</span>
                            <a href="{{ route('store.show', $product->vendor->store_slug) }}">{{ $product->vendor->store_name }}</a>
                        </div>
                        <a href="{{ route('store.show', $product->vendor->store_slug) }}" class="btn btn-secondary btn-sm">Visit Store</a>
                        @if($product->vendor->business_phone)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $product->vendor->business_phone) }}?text={{ urlencode('Hi! I\'m interested in "' . $product->name . '" listed on BuyNiger. Is it still available?') }}" target="_blank" class="btn btn-sm" style="background:#25d366;color:white;border:none;display:inline-flex;align-items:center;gap:4px;font-weight:600;">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </a>
                        @endif
                        <form action="{{ route('customer.messages.start', $product->vendor->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="subject" value="Inquiry about {{ $product->name }}">
                            <button type="submit" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-comment-dots"></i> Chat
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description & Reviews -->
        <div class="product-tabs mt-5">
            <div class="tab-header">
                <button class="tab-btn active" onclick="showTab('desc', this)">Description</button>
                <button class="tab-btn" onclick="showTab('reviews', this)">Reviews ({{ $product->rating_count }})</button>
            </div>
            <div class="tab-content" id="descTab">
                <div class="rich-text">
                    {!! nl2br(e($product->description)) !!}
                </div>
            </div>
            <div class="tab-content" id="reviewsTab" style="display: none;">
                <div class="reviews-container">
                    <!-- Review Summary -->
                    <div class="reviews-overview">
                        <div class="avg-rating-box">
                            <div class="big-rating">{{ number_format($product->rating, 1) }}</div>
                            <div class="stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $product->rating ? 'text-warning' : 'text-gray' }}"></i>
                                @endfor
                            </div>
                            <div class="count">{{ $product->rating_count }} Reviews</div>
                        </div>
                        
                        <!-- Review Form (Only if authenticated) -->
                        <div class="review-form-box">
                            @auth
                                <h3>Write a Review</h3>
                                <form action="{{ route('customer.reviews.store', $product->id) }}" method="POST">
                                    @csrf
                                    <div class="rating-input">
                                        <label>Your Rating</label>
                                        <div class="star-rating">
                                            @for($i = 5; $i >= 1; $i--)
                                                <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" required>
                                                <label for="star{{ $i }}"><i class="fas fa-star"></i></label>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="form-group mt-3">
                                        <label>Your Feedback</label>
                                        <textarea name="comment" class="form-control" rows="3" placeholder="Share your experience..." required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary mt-3">Submit Review</button>
                                </form>
                            @else
                                <div class="login-prompt">
                                    <p><a href="{{ route('login') }}">Log in</a> to write a review.</p>
                                </div>
                            @endauth
                        </div>
                    </div>

                    <!-- Reviews List -->
                    <div class="reviews-list mt-5">
                        @forelse($product->reviews()->where('is_approved', true)->latest()->get() as $review)
                            <div class="review-item">
                                <div class="review-header">
                                    <div class="user-meta">
                                        <strong>{{ $review->user->name }}</strong>
                                        <span class="date">{{ $review->created_at->format('M d, Y') }}</span>
                                    </div>
                                    <div class="review-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-gray' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                                <div class="review-comment">
                                    {{ $review->comment }}
                                </div>
                            </div>
                        @empty
                            <div class="empty-reviews">
                                <i class="fas fa-comment-dots"></i>
                                <p>No reviews yet. Be the first to share your thoughts!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
            <div class="mt-5">
                <div class="section-header">
                    <h2 class="section-title">Related Products</h2>
                </div>
                <div class="product-grid">
                    @foreach($relatedProducts as $rel)
                        @include('shop.partials.product-card', ['product' => $rel])
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <script>
        function updateCurrentImage(src, thumb) {
            document.getElementById('currentImage').src = src;
            document.querySelectorAll('.thumb-item').forEach(i => i.classList.remove('active'));
            thumb.classList.add('active');
        }

        function incrementQty() {
            const qty = document.getElementById('qty');
            if (parseInt(qty.value) < parseInt(qty.max)) qty.value = parseInt(qty.value) + 1;
        }

        function decrementQty() {
            const qty = document.getElementById('qty');
            if (parseInt(qty.value) > 1) qty.value = parseInt(qty.value) - 1;
        }

        function showTab(id, btn) {
            document.querySelectorAll('.tab-content').forEach(c => c.style.display = 'none');
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.getElementById(id + 'Tab').style.display = 'block';
            if (btn) btn.classList.add('active');
        }

        function updateVariantPrice(price, name) {
            if (price > 0) {
                document.querySelector('.curr-price').textContent = '₦' + price.toLocaleString();
            }
        }

        // Handle tab switching via URL
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab');
            if (tab === 'reviews') {
    </script>

    <style>
        /* =========== PRODUCT DETAIL - MOBILE FIRST =========== */
        .product-detail-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0;
        }

        /* -- Image Gallery -- */
        .product-gallery {
            position: relative;
        }
        .main-image-container {
            background: #fff;
            border-radius: 0;
            overflow: hidden;
            aspect-ratio: 1/1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .main-image-container img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .thumb-grid {
            display: flex;
            gap: 8px;
            padding: 12px 16px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }
        .thumb-grid::-webkit-scrollbar { display: none; }
        .thumb-item {
            min-width: 56px;
            width: 56px;
            height: 56px;
            border-radius: 10px;
            overflow: hidden;
            border: 2px solid #e2e8f0;
            cursor: pointer;
            flex-shrink: 0;
            transition: border-color 0.2s;
        }
        .thumb-item.active { border-color: var(--primary-500); }
        .thumb-item:hover { border-color: var(--primary-300); }
        .thumb-item img { width: 100%; height: 100%; object-fit: cover; }

        /* -- Product Info Panel -- */
        .product-info-panel {
            padding: 20px 16px 0;
        }
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.75rem;
            color: var(--secondary-400);
            margin-bottom: 12px;
        }
        .breadcrumb a { color: var(--secondary-400); }
        .breadcrumb a:hover { color: var(--primary-600); }
        .breadcrumb i { font-size: 0.5rem; }

        .p-name {
            font-family: var(--font-display);
            font-size: 1.25rem;
            font-weight: 800;
            line-height: 1.3;
            margin-bottom: 8px;
            color: var(--secondary-900);
        }

        .p-meta {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.8125rem;
        }
        .p-rating { display: flex; align-items: center; gap: 4px; }
        .p-rating .fas { font-size: 0.75rem; }
        .p-rating span { color: var(--secondary-500); font-size: 0.75rem; }
        .p-sku { color: var(--secondary-400); font-size: 0.75rem; }

        .text-warning { color: #fbbf24; }
        .text-gray { color: #d1d5db; }

        /* -- Price -- */
        .p-price-block {
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .curr-price {
            font-size: 1.75rem;
            font-weight: 900;
            color: var(--primary-600);
            letter-spacing: -0.5px;
        }
        .old-price {
            font-size: 1rem;
            color: var(--secondary-400);
            text-decoration: line-through;
        }
        .save-badge {
            background: #fef2f2;
            color: #dc2626;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        /* -- Description -- */
        .p-short-desc {
            font-size: 0.875rem;
            line-height: 1.6;
            color: var(--secondary-600);
            margin-bottom: 20px;
        }

        /* -- Variants -- */
        .variant-input { display: none; }
        .variant-label {
            display: inline-block;
            padding: 8px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--secondary-600);
            cursor: pointer;
            transition: all 0.2s;
            background: #fff;
        }
        .variant-input:checked + .variant-label {
            border-color: var(--primary-600);
            background: var(--primary-50);
            color: var(--primary-700);
            box-shadow: 0 4px 12px rgba(var(--primary-rgb), 0.1);
        }
        .variant-label:hover { border-color: var(--primary-300); }

        /* -- Actions -- */
        .p-actions {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .quantity-picker {
            display: flex;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            background: #fff;
        }
        .quantity-picker button {
            background: #f8fafc;
            border: none;
            width: 38px;
            cursor: pointer;
            font-size: 0.875rem;
            color: var(--secondary-600);
            transition: background 0.2s;
        }
        .quantity-picker button:active { background: #e2e8f0; }
        .quantity-picker input {
            width: 40px;
            border: none;
            border-left: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
            text-align: center;
            font-weight: 700;
            font-size: 0.875rem;
            outline: none;
        }
        .p-actions .btn-primary {
            flex: 1;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.9375rem;
            padding: 12px 16px;
        }

        /* -- Vendor Card -- */
        .p-trust { margin-bottom: 0; }
        .vendor-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px;
            background: #f8fafc;
            border-radius: 14px;
            border: 1px solid #f1f5f9;
            flex-wrap: wrap;
        }
        .vendor-card img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        .vendor-info { flex: 1; min-width: 80px; }
        .vendor-info span { display: block; font-size: 0.6875rem; color: var(--secondary-400); text-transform: uppercase; letter-spacing: 0.5px; }
        .vendor-info a { font-weight: 700; font-size: 0.875rem; color: var(--secondary-900); }
        .vendor-card .btn-sm,
        .vendor-card .btn-secondary,
        .vendor-card .btn-outline-primary {
            font-size: 0.75rem;
            padding: 6px 12px;
            border-radius: 8px;
            white-space: nowrap;
        }

        /* -- Tabs -- */
        .product-tabs {
            padding: 0 16px;
        }
        .tab-header {
            display: flex;
            gap: 0;
            border-bottom: 2px solid #f1f5f9;
            margin-bottom: 16px;
        }
        .tab-btn {
            background: none;
            border: none;
            padding: 12px 16px;
            font-weight: 700;
            font-size: 0.875rem;
            color: var(--secondary-400);
            cursor: pointer;
            position: relative;
            transition: color 0.2s;
        }
        .tab-btn.active { color: var(--primary-600); }
        .tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--primary-600);
            border-radius: 2px;
        }
        .rich-text {
            font-size: 0.875rem;
            line-height: 1.8;
            color: var(--secondary-600);
        }

        /* -- Reviews -- */
        .reviews-container { padding: 0; }
        .reviews-overview {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #f1f5f9;
        }
        .avg-rating-box {
            text-align: center;
            padding: 20px;
            background: #f8fafc;
            border-radius: 14px;
        }
        .big-rating { font-size: 2.5rem; font-weight: 800; color: var(--secondary-900); line-height: 1; }
        .avg-rating-box .stars { margin: 10px 0 4px; font-size: 1rem; }
        .avg-rating-box .count { color: var(--secondary-500); font-size: 0.8125rem; }
        .review-form-box h3 { font-size: 1.1rem; font-weight: 700; margin-bottom: 16px; }
        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
            gap: 4px;
        }
        .star-rating input { display: none; }
        .star-rating label { font-size: 1.25rem; color: #d1d5db; cursor: pointer; transition: color 0.2s; }
        .star-rating label:hover,
        .star-rating label:hover ~ label,
        .star-rating input:checked ~ label { color: #fbbf24; }
        .review-item { padding: 20px 0; border-bottom: 1px solid #f1f5f9; }
        .review-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px; }
        .user-meta strong { display: block; font-size: 0.9375rem; }
        .user-meta .date { font-size: 0.75rem; color: var(--secondary-400); }
        .review-comment { font-size: 0.875rem; line-height: 1.6; color: var(--secondary-600); }
        .empty-reviews { text-align: center; padding: 40px 0; color: var(--secondary-400); }
        .empty-reviews i { font-size: 2rem; margin-bottom: 10px; display: block; }
        .empty-reviews p { font-size: 0.875rem; }
        .login-prompt {
            padding: 20px;
            background: #f0f9ff;
            border-radius: 12px;
            text-align: center;
            border: 1px dashed #bae6fd;
            font-size: 0.875rem;
        }

        /* -- Related Products -- */
        .product-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 12px !important;
        }
        .section-title { font-size: 1.125rem; }

        /* ======================================================
           TABLET (≥768px)
           ====================================================== */
        @media (min-width: 768px) {
            .product-detail-grid {
                grid-template-columns: 1fr;
                gap: 32px;
            }
            .product-gallery { max-width: 480px; margin: 0 auto; }
            .main-image-container { border-radius: 16px; border: 1px solid #e2e8f0; }
            .product-info-panel { padding: 0; }
            .p-name { font-size: 1.75rem; }
            .curr-price { font-size: 2rem; }
            .product-tabs { padding: 0; }
            .reviews-overview { grid-template-columns: 200px 1fr; gap: 30px; }
            .product-grid { grid-template-columns: repeat(3, 1fr) !important; gap: 16px !important; }
        }

        /* ======================================================
           DESKTOP (≥992px)
           ====================================================== */
        @media (min-width: 992px) {
            .product-detail-grid {
                grid-template-columns: 1fr 1fr;
                gap: 50px;
            }
            .product-gallery { max-width: none; }
            .main-image-container {
                border-radius: 20px;
                margin-bottom: 16px;
            }
            .thumb-grid {
                display: grid;
                grid-template-columns: repeat(5, 1fr);
                gap: 12px;
                padding: 0;
                overflow-x: visible;
            }
            .thumb-item { min-width: auto; width: auto; height: auto; aspect-ratio: 1/1; border-radius: 12px; }
            .product-info-panel { padding: 0; }
            .p-name { font-size: 2.25rem; }
            .curr-price { font-size: 2.25rem; }
            .old-price { font-size: 1.25rem; }
            .p-short-desc { font-size: 1rem; }
            .p-meta { font-size: 0.9375rem; }
            .p-meta .p-rating .fas { font-size: 0.875rem; }
            .p-meta .p-rating span, .p-sku { font-size: 0.875rem; }
            .quantity-picker button { width: 44px; }
            .quantity-picker input { width: 50px; font-size: 1rem; }
            .p-actions .btn-primary { font-size: 1rem; padding: 14px 20px; }
            .vendor-card { padding: 20px; gap: 16px; }
            .vendor-card img { width: 48px; height: 48px; }
            .vendor-card .btn-sm { font-size: 0.8125rem; padding: 8px 14px; }
            .tab-btn { padding: 1rem 0; font-size: 1rem; }
            .tab-header { gap: 2rem; }
            .rich-text { font-size: 1rem; }
            .reviews-overview { grid-template-columns: 250px 1fr; gap: 40px; padding-bottom: 40px; }
            .avg-rating-box { padding: 30px; }
            .big-rating { font-size: 4rem; }
            .avg-rating-box .stars { font-size: 1.25rem; }
            .review-item { padding: 30px 0; }
            .user-meta strong { font-size: 1.1rem; }
            .review-comment { font-size: 1rem; }
            .product-grid { grid-template-columns: repeat(4, 1fr) !important; gap: 20px !important; }
            .section-title { font-size: 1.5rem; }
        }
    </style>
@endsection
