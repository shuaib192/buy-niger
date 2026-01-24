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
                <div class="product-image" style="display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #f1f5f9, #e2e8f0);">
                    <i class="fas fa-image" style="font-size: 3rem; color: #cbd5e1;"></i>
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
                <span class="product-price">₦{{ number_format($product->sale_price) }}</span>
                <span class="product-old-price">₦{{ number_format($product->price) }}</span>
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
            <button class="wishlist-btn" onclick="addToWishlist({{ $product->id }})" title="Add to Wishlist" style="border:none; background:none; color: var(--secondary-400); margin-left:8px;">
                <i class="far fa-heart"></i>
            </button>
        </div>
    </div>
</div>
