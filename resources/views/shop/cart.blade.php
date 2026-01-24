{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    View: Shopping Cart
--}}
@extends('layouts.shop')

@section('title', 'Your Cart')

@section('content')
<div class="container py-5">
    <h1 class="section-title mb-5">Shopping Cart</h1>

    @if($items->count() > 0)
        <div class="cart-layout">
            <!-- Cart Items -->
            <div class="cart-items">
                @foreach($items as $item)
                <div class="cart-item" data-item-id="{{ $item->id }}">
                    <div class="cart-item-image">
                        @if($item->product->primary_image_url)
                            <img src="{{ $item->product->primary_image_url }}" alt="{{ $item->product->name }}">
                        @else
                            <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:#f1f5f9;">
                                <i class="fas fa-image" style="color:#cbd5e1; font-size:1.5rem;"></i>
                            </div>
                        @endif
                    </div>
                    <div class="cart-item-details">
                        <a href="{{ route('product.detail', $item->product->slug) }}" class="cart-item-name">{{ $item->product->name }}</a>
                        <div class="cart-item-meta">{{ $item->product->category->name ?? 'General' }}</div>
                        <div class="cart-item-price">₦{{ number_format($item->product->sale_price ?? $item->product->price) }}</div>
                    </div>
                    <div class="cart-item-qty">
                        <button class="qty-btn" onclick="updateQty({{ $item->id }}, -1)"><i class="fas fa-minus"></i></button>
                        <input type="number" value="{{ $item->quantity }}" min="1" max="99" id="qty-{{ $item->id }}" onchange="setQty({{ $item->id }}, this.value)">
                        <button class="qty-btn" onclick="updateQty({{ $item->id }}, 1)"><i class="fas fa-plus"></i></button>
                    </div>
                    @php $itemPrice = $item->product->sale_price ?? $item->product->price; @endphp
                    <div class="cart-item-total" id="total-{{ $item->id }}">
                        ₦{{ number_format($itemPrice * $item->quantity) }}
                    </div>
                    <button class="cart-item-remove" onclick="removeItem({{ $item->id }})">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
                @endforeach
            </div>

            <!-- Cart Summary -->
            <div class="cart-summary">
                <h3>Order Summary</h3>
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span id="cart-subtotal">₦{{ number_format($cart->total) }}</span>
                </div>
                <div class="summary-row">
                    <span>Delivery</span>
                    <span>Calculated at checkout</span>
                </div>
                <div class="summary-row total">
                    <span>Total</span>
                    <span id="cart-total">₦{{ number_format($cart->total) }}</span>
                </div>
                <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-full btn-lg mt-4">
                    <i class="fas fa-lock mr-2"></i> Proceed to Checkout
                </a>
                <a href="{{ route('catalog') }}" class="btn btn-outline btn-full mt-3">Continue Shopping</a>
            </div>
        </div>
    @else
        <div class="empty-cart">
            <i class="fas fa-shopping-cart"></i>
            <h2>Your cart is empty</h2>
            <p>Looks like you haven't added anything to your cart yet.</p>
            <a href="{{ route('catalog') }}" class="btn btn-primary btn-lg">Start Shopping</a>
        </div>
    @endif
</div>

