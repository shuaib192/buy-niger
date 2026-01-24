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
            </div>

            <!-- Right: Order Summary -->
            <div class="checkout-sidebar">
                <div class="order-summary">
                    <h3>Order Summary</h3>
                    <div class="summary-items">
                        @foreach($items as $item)
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
                        @endforeach
                    </div>

                    <div class="summary-totals">
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span>₦{{ number_format($cart->total) }}</span>
                        </div>
                        <div class="summary-row">
                            <span>Shipping</span>
                            <span class="text-success">Free</span>
                        </div>
                        <div class="summary-row total">
                            <span>Total</span>
                            <span>₦{{ number_format($cart->total) }}</span>
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
</script>
@endsection
