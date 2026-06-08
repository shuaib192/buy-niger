{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    Partial: Product Card - Premium Design
--}}
<div class="product-card">
    <div class="product-image-wrapper">
        <a href="{{ route('product.detail', $product->slug) }}">
            @if($product->primary_image_url)
                <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="product-image">
            @else
                <div class="product-image img-placeholder">
                    <i class="fas fa-image"></i>
                </div>
            @endif
        </a>
        @if($product->sale_price && $product->sale_price < $product->price)
            <span class="product-badge badge-sale">Sale</span>
        @elseif($product->created_at && $product->created_at->gt(now()->subDays(7)))
            <span class="product-badge badge-new">New</span>
        @endif
    </div>
    <div class="product-info">
        <div class="product-cat">{{ $product->category->name ?? 'General' }}</div>
        <a href="{{ route('product.detail', $product->slug) }}" class="product-name">{{ Str::limit($product->name, 40) }}</a>
        <div class="product-price-row">
            @if($product->sale_price && $product->sale_price < $product->price)
                <div class="price-col">
                    <div class="price-row">
                        <span class="product-price">₦{{ number_format($product->sale_price) }}</span>
                        <span class="product-old-price">Was ₦{{ number_format($product->price) }}</span>
                    </div>
                    <span class="save-chip">
                        Save ₦{{ number_format($product->price - $product->sale_price) }}
                    </span>
                </div>
            @else
                <span class="product-price">₦{{ number_format($product->price) }}</span>
            @endif
        </div>
        <div class="product-footer">
            <div class="product-rating">
                <i class="fas fa-star"></i>
                <span>{{ number_format($product->rating ?? 4.5, 1) }}</span>
            </div>
            <button class="add-to-cart-btn" data-product-id="{{ $product->id }}" title="Add to Cart">
                <i class="fas fa-plus"></i>
            </button>
            <button class="wishlist-btn" onclick="addToWishlist({{ $product->id }})" title="Add to Wishlist">
                <i class="far fa-heart"></i>
            </button>
        </div>
    </div>
</div>