<style>
    .cart-layout {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 32px;
        align-items: start;
    }

    .cart-items {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .cart-item {
        display: grid;
        grid-template-columns: 100px 1fr auto auto auto;
        gap: 20px;
        align-items: center;
        background: white;
        padding: 20px;
        border-radius: 16px;
        border: 1px solid var(--secondary-100);
    }

    .cart-item-image {
        width: 100px;
        height: 100px;
        border-radius: 12px;
        overflow: hidden;
    }

    .cart-item-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .cart-item-name {
        font-weight: 700;
        font-size: 16px;
        color: var(--secondary-900);
        display: block;
        margin-bottom: 4px;
    }

    .cart-item-name:hover {
        color: var(--primary-600);
    }

    .cart-item-meta {
        font-size: 13px;
        color: var(--secondary-400);
    }

    .cart-item-price {
        font-weight: 700;
        color: var(--primary-600);
        margin-top: 8px;
    }

    .cart-item-qty {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .qty-btn {
        width: 32px;
        height: 32px;
        border: 1px solid var(--secondary-200);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        color: var(--secondary-600);
        transition: all 0.2s;
    }

    .qty-btn:hover {
        border-color: var(--primary-500);
        color: var(--primary-600);
    }

    .cart-item-qty input {
        width: 50px;
        text-align: center;
        border: 1px solid var(--secondary-200);
        border-radius: 8px;
        padding: 6px;
        font-weight: 600;
    }

    .cart-item-total {
        font-size: 18px;
        font-weight: 800;
        color: var(--secondary-900);
        min-width: 100px;
        text-align: right;
    }

    .cart-item-remove {
        color: var(--secondary-300);
        font-size: 16px;
        padding: 8px;
        transition: color 0.2s;
    }

    .cart-item-remove:hover {
        color: var(--danger);
    }

    .cart-summary {
        background: white;
        padding: 28px;
        border-radius: 20px;
        border: 1px solid var(--secondary-100);
        position: sticky;
        top: 100px;
    }

    .cart-summary h3 {
        font-weight: 700;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--secondary-100);
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        color: var(--secondary-600);
        font-size: 14px;
    }

    .summary-row.total {
        font-size: 18px;
        font-weight: 800;
        color: var(--secondary-900);
        padding-top: 16px;
        margin-top: 16px;
        border-top: 1px solid var(--secondary-100);
    }

    .btn-outline {
        border: 2px solid var(--secondary-200);
        color: var(--secondary-700);
        background: transparent;
    }

    .btn-outline:hover {
        border-color: var(--primary-500);
        color: var(--primary-600);
    }

    .btn-full {
        width: 100%;
    }

    .empty-cart {
        text-align: center;
        padding: 80px 20px;
        background: white;
        border-radius: 24px;
    }

    .empty-cart i {
        font-size: 4rem;
        color: var(--secondary-200);
        margin-bottom: 24px;
    }

    .empty-cart h2 {
        font-size: 1.5rem;
        margin-bottom: 8px;
    }

    .empty-cart p {
        color: var(--secondary-500);
        margin-bottom: 24px;
    }

    @media (max-width: 1024px) {
        .cart-layout {
            grid-template-columns: 1fr;
        }
        
        .cart-summary {
            position: static;
        }
    }

    @media (max-width: 768px) {
        .cart-item {
            grid-template-columns: 80px 1fr;
            gap: 12px;
        }
        
        .cart-item-image {
            width: 80px;
            height: 80px;
        }
        
        .cart-item-qty,
        .cart-item-total,
        .cart-item-remove {
            grid-column: 2;
        }
        
        .cart-item-total {
            text-align: left;
        }
    }
</style>

<script>
function updateQty(itemId, delta) {
    const input = document.getElementById('qty-' + itemId);
    let newVal = parseInt(input.value) + delta;
    if (newVal < 1) newVal = 1;
    if (newVal > 99) newVal = 99;
    input.value = newVal;
    setQty(itemId, newVal);
}

function setQty(itemId, qty) {
    fetch('{{ route("cart.update", "") }}/' + itemId, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ quantity: qty })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('total-' + itemId).textContent = '₦' + data.item_total.toLocaleString();
            document.getElementById('cart-subtotal').textContent = '₦' + data.cart_total.toLocaleString();
            document.getElementById('cart-total').textContent = '₦' + data.cart_total.toLocaleString();
            updateCartBadge(data.cart_count);
        }
    });
}

function removeItem(itemId) {
    if (!confirm('Remove this item?')) return;
    
    fetch('{{ route("cart.remove", "") }}/' + itemId, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.querySelector('[data-item-id="' + itemId + '"]').remove();
            document.getElementById('cart-subtotal').textContent = '₦' + data.cart_total.toLocaleString();
            document.getElementById('cart-total').textContent = '₦' + data.cart_total.toLocaleString();
            updateCartBadge(data.cart_count);
            if (data.cart_count === 0) location.reload();
        }
    });
}

function updateCartBadge(count) {
    const badge = document.querySelector('.cart-btn .badge');
    if (badge) badge.textContent = count;
}
</script>
@endsection
