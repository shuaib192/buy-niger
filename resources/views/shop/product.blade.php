{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    View: Product Detail
--}}
@extends('layouts.shop')

@section('title', $product->name)

@section('content')
    <div class="container py-5">
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

                <div class="p-price-block">
                    @if($product->sale_price && $product->sale_price < $product->price)
                        <span class="curr-price">₦{{ number_format($product->sale_price) }}</span>
                        <span class="old-price">₦{{ number_format($product->price) }}</span>
                        <span class="save-badge">Save {{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%</span>
                    @else
                        <span class="curr-price">₦{{ number_format($product->price) }}</span>
                    @endif
                </div>

                <div class="p-short-desc">
                    {{ $product->short_description }}
                </div>

                <div class="p-actions mt-5">
                    <div class="quantity-picker">
                        <button type="button" onclick="decrementQty()"><i class="fas fa-minus"></i></button>
                        <input type="number" id="qty" value="1" min="1" max="{{ $product->quantity }}">
                        <button type="button" onclick="incrementQty()"><i class="fas fa-plus"></i></button>
                    </div>
                    <button type="button" class="btn btn-primary btn-lg flex-1" id="addToCartBtn" data-product-id="{{ $product->id }}">
                        <i class="fas fa-shopping-cart mr-2"></i> Add to Cart
                    </button>
                </div>

                <div class="p-trust mt-5">
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

        // Handle tab switching via URL
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab');
            if (tab === 'reviews') {
    </script>

    <style>
        .product-detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
        }

        .main-image-container {
            background: white;
            border-radius: var(--radius-2xl);
            overflow: hidden;
            border: 1px solid var(--secondary-100);
            aspect-ratio: 1/1;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: var(--spacing-lg);
        }

        .main-image-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .thumb-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 12px;
        }

        .thumb-item {
            aspect-ratio: 1/1;
            border-radius: var(--radius-lg);
            overflow: hidden;
            border: 2px solid #f1f5f9;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
            transition: all 0.2s;
        }

        .thumb-item:hover {
            border-color: var(--secondary-200);
        }

        .thumb-item.active {
            border-color: var(--primary-500);
            background: white;
        }

        .thumb-item img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .thumb-item.active {
            border-color: var(--primary-500);
        }

        .thumb-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: var(--secondary-500);
            margin-bottom: var(--spacing-lg);
        }

        .breadcrumb a:hover { color: var(--primary-600); }

        .p-name {
            font-family: var(--font-display);
            font-size: 2.5rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: var(--spacing-md);
        }

        .p-meta {
            display: flex;
            gap: var(--spacing-xl);
            margin-bottom: var(--spacing-xl);
            padding-bottom: var(--spacing-md);
            border-bottom: 1px solid var(--secondary-100);
        }

        .text-warning { color: #fbbf24; }
        .text-gray { color: var(--secondary-200); }

        .p-price-block {
            margin-bottom: var(--spacing-xl);
            display: flex;
            align-items: baseline;
            gap: 1rem;
        }

        .curr-price {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary-600);
        }

        .old-price {
            font-size: 1.25rem;
            color: var(--secondary-400);
            text-decoration: line-through;
        }

        .save-badge {
            background: var(--danger-light);
            color: var(--danger);
            padding: 0.25rem 0.75rem;
            border-radius: var(--radius-full);
            font-size: 0.8125rem;
            font-weight: 700;
        }

        .p-short-desc {
            font-size: 1rem;
            line-height: 1.6;
            color: var(--secondary-600);
        }

        .p-actions {
            display: flex;
            gap: var(--spacing-lg);
        }

        .quantity-picker {
            display: flex;
            border: 2px solid var(--secondary-200);
            border-radius: var(--radius-lg);
            overflow: hidden;
        }

        .quantity-picker button {
            background: white;
            border: none;
            width: 44px;
            cursor: pointer;
        }

        .quantity-picker input {
            width: 60px;
            border: none;
            text-align: center;
            font-weight: 700;
            outline: none;
        }

        .vendor-card {
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
            padding: var(--spacing-lg);
            background: var(--secondary-50);
            border-radius: var(--radius-xl);
        }

        .vendor-card img {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-full);
            object-fit: cover;
        }

        .vendor-info { flex: 1; }
        .vendor-info span { display: block; font-size: 0.75rem; color: var(--secondary-500); }
        .vendor-info a { font-weight: 700; color: var(--secondary-900); }

        .tab-header {
            display: flex;
            gap: var(--spacing-xl);
            border-bottom: 2px solid var(--secondary-100);
            margin-bottom: var(--spacing-xl);
        }

        .tab-btn {
            background: none;
            border: none;
            padding: 1rem 0;
            font-weight: 700;
            color: var(--secondary-500);
            cursor: pointer;
            position: relative;
        }

        .tab-btn.active {
            color: var(--primary-600);
        }

        .tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--primary-600);
        }

        .rich-text { line-height: 1.8; color: var(--secondary-600); }

        /* Review Styles */
        .reviews-container {
            padding: var(--spacing-lg) 0;
        }
        .reviews-overview {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 40px;
            padding-bottom: 40px;
            border-bottom: 1px solid var(--secondary-100);
        }
        .avg-rating-box {
            text-align: center;
            padding: 30px;
            background: var(--secondary-50);
            border-radius: 20px;
        }
        .big-rating {
            font-size: 4rem;
            font-weight: 800;
            color: var(--secondary-900);
            line-height: 1;
        }
        .avg-rating-box .stars {
            margin: 15px 0 5px;
            font-size: 1.25rem;
        }
        .avg-rating-box .count {
            color: var(--secondary-500);
            font-size: 0.875rem;
        }
        .review-form-box h3 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
            gap: 5px;
        }
        .star-rating input { display: none; }
        .star-rating label {
            font-size: 1.5rem;
            color: var(--secondary-200);
            cursor: pointer;
            transition: color 0.2s;
        }
        .star-rating label:hover,
        .star-rating label:hover ~ label,
        .star-rating input:checked ~ label {
            color: #fbbf24;
        }
        .review-item {
            padding: 30px 0;
            border-bottom: 1px solid var(--secondary-100);
        }
        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        .user-meta strong {
            display: block;
            font-size: 1.1rem;
        }
        .user-meta .date {
            font-size: 0.875rem;
            color: var(--secondary-400);
        }
        .review-comment {
            font-size: 1rem;
            line-height: 1.6;
            color: var(--secondary-700);
        }
        .empty-reviews {
            text-align: center;
            padding: 60px 0;
            color: var(--secondary-400);
        }
        .empty-reviews i {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        .login-prompt {
            padding: 30px;
            background: var(--primary-50);
            border-radius: 15px;
            text-align: center;
            border: 1px dashed var(--primary-200);
        }

        @media (max-width: 768px) {
            .product-detail-grid { grid-template-columns: 1fr; gap: 30px; }
            .p-name { font-size: 1.75rem; }
            .reviews-overview { grid-template-columns: 1fr; gap: 30px; }
        }
    </style>
@endsection
