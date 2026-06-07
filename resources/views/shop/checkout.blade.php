{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    View: Checkout Page (Premium v2.0)
--}}
@extends('layouts.shop')

@section('title', 'Checkout — BuyNiger')

@section('content')
<div class="ck-page">
    {{-- Progress Bar --}}
    <div class="ck-progress-bar">
        <div class="container">
            <div class="ck-steps">
                <div class="ck-step done">
                    <div class="ck-step-icon"><i class="fas fa-shopping-cart"></i></div>
                    <span>Cart</span>
                </div>
                <div class="ck-step-line done"></div>
                <div class="ck-step active">
                    <div class="ck-step-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <span>Shipping</span>
                </div>
                <div class="ck-step-line"></div>
                <div class="ck-step">
                    <div class="ck-step-icon"><i class="fas fa-check-double"></i></div>
                    <span>Confirm</span>
                </div>
            </div>
        </div>
    </div>

    <div class="container ck-container">
        {{-- Error Alerts --}}
        @if(session('error'))
            <div class="ck-alert ck-alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        @if($errors->any())
            <div class="ck-alert ck-alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    <strong>Please fix the following errors:</strong>
                    <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            </div>
        @endif

        <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
            @csrf
            <div class="ck-grid">

                {{-- ===== LEFT COLUMN ===== --}}
                <div class="ck-main">

                    {{-- Saved Addresses --}}
                    @if($addresses->count() > 0)
                    <div class="ck-card" id="savedAddressSection">
                        <div class="ck-card-header">
                            <div class="ck-card-icon"><i class="fas fa-map-marker-alt"></i></div>
                            <div>
                                <h2>Delivery Address</h2>
                                <p>Select where to deliver your order</p>
                            </div>
                        </div>
                        <div class="address-grid">
                            @foreach($addresses as $addr)
                            <label class="address-card {{ $defaultAddress && $defaultAddress->id == $addr->id ? 'is-selected' : '' }}" for="addr-{{ $addr->id }}">
                                <input type="radio" name="address_id" id="addr-{{ $addr->id }}" value="{{ $addr->id }}" 
                                       {{ $defaultAddress && $defaultAddress->id == $addr->id ? 'checked' : '' }}
                                       onchange="document.querySelectorAll('.address-card').forEach(c=>c.classList.remove('is-selected')); this.closest('.address-card').classList.add('is-selected')">
                                <div class="addr-check-ring">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div class="addr-icon-wrap">
                                    <i class="fas fa-home"></i>
                                </div>
                                <div class="addr-body">
                                    <strong>{{ $addr->first_name }} {{ $addr->last_name }}</strong>
                                    <span>{{ $addr->address_line_1 }}@if($addr->address_line_2), {{ $addr->address_line_2 }}@endif</span>
                                    <span>{{ $addr->city }}, {{ $addr->state }}</span>
                                    <span class="addr-phone"><i class="fas fa-phone-alt"></i> {{ $addr->phone }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        <button type="button" class="ck-add-addr-btn" onclick="toggleNewAddress()">
                            <i class="fas fa-plus-circle"></i> Add a New Address
                        </button>
                    </div>
                    @endif

                    {{-- New Address Form --}}
                    <div class="ck-card" id="newAddressForm" style="{{ $addresses->count() == 0 ? '' : 'display:none;' }}">
                        <div class="ck-card-header">
                            <div class="ck-card-icon"><i class="fas fa-plus-circle"></i></div>
                            <div>
                                <h2>{{ $addresses->count() == 0 ? 'Delivery Address' : 'New Address' }}</h2>
                                <p>Where should we deliver your order?</p>
                            </div>
                        </div>
                        <input type="hidden" name="new_address" id="newAddressFlag" value="{{ $addresses->count() == 0 ? '1' : '0' }}">
                        
                        <div class="ck-form-grid two-col">
                            <div class="ck-field">
                                <label for="ck_fname">First Name <span class="req">*</span></label>
                                <input type="text" id="ck_fname" name="first_name" class="ck-input" 
                                       value="{{ old('first_name', Auth::user()->name) }}"
                                       {{ $addresses->count() == 0 ? 'required' : '' }} placeholder="John">
                            </div>
                            <div class="ck-field">
                                <label for="ck_lname">Last Name <span class="req">*</span></label>
                                <input type="text" id="ck_lname" name="last_name" class="ck-input" 
                                       value="{{ old('last_name') }}"
                                       {{ $addresses->count() == 0 ? 'required' : '' }} placeholder="Doe">
                            </div>
                        </div>
                        <div class="ck-field">
                            <label for="ck_phone">Phone Number <span class="req">*</span></label>
                            <div class="ck-input-icon-wrap">
                                <i class="fas fa-phone-alt"></i>
                                <input type="text" id="ck_phone" name="phone" class="ck-input with-icon" 
                                       value="{{ old('phone', Auth::user()->phone) }}" placeholder="e.g. 08012345678"
                                       {{ $addresses->count() == 0 ? 'required' : '' }}>
                            </div>
                        </div>
                        <div class="ck-field">
                            <label for="ck_addr1">Street Address <span class="req">*</span></label>
                            <input type="text" id="ck_addr1" name="address_line_1" class="ck-input" 
                                   value="{{ old('address_line_1') }}" placeholder="House number and street name"
                                   {{ $addresses->count() == 0 ? 'required' : '' }}>
                        </div>
                        <div class="ck-field">
                            <label for="ck_addr2">Apartment, Suite (Optional)</label>
                            <input type="text" id="ck_addr2" name="address_line_2" class="ck-input" 
                                   value="{{ old('address_line_2') }}" placeholder="Apt, Suite, Floor, etc.">
                        </div>
                        <div class="ck-form-grid two-col">
                            <div class="ck-field">
                                <label for="ck_city">City <span class="req">*</span></label>
                                <input type="text" id="ck_city" name="city" class="ck-input" 
                                       value="{{ old('city') }}"
                                       {{ $addresses->count() == 0 ? 'required' : '' }} placeholder="e.g. Abuja">
                            </div>
                            <div class="ck-field">
                                <label for="ck_state">State <span class="req">*</span></label>
                                <select id="ck_state" name="state" class="ck-input" {{ $addresses->count() == 0 ? 'required' : '' }}>
                                    <option value="">Select State</option>
                                    @foreach(['Abia','Adamawa','Akwa Ibom','Anambra','Bauchi','Bayelsa','Benue','Borno','Cross River','Delta','Ebonyi','Edo','Ekiti','Enugu','FCT','Gombe','Imo','Jigawa','Kaduna','Kano','Katsina','Kebbi','Kogi','Kwara','Lagos','Nasarawa','Niger','Ogun','Ondo','Osun','Oyo','Plateau','Rivers','Sokoto','Taraba','Yobe','Zamfara'] as $state)
                                        <option value="{{ $state }}" {{ old('state') == $state ? 'selected' : '' }}>{{ $state }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Delivery Info --}}
                    <div class="ck-card">
                        <div class="ck-card-header">
                            <div class="ck-card-icon green"><i class="fas fa-truck"></i></div>
                            <div>
                                <h2>Delivery Method</h2>
                                <p>Coordinated directly with each vendor</p>
                            </div>
                        </div>
                        <div class="delivery-info-box">
                            <div class="delivery-info-icon"><i class="fas fa-handshake"></i></div>
                            <div>
                                <strong>Vendor-Arranged Delivery</strong>
                                <p>After placing your order, use the WhatsApp button to chat with the vendor and agree on delivery details and cost.</p>
                            </div>
                        </div>

                        @php
                            $checkoutVendors = $items->map(fn($i) => optional($i->product)->vendor)->unique('id')->filter();
                        @endphp
                        <div class="vendor-delivery-list">
                            @forelse($checkoutVendors as $vendor)
                            <div class="vendor-delivery-card">
                                <div class="vd-header">
                                    <div class="vd-avatar">{{ substr($vendor->store_name ?? 'V', 0, 1) }}</div>
                                    <span class="vd-name">{{ $vendor->store_name }}</span>
                                    @if($vendor->business_phone)
                                        <span class="vd-available"><i class="fas fa-circle"></i> Available on WhatsApp</span>
                                    @endif
                                </div>
                                @if($vendor->business_phone)
                                <div class="vd-actions">
                                    <a href="https://wa.me/{{ $vendor->whatsapp_number }}?text={{ urlencode('Hi ' . $vendor->store_name . '! I just placed an order on BuyNiger. I\'d like home delivery — please confirm my order and share your delivery cost and timeline.') }}"
                                       target="_blank" class="vd-btn vd-btn-delivery">
                                        <div class="vd-btn-icon"><i class="fas fa-motorcycle"></i></div>
                                        <div>
                                            <strong>Ask About Delivery</strong>
                                            <span>Arrange home delivery</span>
                                        </div>
                                        <i class="fab fa-whatsapp vd-wa-icon"></i>
                                    </a>
                                    <a href="https://wa.me/{{ $vendor->whatsapp_number }}?text={{ urlencode('Hi ' . $vendor->store_name . '! I just placed an order on BuyNiger and I\'d like to come pick it up from your store. Please share your store address and when I can come.') }}"
                                       target="_blank" class="vd-btn vd-btn-pickup">
                                        <div class="vd-btn-icon blue"><i class="fas fa-store"></i></div>
                                        <div>
                                            <strong>Pickup from Store</strong>
                                            <span>Get address on WhatsApp</span>
                                        </div>
                                        <i class="fab fa-whatsapp vd-wa-icon"></i>
                                    </a>
                                </div>
                                @else
                                <div class="vd-no-contact">
                                    <i class="fas fa-info-circle"></i>
                                    No WhatsApp set for this vendor. Contact via platform messaging after ordering.
                                </div>
                                @endif
                            </div>
                            @empty
                            <p class="ck-text-muted">Vendor contact details will be available after ordering.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Order Notes --}}
                    <div class="ck-card">
                        <div class="ck-card-header">
                            <div class="ck-card-icon purple"><i class="fas fa-sticky-note"></i></div>
                            <div>
                                <h2>Order Notes</h2>
                                <p>Any special instructions? (Optional)</p>
                            </div>
                        </div>
                        <div class="ck-field">
                            <textarea name="notes" class="ck-input ck-textarea" rows="3" 
                                      placeholder="e.g. Leave at the gate, call before delivery...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- ===== RIGHT COLUMN: ORDER SUMMARY ===== --}}
                <div class="ck-sidebar">
                    <div class="ck-summary-card">
                        <div class="ck-summary-header">
                            <h2><i class="fas fa-receipt"></i> Order Summary</h2>
                            <span class="ck-item-count">{{ $items->count() }} item{{ $items->count() != 1 ? 's' : '' }}</span>
                        </div>

                        {{-- Items List --}}
                        <div class="ck-items-list">
                            @foreach($items as $item)
                            @if($item->product)
                            <div class="ck-summary-item">
                                <div class="ck-item-img-wrap">
                                    @if($item->product->primary_image_url)
                                        <img src="{{ $item->product->primary_image_url }}" alt="{{ $item->product->name }}">
                                    @else
                                        <div class="ck-item-no-img"><i class="fas fa-box"></i></div>
                                    @endif
                                    <span class="ck-qty-badge">{{ $item->quantity }}</span>
                                </div>
                                <div class="ck-item-details">
                                    <span class="ck-item-name">{{ Str::limit($item->product->name, 32) }}</span>
                                    @if($item->product->vendor)
                                        <span class="ck-item-vendor"><i class="fas fa-store"></i> {{ $item->product->vendor->store_name }}</span>
                                    @endif
                                </div>
                                <span class="ck-item-price">₦{{ number_format($item->subtotal) }}</span>
                            </div>
                            @endif
                            @endforeach
                        </div>

                        {{-- Coupon --}}
                        <div class="ck-coupon-wrap" id="couponSection">
                            <div class="ck-coupon-row">
                                <div class="ck-coupon-icon"><i class="fas fa-tag"></i></div>
                                <input type="text" id="couponInput" class="ck-coupon-input" 
                                       placeholder="Coupon code" maxlength="50">
                                <button type="button" id="applyCouponBtn" class="ck-coupon-btn" onclick="applyCoupon()">Apply</button>
                            </div>
                            <input type="hidden" name="coupon_code" id="couponCodeHidden" value="">
                            <div id="couponMessage" class="ck-coupon-msg" style="display:none;"></div>
                        </div>

                        {{-- Totals --}}
                        <div class="ck-totals">
                            <div class="ck-total-row">
                                <span>Subtotal</span>
                                <span>₦{{ number_format($cart->total) }}</span>
                            </div>
                            <div class="ck-total-row">
                                <span>Delivery</span>
                                <span class="ck-muted-val">Arranged w/ vendor</span>
                            </div>
                            <div class="ck-total-row ck-discount-row" id="discountRow" style="display:none;">
                                <span><i class="fas fa-tag"></i> Coupon Discount</span>
                                <span id="discountDisplay" class="ck-discount-val">-₦0</span>
                            </div>
                            <div class="ck-total-row ck-grand-total">
                                <span>Total</span>
                                <span id="orderTotalDisplay">₦{{ number_format($cart->total) }}</span>
                            </div>
                        </div>

                        {{-- Place Order Button --}}
                        <button type="submit" class="ck-place-order-btn" id="placeOrderBtn">
                            <i class="fas fa-lock"></i>
                            <span>Place Order Securely</span>
                            <span class="ck-btn-arrow"><i class="fas fa-arrow-right"></i></span>
                        </button>

                        <div class="ck-trust-badges">
                            <div class="trust-badge"><i class="fas fa-shield-alt"></i><span>Secure Checkout</span></div>
                            <div class="trust-badge"><i class="fas fa-undo-alt"></i><span>Easy Returns</span></div>
                            <div class="trust-badge"><i class="fas fa-headset"></i><span>24/7 Support</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
