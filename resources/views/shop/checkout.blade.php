{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    View: Checkout Page
--}}
@extends('layouts.shop')

@section('title', 'Checkout')

@section('content')
<div class="container py-5">
    <h1 class="section-title mb-5">Checkout</h1>

    @if(session('error'))
        <div class="alert alert-danger mb-4">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger mb-4">
            <strong>Please fix the following errors:</strong>
            <ul style="margin-bottom:0; margin-top:8px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('checkout.process') }}" method="POST">
        @csrf
        <div class="checkout-layout">
            <!-- Left: Shipping Info -->
            <div class="checkout-main">
                <!-- Saved Addresses -->
                @if($addresses->count() > 0)
                <div class="checkout-section">
                    <h3><i class="fas fa-map-marker-alt mr-2"></i> Delivery Address</h3>
                    <div class="address-grid">
                        @foreach($addresses as $addr)
                        <label class="address-card {{ $defaultAddress && $defaultAddress->id == $addr->id ? 'selected' : '' }}">
                            <input type="radio" name="address_id" value="{{ $addr->id }}" {{ $defaultAddress && $defaultAddress->id == $addr->id ? 'checked' : '' }}>
                            <div class="address-content">
                                <strong>{{ $addr->first_name }} {{ $addr->last_name }}</strong>
                                <p>{{ $addr->address_line_1 }}</p>
                                <p>{{ $addr->city }}, {{ $addr->state }}</p>
                                <p><i class="fas fa-phone"></i> {{ $addr->phone }}</p>
                            </div>
                            <span class="address-check"><i class="fas fa-check"></i></span>
                        </label>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-outline btn-sm mt-3" onclick="toggleNewAddress()">
                        <i class="fas fa-plus mr-2"></i> Add New Address
                    </button>
                </div>
                @endif

                <!-- New Address Form -->
                <div class="checkout-section" id="newAddressForm" style="{{ $addresses->count() == 0 ? '' : 'display: none;' }}">
                    <h3><i class="fas fa-plus-circle mr-2"></i> {{ $addresses->count() == 0 ? 'Delivery Address' : 'New Address' }}</h3>
                    <input type="hidden" name="new_address" id="newAddressFlag" value="{{ $addresses->count() == 0 ? '1' : '0' }}">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>First Name *</label>
                            <input type="text" name="first_name" class="form-control" value="{{ old('first_name', Auth::user()->name) }}" {{ $addresses->count() == 0 ? 'required' : '' }}>
                        </div>
                        <div class="form-group">
                            <label>Last Name *</label>
                            <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" {{ $addresses->count() == 0 ? 'required' : '' }}>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Phone Number *</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', Auth::user()->phone) }}" placeholder="e.g. 08012345678" {{ $addresses->count() == 0 ? 'required' : '' }}>
                    </div>
                    <div class="form-group">
                        <label>Street Address *</label>
                        <input type="text" name="address_line_1" class="form-control" value="{{ old('address_line_1') }}" placeholder="House number and street name" {{ $addresses->count() == 0 ? 'required' : '' }}>
                    </div>
                    <div class="form-group">
                        <label>Apartment, suite, etc. (optional)</label>
                        <input type="text" name="address_line_2" class="form-control" value="{{ old('address_line_2') }}">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>City *</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city') }}" {{ $addresses->count() == 0 ? 'required' : '' }}>
                        </div>
                        <div class="form-group">
                            <label>State *</label>
                            <select name="state" class="form-control" {{ $addresses->count() == 0 ? 'required' : '' }}>
                                <option value="">Select State</option>
                                @foreach(['Abia','Adamawa','Akwa Ibom','Anambra','Bauchi','Bayelsa','Benue','Borno','Cross River','Delta','Ebonyi','Edo','Ekiti','Enugu','FCT','Gombe','Imo','Jigawa','Kaduna','Kano','Katsina','Kebbi','Kogi','Kwara','Lagos','Nasarawa','Niger','Ogun','Ondo','Osun','Oyo','Plateau','Rivers','Sokoto','Taraba','Yobe','Zamfara'] as $state)
                                    <option value="{{ $state }}" {{ old('state') == $state ? 'selected' : '' }}>{{ $state }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Order Notes -->
                <div class="checkout-section">
                    <h3><i class="fas fa-sticky-note mr-2"></i> Order Notes (Optional)</h3>
                    <textarea name="notes" class="form-control" rows="3" placeholder="Any special instructions for delivery...">{{ old('notes') }}</textarea>
                </div>

                <!-- Shipping Method Selection -->
                <div class="checkout-section">
                    <h3><i class="fas fa-truck mr-2"></i> Delivery Method</h3>
                    <p class="section-hint">Choose how you'd like to receive your order</p>
                    <div class="shipping-methods-grid">
                        @foreach($shippingMethods as $index => $method)
                        @php
                            $isPickup = str_contains(strtolower($method->name), 'pickup');
                            $methodCost = $isPickup ? 0 : $vendorDeliveryFee;
                        @endphp
                        <label class="shipping-card {{ $index === 0 ? 'selected' : '' }}">
                            <input type="radio" name="shipping_method_id" value="{{ $method->id }}" data-cost="{{ $methodCost }}" {{ $index === 0 ? 'checked' : '' }}>
                            <div class="shipping-card-inner">
                                <div class="shipping-icon">
                                    @if($isPickup)
                                        <i class="fas fa-store"></i>
                                    @elseif(str_contains(strtolower($method->name), 'vendor'))
                                        <i class="fas fa-handshake"></i>
                                    @else
                                        <i class="fas fa-motorcycle"></i>
                                    @endif
                                </div>
                                <div class="shipping-info">
                                    <strong>{{ $method->name }}</strong>
                                    <span class="shipping-desc">{{ $method->description }}</span>
                                    <span class="shipping-eta"><i class="far fa-clock"></i> {{ $method->estimated_days }}</span>
                                </div>
                                <div class="shipping-price">
                                    @if($methodCost > 0)
                                        <span class="price-tag">₦{{ number_format($methodCost) }}</span>
                                    @else
                                        <span class="price-tag free">FREE</span>
                                    @endif
                                </div>
                            </div>
                            <span class="shipping-check"><i class="fas fa-check"></i></span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right: Order Summary -->
            <div class="checkout-sidebar">
                <div class="order-summary">
                    <h3>Order Summary</h3>
                    <div class="summary-items">
                        @foreach($items as $item)
                        @if($item->product)
                        <div class="summary-item">
                            <div class="item-image">
                                @if($item->product->primary_image_url)
                                    <img src="{{ $item->product->primary_image_url }}" alt="">
                                @else
                                    <div class="no-image"><i class="fas fa-image"></i></div>
                                @endif
                                <span class="item-qty">{{ $item->quantity }}</span>
                            </div>
                            <div class="item-info">
                                <span class="item-name">{{ Str::limit($item->product->name, 30) }}</span>
                                <span class="item-price">₦{{ number_format(($item->product->sale_price ?? $item->product->price) * $item->quantity) }}</span>
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </div>

                    <!-- Coupon Code -->
                    <div class="coupon-section">
                        <div class="coupon-input-group">
                            <input type="text" id="couponInput" placeholder="Enter coupon code" maxlength="50">
                            <button type="button" id="applyCouponBtn" onclick="applyCoupon()">Apply</button>
                        </div>
                        <input type="hidden" name="coupon_code" id="couponCodeHidden" value="">
                        <div id="couponMessage" class="coupon-message" style="display:none;"></div>
                    </div>

                    <div class="summary-totals">
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span>₦{{ number_format($cart->total) }}</span>
                        </div>
                        <div class="summary-row" id="shippingRow">
                            <span>Shipping</span>
                            <span id="shippingCostDisplay">
                                @php
                                    $firstIsPickup = isset($shippingMethods[0]) && str_contains(strtolower($shippingMethods[0]->name), 'pickup');
                                    $initialShipping = $firstIsPickup ? 0 : $vendorDeliveryFee;
                                @endphp
                                @if($initialShipping > 0)
                                    ₦{{ number_format($initialShipping) }}
                                @else
                                    <span class="text-success">Free</span>
                                @endif
                            </span>
                        </div>
                        <div class="summary-row discount-row" id="discountRow" style="display:none;">
                            <span><i class="fas fa-tag"></i> Coupon</span>
                            <span id="discountDisplay" class="text-success">-₦0</span>
                        </div>
                        <div class="summary-row total">
                            <span>Total</span>
                            <span id="orderTotalDisplay">₦{{ number_format($cart->total + $initialShipping) }}</span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg btn-full mt-4">
                        <i class="fas fa-lock mr-2"></i> Place Order
                    </button>
                    
                    <p class="secure-text"><i class="fas fa-shield-alt"></i> Your payment is secure</p>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .checkout-layout {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 32px;
        align-items: start;
    }

    .checkout-section {
        background: white;
        padding: 28px;
        border-radius: 20px;
        margin-bottom: 24px;
        border: 1px solid var(--secondary-100);
    }

    .checkout-section h3 {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 20px;
        color: var(--secondary-900);
    }

    .address-grid {
        display: grid;
        gap: 16px;
    }

    .address-card {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        padding: 16px;
        border: 2px solid var(--secondary-100);
        border-radius: 14px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .address-card:hover {
        border-color: var(--primary-200);
    }

    .address-card.selected,
    .address-card:has(input:checked) {
        border-color: var(--primary-500);
        background: var(--primary-50);
    }

    .address-card input {
        display: none;
    }

    .address-content {
        flex: 1;
    }

    .address-content strong {
        display: block;
        margin-bottom: 4px;
    }

    .address-content p {
        margin: 0;
        font-size: 14px;
        color: var(--secondary-600);
    }

    .address-check {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: var(--primary-500);
        color: white;
        display: none;
        align-items: center;
        justify-content: center;
        font-size: 12px;
    }

    .address-card:has(input:checked) .address-check {
        display: flex;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 8px;
        color: var(--secondary-700);
    }

    .form-control {
        width: 100%;
        padding: 14px 16px;
        border: 2px solid var(--secondary-100);
        border-radius: 12px;
        font-size: 15px;
        transition: border-color 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-400);
    }

    .order-summary {
        background: white;
        padding: 28px;
        border-radius: 20px;
        border: 1px solid var(--secondary-100);
        position: sticky;
        top: 100px;
    }

    .order-summary h3 {
        font-weight: 700;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--secondary-100);
    }

    .summary-items {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 20px;
        max-height: 300px;
        overflow-y: auto;
    }

    .summary-item {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .item-image {
        width: 56px;
        height: 56px;
        border-radius: 10px;
        overflow: hidden;
        position: relative;
        background: var(--secondary-50);
    }

    .item-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .item-qty {
        position: absolute;
        top: -6px;
        right: -6px;
        width: 20px;
        height: 20px;
        background: var(--secondary-700);
        color: white;
        font-size: 11px;
        font-weight: 700;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .no-image {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--secondary-300);
    }

    .item-info {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .item-name {
        font-size: 14px;
        font-weight: 600;
        color: var(--secondary-800);
    }

    .item-price {
        font-size: 13px;
        color: var(--secondary-500);
    }

    .summary-totals {
        border-top: 1px solid var(--secondary-100);
        padding-top: 16px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        font-size: 14px;
        color: var(--secondary-600);
    }

    .summary-row.total {
        font-size: 18px;
        font-weight: 800;
        color: var(--secondary-900);
        border-top: 1px solid var(--secondary-100);
        padding-top: 16px;
        margin-top: 8px;
    }

    .btn-full {
        width: 100%;
    }

    .btn-outline {
        border: 2px solid var(--secondary-200);
        color: var(--secondary-700);
        background: transparent;
    }

    .secure-text {
        text-align: center;
        font-size: 13px;
        color: var(--secondary-400);
        margin-top: 16px;
    }

    .secure-text i {
        color: var(--success);
        margin-right: 6px;
    }

    .alert {
        padding: 16px 20px;
        border-radius: 12px;
        font-size: 14px;
    }

    .alert-danger {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }

    .section-hint { font-size: 13px; color: var(--secondary-500); margin-bottom: 16px; }

    /* Shipping Methods */
    .shipping-methods-grid { display: flex; flex-direction: column; gap: 12px; }
    .shipping-card {
        display: flex; align-items: center; gap: 16px;
        padding: 16px 18px; border: 2px solid var(--secondary-100);
        border-radius: 16px; cursor: pointer; transition: all 0.2s; position: relative;
    }
    .shipping-card:hover { border-color: var(--primary-200); }
    .shipping-card.selected, .shipping-card:has(input:checked) {
        border-color: var(--primary-500); background: var(--primary-50);
    }
    .shipping-card input { display: none; }
    .shipping-card-inner { flex: 1; display: flex; align-items: center; gap: 16px; }
    .shipping-icon {
        width: 44px; height: 44px; border-radius: 14px;
        background: var(--secondary-50); display: flex; align-items: center;
        justify-content: center; font-size: 18px; color: var(--secondary-500); flex-shrink: 0;
    }
    .shipping-card:has(input:checked) .shipping-icon {
        background: var(--primary-100); color: var(--primary-600);
    }
    .shipping-info { flex: 1; }
    .shipping-info strong { display: block; font-size: 15px; color: var(--secondary-900); margin-bottom: 2px; }
    .shipping-desc { display: block; font-size: 12px; color: var(--secondary-500); }
    .shipping-eta { display: inline-flex; align-items: center; gap: 4px; font-size: 11px; color: var(--secondary-400); margin-top: 4px; }
    .shipping-price { flex-shrink: 0; }
    .price-tag { font-size: 15px; font-weight: 800; color: var(--secondary-900); }
    .price-tag.free { color: var(--success); }
    .shipping-check {
        width: 24px; height: 24px; border-radius: 50%;
        background: var(--primary-500); color: white; display: none;
        align-items: center; justify-content: center; font-size: 12px;
        position: absolute; top: 12px; right: 12px;
    }
    .shipping-card:has(input:checked) .shipping-check { display: flex; }

    /* Coupon */
    .coupon-section { padding: 14px 0; border-bottom: 1px solid #f1f5f9; margin-bottom: 16px; }
    .coupon-input-group { display: flex; gap: 8px; }
    .coupon-input-group input {
        flex: 1; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 12px;
        font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;
        background: #fafbfc; color: #0f172a; outline: none; transition: all 0.2s;
    }
    .coupon-input-group input:focus { border-color: var(--primary-500); background: white; }
    .coupon-input-group input::placeholder { text-transform: none; letter-spacing: normal; font-weight: 500; color: #94a3b8; }
    .coupon-input-group button {
        padding: 10px 18px; background: #0f172a; color: white; border: none;
        border-radius: 12px; font-weight: 700; font-size: 13px; cursor: pointer; transition: all 0.2s; white-space: nowrap;
    }
    .coupon-input-group button:hover { background: #1e293b; }
    .coupon-input-group button:disabled { opacity: 0.5; cursor: wait; }
    .coupon-message { margin-top: 8px; font-size: 12px; font-weight: 600; padding: 8px 12px; border-radius: 10px; }
    .coupon-message.success { background: #ecfdf5; color: #059669; }
    .coupon-message.error { background: #fef2f2; color: #dc2626; }
    .coupon-applied { display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 12px; }
    .coupon-applied .coupon-label { font-size: 13px; font-weight: 700; color: #059669; display: flex; align-items: center; gap: 6px; }
    .coupon-applied .remove-coupon { background: none; border: none; color: #dc2626; font-size: 16px; cursor: pointer; padding: 0; line-height: 1; }
    .discount-row span { color: #059669; font-weight: 700; }


    @media (max-width: 1024px) {
        .checkout-layout {
            grid-template-columns: 1fr;
        }
        
        .order-summary {
            position: static;
        }
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
function toggleNewAddress() {
    const form = document.getElementById('newAddressForm');
    const flag = document.getElementById('newAddressFlag');
    
    if (form.style.display === 'none') {
        form.style.display = 'block';
        flag.value = '1';
        // Make fields required
        form.querySelectorAll('input[name="first_name"], input[name="phone"], input[name="address_line_1"], input[name="city"], select[name="state"]').forEach(el => {
            el.required = true;
        });
    } else {
        form.style.display = 'none';
        flag.value = '0';
        // Remove required
        form.querySelectorAll('input, select').forEach(el => {
            el.required = false;
        });
    }
}

// ---- State ----
const subtotal = {{ $cart->total }};
let currentShipping = {{ $initialShipping ?? 0 }};
let currentDiscount = 0;

function recalcTotal() {
    const total = subtotal + currentShipping - currentDiscount;
    document.getElementById('orderTotalDisplay').textContent = '₦' + total.toLocaleString('en-NG');
}

// ---- Shipping ----
document.querySelectorAll('input[name="shipping_method_id"]').forEach(radio => {
    radio.addEventListener('change', function() {
        currentShipping = parseFloat(this.dataset.cost) || 0;
        const shippingDisplay = document.getElementById('shippingCostDisplay');
        if (currentShipping > 0) {
            shippingDisplay.innerHTML = '₦' + currentShipping.toLocaleString('en-NG');
        } else {
            shippingDisplay.innerHTML = '<span class="text-success">Free</span>';
        }
        document.querySelectorAll('.shipping-card').forEach(c => c.classList.remove('selected'));
        this.closest('.shipping-card').classList.add('selected');
        recalcTotal();
    });
});

// ---- Coupon ----
function applyCoupon() {
    const input = document.getElementById('couponInput');
    const btn = document.getElementById('applyCouponBtn');
    const msg = document.getElementById('couponMessage');
    const code = input.value.trim();

    if (!code) { input.focus(); return; }

    btn.disabled = true;
    btn.textContent = 'Checking...';
    msg.style.display = 'none';

    fetch('{{ route("checkout.applyCoupon") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ coupon_code: code })
    })
    .then(res => res.json().then(data => ({ ok: res.ok, data })))
    .then(({ ok, data }) => {
        btn.disabled = false;
        btn.textContent = 'Apply'; // Reset button text
        msg.style.display = 'block';

        if (ok && data.success) {
            currentDiscount = data.discount;
            document.getElementById('couponCodeHidden').value = data.coupon_code;
            
            // Show success
            msg.className = 'coupon-message success';
            msg.innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message;

            // Replace input with applied state
            const section = document.querySelector('.coupon-section');
            section.innerHTML = `
                <div class="coupon-applied">
                    <span class="coupon-label"><i class="fas fa-tag"></i> ${data.coupon_code}</span>
                    <button type="button" class="remove-coupon" onclick="removeCoupon()" title="Remove coupon">&times;</button>
                </div>
                <input type="hidden" name="coupon_code" id="couponCodeHidden" value="${data.coupon_code}">
                <div id="couponMessage" class="coupon-message success" style="display:block;"><i class="fas fa-check-circle"></i> ${data.message}</div>
            `;

            // Show discount row
            document.getElementById('discountRow').style.display = 'flex';
            document.getElementById('discountDisplay').textContent = '-₦' + data.discount.toLocaleString('en-NG');
            recalcTotal();
        } else {
            msg.className = 'coupon-message error';
            msg.innerHTML = '<i class="fas fa-times-circle"></i> ' + (data.message || 'Invalid coupon code.');
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.textContent = 'Apply';
        msg.style.display = 'block';
        msg.className = 'coupon-message error';
        msg.innerHTML = '<i class="fas fa-times-circle"></i> Something went wrong. Try again.';
    });
}

function removeCoupon() {
    currentDiscount = 0;
    document.getElementById('discountRow').style.display = 'none';
    
    const section = document.querySelector('.coupon-section');
    section.innerHTML = `
        <div class="coupon-input-group">
            <input type="text" id="couponInput" placeholder="Enter coupon code" maxlength="50">
            <button type="button" id="applyCouponBtn" onclick="applyCoupon()">Apply</button>
        </div>
        <input type="hidden" name="coupon_code" id="couponCodeHidden" value="">
        <div id="couponMessage" class="coupon-message" style="display:none;"></div>
    `;
    recalcTotal();
}
</script>
@endsection
