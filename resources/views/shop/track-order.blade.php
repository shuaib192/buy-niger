@extends('layouts.shop')

@section('title', 'Track Your Order')

@section('content')
<div class="track-page">
    {{-- Hero Search Section --}}
    <section class="track-hero">
        <div class="container">
            <div class="track-hero-inner">
                <div class="track-hero-icon">
                    <i class="fas fa-shipping-fast"></i>
                </div>
                <h1>Track Your Order</h1>
                <p>Enter your Order Number or Tracking ID below to see live updates on your package.</p>

                <form action="{{ route('track.order') }}" method="POST" class="track-search-form" id="trackForm">
                    @csrf
                    <div class="track-input-group">
                        <i class="fas fa-search"></i>
                        <input 
                            type="text" 
                            name="order_number" 
                            placeholder="e.g. BN-67B3F... or TRK-..." 
                            required 
                            value="{{ request('order_number') }}"
                            autocomplete="off"
                        >
                        <button type="submit" id="trackBtn">
                            <span>Track</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </form>

                @if(session('error'))
                <div class="track-alert error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>{{ session('error') }}</span>
                </div>
                @endif
            </div>
        </div>
    </section>

    @if(isset($order))
    {{-- Order Result --}}
    <section class="track-result">
        <div class="container">
            {{-- Order Header Card --}}
            <div class="result-header">
                <div class="result-header-left">
                    <span class="result-label">Order Number</span>
                    <h2 class="result-order-id">#{{ $order->order_number }}</h2>
                    <span class="result-date"><i class="far fa-calendar-alt"></i> Placed {{ $order->created_at->format('M d, Y \a\t h:i A') }}</span>
                </div>
                <div class="result-header-right">
                    @php
                        $statusConfig = [
                            'pending'    => ['icon' => 'fa-clock',        'color' => '#f59e0b', 'bg' => '#fffbeb', 'text' => 'Pending'],
                            'processing' => ['icon' => 'fa-cog',          'color' => '#3b82f6', 'bg' => '#eff6ff', 'text' => 'Processing'],
                            'shipped'    => ['icon' => 'fa-truck',        'color' => '#8b5cf6', 'bg' => '#f5f3ff', 'text' => 'Shipped'],
                            'delivered'  => ['icon' => 'fa-check-circle', 'color' => '#22c55e', 'bg' => '#f0fdf4', 'text' => 'Delivered'],
                            'cancelled'  => ['icon' => 'fa-times-circle', 'color' => '#ef4444', 'bg' => '#fef2f2', 'text' => 'Cancelled'],
                        ];
                        $sc = $statusConfig[$order->status] ?? ['icon' => 'fa-info-circle', 'color' => '#64748b', 'bg' => '#f8fafc', 'text' => ucfirst($order->status)];
                    @endphp
                    <div class="status-chip" style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }};">
                        <i class="fas {{ $sc['icon'] }}"></i>
                        {{ $sc['text'] }}
                    </div>
                </div>
            </div>

            {{-- Timeline --}}
            @if(!in_array($order->status, ['cancelled', 'refunded']))
            <div class="track-timeline-card">
                <h3><i class="fas fa-route"></i> Shipment Progress</h3>
                @php
                    $steps = [
                        ['key' => 'pending',    'label' => 'Order Placed',  'icon' => 'fa-receipt',       'desc' => 'Your order has been received'],
                        ['key' => 'processing', 'label' => 'Processing',    'icon' => 'fa-boxes-packing', 'desc' => 'Your items are being prepared'],
                        ['key' => 'shipped',    'label' => 'Shipped',       'icon' => 'fa-truck-fast',    'desc' => 'Package is on its way to you'],
                        ['key' => 'delivered',  'label' => 'Delivered',     'icon' => 'fa-house-circle-check', 'desc' => 'Package has been delivered'],
                    ];
                    $statusOrder = ['pending' => 1, 'processing' => 2, 'shipped' => 3, 'delivered' => 4];
                    $currentLevel = $statusOrder[$order->status] ?? 0;
                @endphp
                <div class="timeline">
                    @foreach($steps as $i => $step)
                    @php
                        $stepLevel = $statusOrder[$step['key']];
                        $isDone = $currentLevel >= $stepLevel;
                        $isCurrent = $currentLevel == $stepLevel;
                    @endphp
                    <div class="timeline-step {{ $isDone ? 'done' : '' }} {{ $isCurrent ? 'current' : '' }}">
                        <div class="timeline-dot">
                            @if($isDone && !$isCurrent)
                                <i class="fas fa-check"></i>
                            @else
                                <i class="fas {{ $step['icon'] }}"></i>
                            @endif
                        </div>
                        @if($i < count($steps) - 1)
                        <div class="timeline-line {{ $currentLevel > $stepLevel ? 'filled' : '' }}"></div>
                        @endif
                        <div class="timeline-info">
                            <span class="timeline-label">{{ $step['label'] }}</span>
                            <span class="timeline-desc">{{ $step['desc'] }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Items --}}
            <div class="track-items-card">
                <h3><i class="fas fa-box-open"></i> Items in Your Package</h3>
                <div class="items-list">
                    @foreach($order->items as $item)
                    <div class="item-row">
                        <div class="item-image">
                            @if($item->product && $item->product->image_url)
                                <img src="{{ $item->product->image_url }}" alt="{{ $item->product_name }}">
                            @else
                                <div class="item-image-placeholder">
                                    <i class="fas fa-box"></i>
                                </div>
                            @endif
                        </div>
                        <div class="item-details">
                            <h4>{{ $item->product_name }}</h4>
                            <span class="item-qty">Qty: {{ $item->quantity }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Help --}}
            <div class="track-help">
                <i class="fas fa-headset"></i>
                <div>
                    <strong>Need help?</strong>
                    <p>If you have questions about your order, <a href="{{ route('contact') }}">contact our support team</a>.</p>
                </div>
            </div>
        </div>
    </section>
    @endif