/* ============================================
   CHECKOUT — PREMIUM v2.0
   ============================================ */

/* Progress Bar */
.ck-progress-bar {
    background: white;
    border-bottom: 1px solid #f1f5f9;
    padding: 16px 0;
    position: sticky;
    top: 0;
    z-index: 100;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
}
.ck-steps {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
    max-width: 480px;
    margin: 0 auto;
}
.ck-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 5px;
    position: relative;
    z-index: 1;
}
.ck-step-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #f1f5f9;
    color: #94a3b8;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    font-weight: 700;
    border: 2.5px solid #e2e8f0;
    transition: all 0.3s;
}
.ck-step span {
    font-size: 11px;
    font-weight: 600;
    color: #94a3b8;
    white-space: nowrap;
}
.ck-step.done .ck-step-icon { background: #0066FF; color: white; border-color: #0066FF; box-shadow: 0 4px 12px rgba(0,102,255,0.25); }
.ck-step.done span { color: #0066FF; }
.ck-step.active .ck-step-icon { background: white; color: #0066FF; border-color: #0066FF; box-shadow: 0 0 0 4px rgba(0,102,255,0.12); animation: ck-pulse 2.5s infinite; }
.ck-step.active span { color: #0f172a; font-weight: 700; }
@keyframes ck-pulse { 0%,100%{ box-shadow: 0 0 0 4px rgba(0,102,255,0.12); } 50%{ box-shadow: 0 0 0 7px rgba(0,102,255,0.06); } }
.ck-step-line {
    flex: 1;
    height: 2.5px;
    background: #e2e8f0;
    margin: 0 6px;
    margin-bottom: 16px;
    border-radius: 4px;
    transition: background 0.3s;
}
.ck-step-line.done { background: #0066FF; }

/* Page Layout */
.ck-page { background: #f8fafc; min-height: 100vh; padding-bottom: 60px; }
.ck-container { padding-top: 36px; }
.ck-grid {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 28px;
    align-items: start;
}

/* Alert */
.ck-alert {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 14px 18px;
    border-radius: 14px;
    font-size: 14px;
    margin-bottom: 20px;
    animation: ck-fadeIn 0.3s ease;
}
@keyframes ck-fadeIn { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
.ck-alert-error { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
.ck-alert i { flex-shrink: 0; margin-top: 1px; }
.ck-alert ul { margin: 6px 0 0 16px; padding: 0; }
.ck-alert li { font-size: 13px; margin-bottom: 2px; }

/* Cards */
.ck-card {
    background: white;
    border: 1px solid #f1f5f9;
    border-radius: 22px;
    padding: 28px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    transition: box-shadow 0.2s;
}
.ck-card:last-child { margin-bottom: 0; }

/* Card Header */
.ck-card-header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 22px;
    padding-bottom: 18px;
    border-bottom: 1px solid #f8fafc;
}
.ck-card-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    background: #eef2ff;
    color: #6366f1;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.ck-card-icon.green { background: #ecfdf5; color: #059669; }
.ck-card-icon.purple { background: #fdf4ff; color: #a855f7; }
.ck-card-header h2 { font-size: 16px; font-weight: 800; color: #0f172a; margin: 0 0 2px; }
.ck-card-header p { font-size: 12px; color: #94a3b8; margin: 0; font-weight: 500; }

/* Address Grid */
.address-grid { display: flex; flex-direction: column; gap: 12px; margin-bottom: 16px; }
.address-card {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px 18px;
    border: 2px solid #f1f5f9;
    border-radius: 16px;
    cursor: pointer;
    transition: all 0.25s;
    position: relative;
    background: #fafbfc;
}
.address-card input[type="radio"] { display: none; }
.address-card:hover { border-color: #c7d2fe; background: #f8faff; }
.address-card.is-selected { border-color: #0066FF; background: #f0f7ff; }
.addr-check-ring {
    width: 22px; height: 22px; border-radius: 50%;
    border: 2px solid #e2e8f0;
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: 10px;
    transition: all 0.2s;
    flex-shrink: 0;
}
.address-card.is-selected .addr-check-ring { background: #0066FF; border-color: #0066FF; }
.addr-icon-wrap {
    width: 40px; height: 40px; border-radius: 12px;
    background: #f1f5f9; color: #6366f1;
    display: flex; align-items: center; justify-content: center;
    font-size: 15px; flex-shrink: 0;
}
.address-card.is-selected .addr-icon-wrap { background: #eef2ff; }
.addr-body { flex: 1; display: flex; flex-direction: column; gap: 1px; }
.addr-body strong { font-size: 14px; font-weight: 700; color: #0f172a; }
.addr-body span { font-size: 12px; color: #64748b; }
.addr-phone { display: flex; align-items: center; gap: 5px; color: #0066FF !important; font-weight: 600 !important; margin-top: 4px; }
.addr-phone i { font-size: 10px; }
.ck-add-addr-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border: 2px dashed #c7d2fe;
    border-radius: 14px;
    background: transparent;
    color: #6366f1;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
    width: 100%;
    justify-content: center;
}
.ck-add-addr-btn:hover { border-color: #6366f1; background: #f0f0ff; }

/* Form Fields */
.ck-form-grid { display: flex; gap: 16px; }
.ck-form-grid.two-col > * { flex: 1; }
.ck-field { margin-bottom: 18px; }
.ck-field label { display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 8px; letter-spacing: 0.02em; text-transform: uppercase; }
.req { color: #ef4444; }
.ck-input {
    width: 100%; padding: 13px 16px;
    border: 2px solid #e8edf5;
    border-radius: 12px;
    font-size: 14px; font-weight: 500;
    color: #0f172a;
    background: #fafbfc;
    transition: all 0.2s;
    outline: none;
    box-sizing: border-box;
}
.ck-input:focus { border-color: #6366f1; background: white; box-shadow: 0 0 0 4px rgba(99,102,241,0.08); }
.ck-input.with-icon { padding-left: 42px; }
.ck-textarea { resize: vertical; min-height: 80px; font-family: inherit; }
select.ck-input { cursor: pointer; }
.ck-input-icon-wrap { position: relative; }
.ck-input-icon-wrap > i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 13px; pointer-events: none; }

/* Delivery Section */
.delivery-info-box {
    display: flex; gap: 14px; align-items: flex-start;
    background: #f0fdf4; border: 1px solid #bbf7d0;
    border-radius: 14px; padding: 16px 18px;
    margin-bottom: 16px;
}
.delivery-info-icon {
    width: 40px; height: 40px; border-radius: 12px;
    background: #22c55e; color: white;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; flex-shrink: 0;
}
.delivery-info-box strong { display: block; color: #15803d; font-size: 14px; font-weight: 700; margin-bottom: 4px; }
.delivery-info-box p { margin: 0; font-size: 13px; color: #166534; line-height: 1.5; }
.vendor-delivery-list { display: flex; flex-direction: column; gap: 12px; }
.vendor-delivery-card {
    border: 1px solid #f1f5f9;
    border-radius: 16px;
    overflow: hidden;
}
.vd-header {
    display: flex; align-items: center; gap: 10px;
    padding: 12px 16px;
    background: #f8fafc;
    border-bottom: 1px solid #f1f5f9;
}
.vd-avatar {
    width: 32px; height: 32px; border-radius: 10px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white; display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: 13px; flex-shrink: 0;
}
.vd-name { font-size: 13px; font-weight: 700; color: #0f172a; flex: 1; }
.vd-available { font-size: 11px; font-weight: 700; color: #22c55e; display: flex; align-items: center; gap: 4px; }
.vd-available i { font-size: 6px; }
.vd-actions { display: flex; flex-direction: column; }
.vd-btn {
    display: flex; align-items: center; gap: 12px;
    padding: 13px 16px; text-decoration: none;
    border-bottom: 1px solid #f8fafc;
    transition: background 0.2s;
}
.vd-btn:last-child { border-bottom: none; }
.vd-btn:hover { background: #f8fafc; }
.vd-btn-icon {
    width: 38px; height: 38px; border-radius: 11px;
    background: #dcfce7; color: #16a34a;
    display: flex; align-items: center; justify-content: center;
    font-size: 15px; flex-shrink: 0;
}
.vd-btn-icon.blue { background: #dbeafe; color: #2563eb; }
.vd-btn strong { display: block; font-size: 13px; color: #0f172a; margin-bottom: 1px; }
.vd-btn span { font-size: 11px; color: #94a3b8; }
.vd-btn > div { flex: 1; }
.vd-wa-icon { color: #25d366; font-size: 20px; flex-shrink: 0; }
.vd-no-contact { padding: 14px 16px; font-size: 13px; color: #94a3b8; display: flex; gap: 8px; align-items: center; }

/* ===== SIDEBAR / ORDER SUMMARY ===== */
.ck-sidebar { position: sticky; top: 90px; }
.ck-summary-card {
    background: white;
    border: 1px solid #f1f5f9;
    border-radius: 22px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.06);
    overflow: hidden;
}
.ck-summary-header {
    display: flex; justify-content: space-between; align-items: center;
    padding: 20px 24px 16px;
    border-bottom: 1px solid #f8fafc;
}
.ck-summary-header h2 { font-size: 16px; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 10px; }
.ck-summary-header h2 i { color: #6366f1; }
.ck-item-count { font-size: 12px; font-weight: 700; color: #94a3b8; background: #f1f5f9; padding: 4px 10px; border-radius: 20px; }

/* Items */
.ck-items-list { padding: 16px 24px; display: flex; flex-direction: column; gap: 12px; max-height: 280px; overflow-y: auto; }
.ck-items-list::-webkit-scrollbar { width: 4px; }
.ck-items-list::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }
.ck-summary-item { display: flex; align-items: center; gap: 12px; }
.ck-item-img-wrap { width: 52px; height: 52px; border-radius: 12px; overflow: hidden; position: relative; flex-shrink: 0; }
.ck-item-img-wrap img { width: 100%; height: 100%; object-fit: cover; }
.ck-item-no-img { width: 100%; height: 100%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 18px; }
.ck-qty-badge {
    position: absolute; top: -4px; right: -4px;
    width: 19px; height: 19px; background: #0f172a;
    color: white; font-size: 10px; font-weight: 800;
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    border: 2px solid white;
}
.ck-item-details { flex: 1; display: flex; flex-direction: column; gap: 2px; min-width: 0; }
.ck-item-name { font-size: 13px; font-weight: 600; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.ck-item-vendor { font-size: 11px; color: #94a3b8; display: flex; align-items: center; gap: 4px; }
.ck-item-price { font-size: 14px; font-weight: 700; color: #0f172a; flex-shrink: 0; }

/* Coupon */
.ck-coupon-wrap { padding: 14px 24px; border-top: 1px solid #f8fafc; border-bottom: 1px solid #f8fafc; }
.ck-coupon-row { display: flex; align-items: center; gap: 8px; }
.ck-coupon-icon { color: #6366f1; font-size: 15px; flex-shrink: 0; }
.ck-coupon-input {
    flex: 1; padding: 9px 12px;
    border: 1.5px solid #e2e8f0; border-radius: 10px;
    font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;
    background: #fafbfc; outline: none; transition: all 0.2s;
    color: #0f172a;
}
.ck-coupon-input::placeholder { text-transform: none; letter-spacing: normal; font-weight: 400; color: #94a3b8; }
.ck-coupon-input:focus { border-color: #6366f1; background: white; }
.ck-coupon-btn {
    padding: 9px 16px; background: #0f172a; color: white;
    border: none; border-radius: 10px;
    font-weight: 700; font-size: 12px; cursor: pointer; transition: all 0.2s; white-space: nowrap;
}
.ck-coupon-btn:hover { background: #1e293b; }
.ck-coupon-btn:disabled { opacity: 0.5; cursor: wait; }
.ck-coupon-msg { margin-top: 8px; font-size: 12px; font-weight: 600; padding: 7px 12px; border-radius: 9px; }
.ck-coupon-msg.success { background: #ecfdf5; color: #059669; }
.ck-coupon-msg.error { background: #fef2f2; color: #dc2626; }
.ck-coupon-applied { display: flex; align-items: center; justify-content: space-between; padding: 9px 12px; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 10px; }
.ck-coupon-applied .ck-applied-label { font-size: 12px; font-weight: 700; color: #059669; display: flex; align-items: center; gap: 6px; }
.ck-coupon-applied .ck-remove-coupon { background: none; border: none; color: #dc2626; font-size: 18px; cursor: pointer; line-height: 1; padding: 0; }

/* Totals */
.ck-totals { padding: 16px 24px; display: flex; flex-direction: column; gap: 0; }
.ck-total-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; font-size: 14px; color: #64748b; border-bottom: 1px solid #f8fafc; }
.ck-total-row:last-child { border-bottom: none; }
.ck-total-row span:last-child { font-weight: 600; color: #0f172a; }
.ck-muted-val { color: #94a3b8 !important; font-size: 12px !important; font-weight: 500 !important; }
.ck-discount-row span { color: #059669 !important; }
.ck-discount-val { color: #059669 !important; font-weight: 800 !important; }
.ck-grand-total { padding-top: 12px; margin-top: 4px; border-top: 2px solid #f1f5f9 !important; }
.ck-grand-total > span:first-child { font-size: 16px; font-weight: 800; color: #0f172a; }
.ck-grand-total > span:last-child { font-size: 22px; font-weight: 900; color: #0f172a; letter-spacing: -0.03em; }

/* Place Order */
.ck-place-order-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    width: 100%;
    padding: 16px 24px;
    margin: 0;
    background: linear-gradient(135deg, #0066FF, #6366f1);
    color: white;
    border: none;
    border-radius: 0;
    font-size: 15px;
    font-weight: 800;
    cursor: pointer;
    transition: all 0.25s;
    position: relative;
    overflow: hidden;
    letter-spacing: 0.01em;
}
.ck-place-order-btn::before {
    content: '';
    position: absolute; inset: 0;
    background: linear-gradient(135deg, #004ecc, #4f46e5);
    opacity: 0;
    transition: opacity 0.25s;
}
.ck-place-order-btn:hover::before { opacity: 1; }
.ck-place-order-btn:hover { transform: translateY(-1px); box-shadow: 0 8px 24px rgba(0,102,255,0.35); }
.ck-place-order-btn > * { position: relative; z-index: 1; }
.ck-btn-arrow { margin-left: auto; width: 28px; height: 28px; background: rgba(255,255,255,0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 11px; }

/* Trust Badges */
.ck-trust-badges {
    display: flex;
    justify-content: space-around;
    padding: 16px 20px;
    background: #f8fafc;
    border-top: 1px solid #f1f5f9;
}
.trust-badge { display: flex; flex-direction: column; align-items: center; gap: 4px; }
.trust-badge i { font-size: 16px; color: #6366f1; }
.trust-badge span { font-size: 10px; font-weight: 600; color: #64748b; white-space: nowrap; }

.ck-text-muted { font-size: 13px; color: #94a3b8; text-align: center; padding: 20px; }

/* Responsive */
@media (max-width: 1024px) {
    .ck-grid { grid-template-columns: 1fr; }
    .ck-sidebar { position: static; }
    .ck-summary-card { border-radius: 22px; }
}
@media (max-width: 768px) {
    .ck-form-grid.two-col { flex-direction: column; gap: 0; }
    .ck-card { padding: 20px 16px; }
    .ck-card-header { gap: 12px; }
    .ck-card-icon { width: 40px; height: 40px; font-size: 15px; }
    .ck-progress-bar { padding: 12px 0; }
    .ck-step-icon { width: 34px; height: 34px; font-size: 13px; }
}
</style>

<script>
function toggleNewAddress() {
    const form = document.getElementById('newAddressForm');
    const flag = document.getElementById('newAddressFlag');
    const savedSection = document.getElementById('savedAddressSection');
    if (form.style.display === 'none') {
        form.style.display = 'block';
        flag.value = '1';
        form.querySelectorAll('input[name="first_name"], input[name="phone"], input[name="address_line_1"], input[name="city"], select[name="state"]').forEach(el => el.required = true);
        if (savedSection) {
            savedSection.querySelectorAll('input[name="address_id"]').forEach(el => el.required = false);
        }
    } else {
        form.style.display = 'none';
        flag.value = '0';
        form.querySelectorAll('input, select').forEach(el => el.required = false);
    }
}

const subtotal = {{ number_format($cart->total, 2, '.', '') }};
let currentDiscount = 0;

function recalcTotal() {
    const total = Math.max(0, subtotal - currentDiscount);
    document.getElementById('orderTotalDisplay').textContent = '₦' + total.toLocaleString('en-NG', {minimumFractionDigits: 0, maximumFractionDigits: 0});
}

function applyCoupon() {
    const input = document.getElementById('couponInput');
    const btn = document.getElementById('applyCouponBtn');
    const msg = document.getElementById('couponMessage');
    const code = input.value.trim();
    if (!code) { input.focus(); return; }
    btn.disabled = true; btn.textContent = '...';
    msg.style.display = 'none';
    fetch('{{ route("checkout.applyCoupon") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ coupon_code: code })
    })
    .then(res => res.json().then(data => ({ ok: res.ok, data })))
    .then(({ ok, data }) => {
        btn.disabled = false; btn.textContent = 'Apply';
        msg.style.display = 'block';
        if (ok && data.success) {
            currentDiscount = data.discount;
            document.getElementById('couponCodeHidden').value = data.coupon_code;
            const section = document.getElementById('couponSection');
            section.innerHTML = `
                <div class="ck-coupon-applied">
                    <span class="ck-applied-label"><i class="fas fa-tag"></i> ${data.coupon_code}</span>
                    <button type="button" class="ck-remove-coupon" onclick="removeCoupon()" title="Remove">&times;</button>
                </div>
                <input type="hidden" name="coupon_code" id="couponCodeHidden" value="${data.coupon_code}">
                <div id="couponMessage" class="ck-coupon-msg success" style="display:block;"><i class="fas fa-check-circle"></i> ${data.message}</div>
            `;
            document.getElementById('discountRow').style.display = 'flex';
            document.getElementById('discountDisplay').textContent = '-₦' + data.discount.toLocaleString('en-NG');
            recalcTotal();
        } else {
            msg.className = 'ck-coupon-msg error';
            msg.innerHTML = `<i class="fas fa-times-circle"></i> ${data.message || 'Invalid coupon code.'}`;
        }
    })
    .catch(() => {
        btn.disabled = false; btn.textContent = 'Apply';
        msg.style.display = 'block';
        msg.className = 'ck-coupon-msg error';
        msg.innerHTML = '<i class="fas fa-times-circle"></i> Something went wrong. Try again.';
    });
}

function removeCoupon() {
    currentDiscount = 0;
    document.getElementById('discountRow').style.display = 'none';
    document.getElementById('couponSection').innerHTML = `
        <div class="ck-coupon-row">
            <div class="ck-coupon-icon"><i class="fas fa-tag"></i></div>
            <input type="text" id="couponInput" class="ck-coupon-input" placeholder="Coupon code" maxlength="50">
            <button type="button" id="applyCouponBtn" class="ck-coupon-btn" onclick="applyCoupon()">Apply</button>
        </div>
        <input type="hidden" name="coupon_code" id="couponCodeHidden" value="">
        <div id="couponMessage" class="ck-coupon-msg" style="display:none;"></div>
    `;
    recalcTotal();
}

// Place order button loading state
document.getElementById('checkoutForm').addEventListener('submit', function() {
    const btn = document.getElementById('placeOrderBtn');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Processing...</span>';
    btn.disabled = true;
});
</script>
@endsection
