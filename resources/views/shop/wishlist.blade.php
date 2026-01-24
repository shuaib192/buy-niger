@extends('layouts.app')

@section('title', 'My Wishlist')
@section('page_title', 'My Wishlist')

@section('sidebar')
    @include('customer.partials.sidebar')
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h3>My Wishlist</h3>
                    <a href="{{ route('catalog') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-plus"></i> Add Items
                    </a>
                </div>
            </div>
            <div class="dashboard-card-body">
                @if($wishlistItems->count() > 0)
                    <div class="wishlist-grid">
                        @foreach($wishlistItems as $item)
                            @php $product = $item->product; @endphp
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
                                    <button class="remove-btn" onclick="removeFromWishlist(event, {{ $product->id }})" title="Remove">
                                        <i class="fas fa-times"></i>
                                    </button>
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
                                        <button class="add-to-cart-btn" onclick="moveToCart(event, {{ $product->id }})" title="Move to Cart">
                                            <i class="fas fa-cart-plus"></i> Move to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        {{ $wishlistItems->links() }}
                    </div>
                @else
                    <div class="empty-state text-center py-5">
                        <i class="far fa-heart fa-4x text-muted mb-4"></i>
                        <h3>Your wishlist is empty</h3>
                        <p class="text-muted mb-4">Save items you love to verify them later.</p>
                        <a href="{{ route('catalog') }}" class="btn btn-primary btn-lg">Explore Products</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .wishlist-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 20px;
    }

    .product-card {
        background: white;
        border: 1px solid var(--secondary-100);
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
        position: relative;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08);
        border-color: var(--primary-100);
    }

    .product-image-wrapper {
        position: relative;
        padding-top: 100%; /* 1:1 Aspect Ratio */
        overflow: hidden;
        background: var(--secondary-50);
    }

    .product-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .product-card:hover .product-image {
        transform: scale(1.05);
    }

    .remove-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: white;
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        color: var(--danger);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        z-index: 2;
        opacity: 0;
        transform: translateY(-10px);
    }

    .product-card:hover .remove-btn {
        opacity: 1;
        transform: translateY(0);
    }

    .remove-btn:hover {
        background: var(--danger);
        color: white;
    }

    .product-info {
        padding: 16px;
    }

    .product-cat {
        font-size: 11px;
        color: var(--secondary-400);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }

    .product-name {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: var(--secondary-900);
        margin-bottom: 8px;
        text-decoration: none;
        line-height: 1.4;
        height: 40px; /* Limit to 2 lines */
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .product-name:hover {
        color: var(--primary-600);
    }

    .product-price-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
    }

    .product-price {
        font-size: 16px;
        font-weight: 700;
        color: var(--secondary-900);
    }

    .product-old-price {
        font-size: 13px;
        text-decoration: line-through;
        color: var(--secondary-400);
    }

    .product-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 12px;
        border-top: 1px solid var(--secondary-50);
    }

    .product-rating {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 12px;
        color: #fbbf24;
    }

    .product-rating span {
        color: var(--secondary-500);
        margin-left: 2px;
    }

    .add-to-cart-btn {
        background: none;
        border: none;
        color: var(--primary-600);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 4px 8px;
        border-radius: 4px;
        transition: all 0.2s;
    }

    .add-to-cart-btn:hover {
        background: var(--primary-50);
    }
</style>

<script>
    function removeFromWishlist(event, productId) {
        event.preventDefault();
        
        if (!confirm('Remove from wishlist?')) return;

        const card = event.target.closest('.product-card');

        fetch('{{ route("wishlist.remove", "") }}/' + productId, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message || 'Removed from wishlist');
                if (card) {
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.9)';
                    setTimeout(() => {
                        card.remove();
                        if (document.querySelectorAll('.product-card').length === 0) {
                            location.reload();
                        }
                    }, 300);
                } else {
                    location.reload();
                }
            } else {
                showToast(data.message || 'Error removing item', 'error');
            }
        })
        .catch(() => {
            showToast('Network error. Please try again.', 'error');
        });
    }

    function moveToCart(event, productId) {
        event.preventDefault();
        const card = event.target.closest('.product-card');

        fetch('{{ route("wishlist.move", "") }}/' + productId, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message || 'Moved to cart!');
                const cartCountEl = document.getElementById('cart-badge');
                if (cartCountEl && data.cart_count) {
                    cartCountEl.textContent = data.cart_count;
                }

                if (card) {
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.9)';
                    setTimeout(() => {
                        card.remove();
                        if (document.querySelectorAll('.product-card').length === 0) {
                            location.reload();
                        }
                    }, 300);
                } else {
                    location.reload();
                }
            } else {
                showToast(data.message || 'Error moving to cart', 'error');
            }
        })
        .catch(() => {
            showToast('Network error. Please try again.', 'error');
        });
    }
</script>
@endsection