</div>

<style>
/* ── Page ── */
.track-page { background: #f8fafc; min-height: 70vh; }

/* ── Hero ── */
.track-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #1e40af 100%);
    padding: 60px 0 80px;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.track-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, rgba(96,165,250,0.15) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
}
.track-hero-inner { position: relative; z-index: 2; max-width: 600px; margin: 0 auto; }
.track-hero-icon {
    width: 64px;
    height: 64px;
    background: rgba(96,165,250,0.15);
    border-radius: 18px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
}
.track-hero-icon i { font-size: 28px; color: #60a5fa; }
.track-hero h1 {
    color: #fff;
    font-size: 32px;
    font-weight: 800;
    margin: 0 0 10px;
    font-family: 'Outfit', sans-serif;
}
.track-hero p {
    color: rgba(255,255,255,0.6);
    font-size: 15px;
    margin: 0 0 32px;
    line-height: 1.6;
}

/* ── Search Form ── */
.track-search-form { max-width: 520px; margin: 0 auto; }
.track-input-group {
    display: flex;
    align-items: center;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 16px;
    padding: 6px;
    transition: all 0.3s;
}
.track-input-group:focus-within {
    background: rgba(255,255,255,0.15);
    border-color: rgba(96,165,250,0.5);
    box-shadow: 0 0 0 4px rgba(96,165,250,0.1);
}
.track-input-group > i {
    color: rgba(255,255,255,0.4);
    padding: 0 16px;
    font-size: 18px;
}
.track-input-group input {
    flex: 1;
    background: transparent;
    border: none;
    outline: none;
    color: #fff;
    font-size: 15px;
    font-weight: 500;
    padding: 14px 0;
}
.track-input-group input::placeholder { color: rgba(255,255,255,0.35); }
.track-input-group button {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: #fff;
    border: none;
    padding: 12px 28px;
    border-radius: 12px;
    font-weight: 700;
    font-size: 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
    white-space: nowrap;
}
.track-input-group button:hover {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(37,99,235,0.4);
}

/* ── Alert ── */
.track-alert {
    margin-top: 20px;
    padding: 14px 20px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
    font-weight: 500;
}
.track-alert.error {
    background: rgba(239,68,68,0.15);
    color: #fca5a5;
    border: 1px solid rgba(239,68,68,0.2);
}

/* ── Result Section ── */
.track-result {
    padding: 0 0 60px;
    margin-top: -40px;
    position: relative;
    z-index: 3;
}
.track-result .container { max-width: 700px; }

/* ── Result Header ── */
.result-header {
    background: #fff;
    border-radius: 20px;
    padding: 28px 32px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 4px 24px rgba(0,0,0,0.06);
    margin-bottom: 20px;
}
.result-label {
    font-size: 12px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
.result-order-id {
    font-size: 22px;
    font-weight: 800;
    color: #0f172a;
    margin: 4px 0 8px;
    font-family: 'Outfit', monospace;
}
.result-date {
    font-size: 13px;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 6px;
}
.status-chip {
    padding: 10px 20px;
    border-radius: 50px;
    font-size: 14px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* ── Timeline ── */
.track-timeline-card {
    background: #fff;
    border-radius: 20px;
    padding: 32px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.06);
    margin-bottom: 20px;
}
.track-timeline-card h3, .track-items-card h3 {
    font-size: 16px;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 28px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.track-timeline-card h3 i, .track-items-card h3 i {
    color: #3b82f6;
}
.timeline {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    position: relative;
}
.timeline-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;
    position: relative;
}
.timeline-dot {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #f1f5f9;
    border: 3px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    color: #94a3b8;
    position: relative;
    z-index: 2;
    transition: all 0.3s;
}
.timeline-step.done .timeline-dot {
    background: linear-gradient(135deg, #22c55e, #16a34a);
    border-color: #22c55e;
    color: #fff;
}
.timeline-step.current .timeline-dot {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    border-color: #3b82f6;
    color: #fff;
    box-shadow: 0 0 0 6px rgba(59,130,246,0.15);
    animation: pulse-dot 2s infinite;
}
@keyframes pulse-dot {
    0%, 100% { box-shadow: 0 0 0 6px rgba(59,130,246,0.15); }
    50% { box-shadow: 0 0 0 12px rgba(59,130,246,0.08); }
}
.timeline-line {
    position: absolute;
    top: 24px;
    left: calc(50% + 24px);
    width: calc(100% - 48px);
    height: 3px;
    background: #e2e8f0;
    z-index: 1;
}
.timeline-line.filled { background: #22c55e; }
.timeline-info {
    text-align: center;
    margin-top: 14px;
}
.timeline-label {
    display: block;
    font-size: 13px;
    font-weight: 700;
    color: #475569;
    margin-bottom: 2px;
}
.timeline-step.done .timeline-label { color: #16a34a; }
.timeline-step.current .timeline-label { color: #2563eb; }
.timeline-desc {
    font-size: 11px;
    color: #94a3b8;
    display: block;
}

/* ── Items ── */
.track-items-card {
    background: #fff;
    border-radius: 20px;
    padding: 32px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.06);
    margin-bottom: 20px;
}
.items-list { display: flex; flex-direction: column; gap: 16px; }
.item-row {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 14px 16px;
    background: #f8fafc;
    border-radius: 14px;
    border: 1px solid #f1f5f9;
    transition: all 0.2s;
}
.item-row:hover { border-color: #e2e8f0; background: #fff; }
.item-image img, .item-image-placeholder {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    object-fit: cover;
}
.item-image-placeholder {
    background: #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
    font-size: 20px;
}
.item-details h4 {
    font-size: 14px;
    font-weight: 600;
    color: #1e293b;
    margin: 0 0 4px;
}
.item-qty {
    font-size: 12px;
    color: #64748b;
    font-weight: 500;
}

/* ── Help ── */
.track-help {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 16px;
    padding: 20px 24px;
    display: flex;
    align-items: center;
    gap: 16px;
}
.track-help > i {
    font-size: 24px;
    color: #3b82f6;
}
.track-help strong {
    display: block;
    font-size: 14px;
    color: #1e40af;
    margin-bottom: 2px;
}
.track-help p {
    font-size: 13px;
    color: #3b82f6;
    margin: 0;
}
.track-help a { color: #1e40af; font-weight: 600; text-decoration: underline; }

/* ── Responsive ── */
@media (max-width: 768px) {
    .track-hero { padding: 40px 0 60px; }
    .track-hero h1 { font-size: 24px; }
    .track-hero p { font-size: 13px; }
    .track-input-group { flex-direction: column; padding: 12px; gap: 10px; }
    .track-input-group > i { display: none; }
    .track-input-group input { width: 100%; text-align: center; padding: 12px; }
    .track-input-group button { width: 100%; justify-content: center; padding: 14px; border-radius: 10px; }

    .result-header { flex-direction: column; gap: 16px; text-align: center; padding: 24px; }
    .result-header-right { width: 100%; display: flex; justify-content: center; }

    .timeline { flex-direction: column; gap: 0; align-items: stretch; }
    .timeline-step { flex-direction: row; align-items: center; gap: 16px; padding: 0; }
    .timeline-dot { width: 40px; height: 40px; min-width: 40px; font-size: 14px; }
    .timeline-info { text-align: left; margin-top: 0; }
    .timeline-line {
        position: absolute;
        top: 40px;
        left: 20px;
        width: 3px;
        height: calc(100% + 16px);
    }
    .timeline-step:last-child .timeline-line { display: none; }
    .timeline-step { padding-bottom: 24px; }

    .track-timeline-card, .track-items-card { padding: 24px; }
    .track-help { flex-direction: column; text-align: center; }
    .status-chip { font-size: 13px; padding: 8px 16px; }
}

@media (max-width: 480px) {
    .track-hero { padding: 32px 0 50px; }
    .track-hero h1 { font-size: 20px; }
    .result-order-id { font-size: 18px; }
    .track-result .container { padding: 0 12px; }
}
</style>

@push('scripts')
<script>
document.getElementById('trackForm')?.addEventListener('submit', function() {
    const btn = document.getElementById('trackBtn');
    btn.innerHTML = '<div class="spinner" style="width:16px;height:16px;border:2px solid rgba(255,255,255,0.3);border-top-color:#fff;border-radius:50%;animation:spin .6s linear infinite;"></div><span>Tracking...</span>';
    btn.disabled = true;
});
</script>
@endpush
@endsection
