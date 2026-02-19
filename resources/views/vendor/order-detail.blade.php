{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    View: Vendor Order Detail (Premium)
--}}
@extends('layouts.app')

@section('title', 'Order #' . $orderItem->order->order_number)
@section('page_title', 'Order Detail')

@section('sidebar')
    @include('vendor.partials.sidebar')
@endsection

@section('content')
<div class="order-detail-page">
    {{-- Breadcrumb --}}
    <div class="page-breadcrumb">
        <a href="{{ route('vendor.orders') }}" class="breadcrumb-link"><i class="fas fa-arrow-left"></i> Back to Orders</a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">#{{ $orderItem->order->order_number }}</span>
    </div>

    {{-- Order Header --}}
    <div class="order-header-card">
        <div class="order-header-left">
            <h1 class="order-title">Order #{{ $orderItem->order->order_number }}</h1>
            <div class="order-meta">
                <span class="meta-item"><i class="far fa-calendar-alt"></i> {{ $orderItem->created_at->format('M d, Y \\a\\t h:i A') }}</span>
                <span class="meta-item"><i class="far fa-user"></i> {{ $orderItem->order->user->name ?? 'Guest' }}</span>
            </div>
        </div>
        <div class="order-header-right">
            @php
                $badgeMap = ['delivered'=>'badge-emerald','cancelled'=>'badge-red','processing'=>'badge-blue','shipped'=>'badge-indigo','pending'=>'badge-amber'];
            @endphp
            <span class="status-badge-lg {{ $badgeMap[$orderItem->status] ?? 'badge-gray' }}">
                <span class="badge-dot"></span>{{ ucfirst($orderItem->status) }}
            </span>
            <button onclick="window.print()" class="btn-icon-action no-print" title="Print Invoice"><i class="fas fa-print"></i></button>
        </div>
    </div>

    <div class="row g-4">
        {{-- Main Content --}}
        <div class="col-lg-8">
            {{-- Progress Stepper --}}
            @if($orderItem->status != 'cancelled')
            <div class="premium-card mb-4 no-print">
                <div class="card-body-premium p-4">
                    @php
                        $steps = ['pending', 'processing', 'shipped', 'delivered'];
                        $currentStep = array_search($orderItem->status, $steps);
                        $icons = ['fas fa-inbox','fas fa-cog','fas fa-truck','fas fa-check-double'];
                    @endphp
                    <div class="stepper-premium">
                        @foreach($steps as $index => $step)
                            <div class="stepper-item {{ $index <= $currentStep ? 'completed' : '' }} {{ $index == $currentStep ? 'current' : '' }}">
                                <div class="stepper-icon">
                                    @if($index < $currentStep)
                                        <i class="fas fa-check"></i>
                                    @else
                                        <i class="{{ $icons[$index] }}"></i>
                                    @endif
                                </div>
                                <span class="stepper-label">{{ ucfirst($step) }}</span>
                                @if(!$loop->last) <div class="stepper-line"></div> @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Order Items --}}
            <div class="premium-card mb-4">
                <div class="card-header-premium"><h3>Order Items</h3></div>
                <div class="card-body-premium p-0">
                    <div class="order-item-row">
                        <div class="d-flex align-items-center gap-3 flex-grow-1">
                            @if($orderItem->product && $orderItem->product->primary_image_url)
                                <img src="{{ $orderItem->product->primary_image_url }}" alt="" class="order-product-img">
                            @else
                                <div class="order-product-img-placeholder"><i class="fas fa-box"></i></div>
                            @endif
                            <div>
                                <div class="product-title">{{ $orderItem->product_name }}</div>
                                <div class="product-sku">SKU: {{ $orderItem->product->sku ?? 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="item-qty">× {{ $orderItem->quantity }}</div>
                        <div class="item-price">₦{{ number_format($orderItem->price) }}</div>
                        <div class="item-total">₦{{ number_format($orderItem->subtotal) }}</div>
                    </div>
                    <div class="order-totals">
                        <div class="total-line"><span>Subtotal</span><span>₦{{ number_format($orderItem->subtotal) }}</span></div>
                        <div class="total-line"><span>Shipping</span><span>₦0.00</span></div>
                        <div class="total-line total-final"><span>Total</span><span class="text-primary">₦{{ number_format($orderItem->subtotal) }}</span></div>
                    </div>
                </div>
            </div>

            {{-- Shipping & Info --}}
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="premium-card h-100">
                        <div class="card-header-premium"><h3><i class="fas fa-map-marker-alt text-primary mr-2"></i>Shipping Address</h3></div>
                        <div class="card-body-premium">
                            @if($orderItem->order->address)
                                <div class="address-block">
                                    <strong>{{ $orderItem->order->address->first_name }} {{ $orderItem->order->address->last_name }}</strong>
                                    <p>{{ $orderItem->order->address->address_line1 }}</p>
                                    @if($orderItem->order->address->address_line2) <p>{{ $orderItem->order->address->address_line2 }}</p> @endif
                                    <p>{{ $orderItem->order->address->city }}, {{ $orderItem->order->address->state }}</p>
                                    @if($orderItem->order->address->phone)
                                        <div class="address-phone"><i class="fas fa-phone-alt"></i> {{ $orderItem->order->address->phone }}</div>
                                    @endif
                                </div>
                            @else
                                <div class="text-center text-muted py-3"><i class="fas fa-map-marked-alt fa-2x mb-2 d-block text-secondary-300"></i>No address provided</div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="premium-card h-100">
                        <div class="card-header-premium"><h3><i class="fas fa-receipt text-primary mr-2"></i>Payment Info</h3></div>
                        <div class="card-body-premium">
                            <div class="info-list">
                                <div class="info-row"><span class="info-label">Method</span><span class="info-value">{{ strtoupper($orderItem->order->payment_method ?? 'Not Set') }}</span></div>
                                <div class="info-row">
                                    <span class="info-label">Payment</span>
                                    <span class="status-badge {{ ($orderItem->order->payment_status ?? 'pending') == 'paid' ? 'badge-emerald' : 'badge-amber' }}">
                                        <span class="badge-dot"></span>{{ ucfirst($orderItem->order->payment_status ?? 'Pending') }}
                                    </span>
                                </div>
                                <div class="info-row"><span class="info-label">Tracking</span><span class="info-value">{{ $orderItem->tracking_number ?: '—' }}</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4 no-print">
            {{-- Manage Order --}}
            <div class="premium-card mb-4 border-primary-highlight">
                <div class="card-header-premium bg-primary-subtle"><h3><i class="fas fa-sliders-h mr-2"></i>Manage Order</h3></div>
                <div class="card-body-premium">
                    <form action="{{ route('vendor.orders.status', $orderItem->id) }}" method="POST">
                        @csrf
                        <div class="form-group-premium mb-3">
                            <label>Update Status</label>
                            <select name="status" class="form-select-premium" onchange="toggleTracking(this.value)">
                                <option value="pending" {{ $orderItem->status == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                <option value="processing" {{ $orderItem->status == 'processing' ? 'selected' : '' }}>⚙️ Processing</option>
                                <option value="shipped" {{ $orderItem->status == 'shipped' ? 'selected' : '' }}>🚚 Shipped</option>
                                <option value="delivered" {{ $orderItem->status == 'delivered' ? 'selected' : '' }}>✅ Delivered</option>
                                <option value="cancelled" {{ $orderItem->status == 'cancelled' ? 'selected' : '' }}>❌ Cancelled</option>
                            </select>
                        </div>
                        <div id="tracking-field" class="{{ $orderItem->status == 'shipped' ? '' : 'd-none' }} mb-3">
                            <div class="form-group-premium">
                                <label>Tracking Number</label>
                                <input type="text" name="tracking_number" class="form-input-premium" value="{{ $orderItem->tracking_number }}" placeholder="e.g. DHL-X892">
                            </div>
                        </div>
                        <button type="submit" class="btn-primary-premium w-100">
                            <i class="fas fa-save mr-2"></i>Apply Update
                        </button>
                    </form>
                </div>
            </div>

            {{-- Customer Card --}}
            <div class="premium-card">
                <div class="card-header-premium"><h3><i class="far fa-user mr-2"></i>Customer</h3></div>
                <div class="card-body-premium">
                    <div class="customer-info-block">
                        <div class="customer-avatar-lg">{{ substr($orderItem->order->user->name ?? 'G', 0, 1) }}</div>
                        <div>
                            <div class="customer-name-lg">{{ $orderItem->order->user->name ?? 'Guest User' }}</div>
                            <small class="text-muted">ID: #{{ $orderItem->order->user_id ?? 'N/A' }}</small>
                        </div>
                    </div>
                    <div class="customer-actions">
                        <a href="mailto:{{ $orderItem->order->user->email ?? '' }}" class="action-btn"><i class="fas fa-envelope"></i> Email</a>
                        @if($orderItem->order->address && $orderItem->order->address->phone)
                            <a href="tel:{{ $orderItem->order->address->phone }}" class="action-btn"><i class="fas fa-phone-alt"></i> Call</a>
                        @endif
                        @if($orderItem->order->user)
                            <form action="{{ route('vendor.messages.start', $orderItem->order->user_id) }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="subject" value="Re: Order #{{ $orderItem->order->order_number }}">
                                <button type="submit" class="action-btn action-btn-primary"><i class="fas fa-comments"></i> Message</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .order-detail-page { animation: fadeInUp 0.4s ease; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }

    .page-breadcrumb { display: flex; align-items: center; gap: 8px; margin-bottom: 20px; font-size: 13px; }
    .breadcrumb-link { color: #0066FF; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; } .breadcrumb-link:hover { color: #004ecc; }
    .breadcrumb-separator { color: #cbd5e1; }
    .breadcrumb-current { color: #64748b; font-weight: 500; }

    .order-header-card { display: flex; justify-content: space-between; align-items: center; background: white; border: 1px solid #f1f5f9; border-radius: 20px; padding: 24px 28px; margin-bottom: 24px; box-shadow: 0 1px 4px rgba(0,0,0,0.03); flex-wrap: wrap; gap: 16px; }
    .order-title { font-size: 22px; font-weight: 800; color: #0f172a; margin: 0 0 6px; letter-spacing: -0.02em; }
    .order-meta { display: flex; gap: 16px; flex-wrap: wrap; }
    .meta-item { font-size: 13px; color: #64748b; font-weight: 500; display: inline-flex; align-items: center; gap: 6px; }
    .order-header-right { display: flex; align-items: center; gap: 12px; }

    .status-badge-lg { display: inline-flex; align-items: center; gap: 8px; padding: 8px 18px; border-radius: 24px; font-size: 14px; font-weight: 700; }
    .badge-dot { width: 7px; height: 7px; border-radius: 50%; }
    .badge-emerald { background: #ecfdf5; color: #059669; } .badge-emerald .badge-dot { background: #059669; }
    .badge-red { background: #fef2f2; color: #dc2626; } .badge-red .badge-dot { background: #dc2626; }
    .badge-blue { background: #eff6ff; color: #2563eb; } .badge-blue .badge-dot { background: #2563eb; }
    .badge-indigo { background: #eef2ff; color: #4f46e5; } .badge-indigo .badge-dot { background: #4f46e5; }
    .badge-amber { background: #fffbeb; color: #d97706; } .badge-amber .badge-dot { background: #d97706; }
    .status-badge { display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; }

    .btn-icon-action { width: 42px; height: 42px; border-radius: 12px; border: 1px solid #e2e8f0; background: white; display: flex; align-items: center; justify-content: center; color: #64748b; cursor: pointer; transition: all 0.2s; }
    .btn-icon-action:hover { background: #f8fafc; color: #0f172a; border-color: #cbd5e1; }

    /* Cards */
    .premium-card { background: white; border: 1px solid #f1f5f9; border-radius: 20px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.03); }
    .card-header-premium { padding: 18px 24px; border-bottom: 1px solid #f1f5f9; }
    .card-header-premium h3 { font-size: 15px; font-weight: 700; color: #0f172a; margin: 0; display: flex; align-items: center; }
    .card-body-premium { padding: 24px; }
    .border-primary-highlight { border-color: #dbeafe; }
    .bg-primary-subtle { background: #f0f7ff; }

    /* Stepper */
    .stepper-premium { display: flex; align-items: flex-start; justify-content: space-between; position: relative; }
    .stepper-item { display: flex; flex-direction: column; align-items: center; flex: 1; position: relative; z-index: 1; }
    .stepper-icon { width: 48px; height: 48px; border-radius: 50%; background: #f1f5f9; color: #94a3b8; display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 700; margin-bottom: 8px; transition: all 0.3s; border: 3px solid transparent; }
    .stepper-item.completed .stepper-icon { background: #0066FF; color: white; border-color: rgba(0,102,255,0.2); box-shadow: 0 4px 12px rgba(0,102,255,0.2); }
    .stepper-item.current .stepper-icon { animation: pulse 2s infinite; }
    @keyframes pulse { 0%,100% { box-shadow: 0 4px 12px rgba(0,102,255,0.2); } 50% { box-shadow: 0 4px 24px rgba(0,102,255,0.4); } }
    .stepper-label { font-size: 12px; font-weight: 600; color: #94a3b8; }
    .stepper-item.completed .stepper-label { color: #0066FF; }
    .stepper-line { position: absolute; top: 24px; left: 50%; width: 100%; height: 3px; background: #f1f5f9; z-index: -1; }
    .stepper-item.completed .stepper-line { background: #0066FF; }

    /* Order Items */
    .order-item-row { display: flex; align-items: center; justify-content: space-between; padding: 20px 24px; gap: 16px; flex-wrap: wrap; }
    .order-product-img { width: 64px; height: 64px; border-radius: 14px; object-fit: cover; border: 1px solid #f1f5f9; }
    .order-product-img-placeholder { width: 64px; height: 64px; border-radius: 14px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 20px; }
    .product-title { font-weight: 700; color: #0f172a; font-size: 15px; }
    .product-sku { font-size: 12px; color: #94a3b8; margin-top: 2px; }
    .item-qty { font-size: 14px; color: #64748b; font-weight: 600; }
    .item-price { font-size: 14px; color: #64748b; }
    .item-total { font-size: 16px; font-weight: 800; color: #0f172a; }
    .order-totals { border-top: 1px solid #f1f5f9; padding: 16px 24px; }
    .total-line { display: flex; justify-content: space-between; padding: 8px 0; font-size: 14px; color: #64748b; }
    .total-line span:last-child { font-weight: 700; color: #0f172a; }
    .total-final { border-top: 2px solid #f1f5f9; padding-top: 12px; margin-top: 4px; font-size: 18px; }
    .total-final span:last-child { font-size: 20px; }
    .text-primary { color: #0066FF !important; }

    /* Address & Info */
    .address-block strong { display: block; color: #0f172a; font-size: 15px; margin-bottom: 8px; }
    .address-block p { color: #475569; font-size: 14px; margin-bottom: 4px; }
    .address-phone { margin-top: 12px; color: #0066FF; font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 8px; }
    .info-list { display: flex; flex-direction: column; gap: 12px; }
    .info-row { display: flex; justify-content: space-between; align-items: center; }
    .info-label { font-size: 13px; color: #94a3b8; font-weight: 500; }
    .info-value { font-size: 14px; font-weight: 700; color: #0f172a; }

    /* Form */
    .form-group-premium label { display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #64748b; margin-bottom: 8px; }
    .form-select-premium, .form-input-premium { width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 14px; font-weight: 600; background: #fafbfc; color: #0f172a; outline: none; transition: all 0.2s; }
    .form-select-premium:focus, .form-input-premium:focus { border-color: #0066FF; background: white; box-shadow: 0 0 0 3px rgba(0,102,255,0.1); }
    .btn-primary-premium { display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 12px; background: #0066FF; color: white; border: none; border-radius: 14px; font-weight: 700; font-size: 14px; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 14px rgba(0,102,255,0.25); }
    .btn-primary-premium:hover { background: #0052cc; transform: translateY(-1px); box-shadow: 0 6px 20px rgba(0,102,255,0.35); }

    /* Customer */
    .customer-info-block { display: flex; align-items: center; gap: 14px; margin-bottom: 20px; }
    .customer-avatar-lg { width: 52px; height: 52px; border-radius: 16px; background: linear-gradient(135deg, #667eea, #764ba2); color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 20px; flex-shrink: 0; }
    .customer-name-lg { font-weight: 700; color: #0f172a; font-size: 16px; }
    .customer-actions { display: flex; flex-direction: column; gap: 8px; }
    .action-btn { display: flex; align-items: center; gap: 10px; padding: 10px 16px; border-radius: 12px; border: 1px solid #e2e8f0; background: white; color: #475569; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.2s; width: 100%; }
    .action-btn:hover { background: #f8fafc; border-color: #cbd5e1; color: #1e293b; }
    .action-btn i { width: 16px; text-align: center; }
    .action-btn-primary { border-color: #dbeafe; color: #0066FF; background: #f0f7ff; }
    .action-btn-primary:hover { background: #dbeafe; color: #004ecc; }

    .gap-3 { gap: 1rem; } .gap-4 { gap: 1.5rem; } .g-4 > * { padding: 0.75rem; }

    @media print { .no-print { display: none !important; } .premium-card { border: none !important; box-shadow: none !important; } }
    @media (max-width: 768px) { .order-item-row { flex-direction: column; align-items: flex-start; } .order-header-card { flex-direction: column; align-items: flex-start; } }
</style>

<script>
    function toggleTracking(status) {
        const field = document.getElementById('tracking-field');
        if (status === 'shipped') { field.classList.remove('d-none'); } else { field.classList.add('d-none'); }
    }
</script>
@endsection
