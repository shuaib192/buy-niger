{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    View: Vendor Coupons (Premium)
--}}
@extends('layouts.app')

@section('title', 'Coupons')
@section('page_title', 'Coupon Manager')

@section('sidebar')
    @include('vendor.partials.sidebar')
@endsection

@section('content')
<div class="coupons-page">
    {{-- Page Header --}}
    <div class="page-header-premium">
        <div>
            <h1 class="page-title">Coupon Manager</h1>
            <p class="page-subtitle">Create and manage discount coupons for your products.</p>
        </div>
        <button class="btn-primary-premium" id="showCreateForm">
            <i class="fas fa-plus"></i> New Coupon
        </button>
    </div>

    {{-- Create Coupon Form (hidden by default) --}}
    <div class="premium-card mb-4" id="createCouponCard" style="display:none;">
        <div class="card-header-premium bg-primary-subtle">
            <h3><i class="fas fa-tag mr-2"></i>Create New Coupon</h3>
        </div>
        <div class="card-body-premium">
            <form action="{{ route('vendor.coupons.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="form-group-premium">
                            <label>Coupon Code</label>
                            <input type="text" name="code" class="form-input-premium" placeholder="e.g. SAVE20" required 
                                   style="text-transform: uppercase;" maxlength="20">
                            <small class="form-hint">Letters, numbers only. Max 20 chars.</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group-premium">
                            <label>Discount Type</label>
                            <select name="type" class="form-select-premium" required>
                                <option value="percent">Percentage (%)</option>
                                <option value="fixed">Fixed Amount (₦)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group-premium">
                            <label>Value</label>
                            <input type="number" name="value" class="form-input-premium" placeholder="e.g. 15" required min="0" step="0.01">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group-premium">
                            <label>Min. Spend (₦)</label>
                            <input type="number" name="min_spend" class="form-input-premium" placeholder="Optional" min="0">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group-premium">
                            <label>Usage Limit</label>
                            <input type="number" name="usage_limit" class="form-input-premium" placeholder="Unlimited" min="1">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group-premium">
                            <label>Expires On</label>
                            <input type="date" name="expires_at" class="form-input-premium">
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn-primary-premium"><i class="fas fa-check mr-2"></i>Create Coupon</button>
                    <button type="button" class="btn-outline-premium" id="cancelCreate">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Success / Error Alerts --}}
    @if(session('success'))
        <div class="alert-premium alert-success mb-4">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="alert-premium alert-error mb-4">
            <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
        </div>
    @endif

    {{-- Coupons Grid --}}
    @if($coupons->count() > 0)
        <div class="coupons-grid">
            @foreach($coupons as $coupon)
                <div class="coupon-card {{ $coupon->is_active ? '' : 'coupon-inactive' }}">
                    <div class="coupon-left">
                        <div class="coupon-value-badge">
                            @if($coupon->type == 'percent')
                                <span class="big-value">{{ intval($coupon->value) }}</span>
                                <span class="big-unit">%</span>
                            @else
                                <span class="big-unit" style="font-size:14px;">₦</span>
                                <span class="big-value" style="font-size:24px;">{{ number_format($coupon->value) }}</span>
                            @endif
                        </div>
                        <span class="coupon-type-label">{{ $coupon->type == 'percent' ? 'Percentage' : 'Fixed' }}</span>
                    </div>
                    <div class="coupon-right">
                        <div class="coupon-code-text">{{ $coupon->code }}</div>
                        <div class="coupon-meta-list">
                            @if($coupon->min_spend)
                                <span class="coupon-meta"><i class="fas fa-shopping-cart"></i> Min ₦{{ number_format($coupon->min_spend) }}</span>
                            @endif
                            @if($coupon->usage_limit)
                                <span class="coupon-meta"><i class="fas fa-users"></i> {{ $coupon->times_used ?? 0 }}/{{ $coupon->usage_limit }}</span>
                            @else
                                <span class="coupon-meta"><i class="fas fa-infinity"></i> Unlimited</span>
                            @endif
                            @if($coupon->expires_at)
                                <span class="coupon-meta {{ \Carbon\Carbon::parse($coupon->expires_at)->isPast() ? 'text-danger' : '' }}">
                                    <i class="far fa-calendar"></i> {{ \Carbon\Carbon::parse($coupon->expires_at)->format('M d, Y') }}
                                </span>
                            @endif
                        </div>
                        <div class="coupon-actions">
                            <button class="toggle-btn {{ $coupon->is_active ? 'active' : '' }}" 
                                    onclick="toggleCoupon({{ $coupon->id }}, this)"
                                    title="{{ $coupon->is_active ? 'Deactivate' : 'Activate' }}">
                                <span class="toggle-track"><span class="toggle-thumb"></span></span>
                            </button>
                            <form action="{{ route('vendor.coupons.destroy', $coupon->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this coupon?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="delete-btn" title="Delete"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="premium-card">
            <div class="empty-state-premium">
                <div class="empty-icon"><i class="fas fa-ticket-alt"></i></div>
                <h4>No coupons yet</h4>
                <p>Create your first coupon to offer discounts to your customers.</p>
                <button class="btn-primary-premium mt-3" onclick="document.getElementById('showCreateForm').click()">
                    <i class="fas fa-plus mr-2"></i>Create Your First Coupon
                </button>
            </div>
        </div>
    @endif
</div>

<style>
    .coupons-page { animation: fadeInUp 0.4s ease; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }

    .page-header-premium { display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 16px; }
    .page-title { font-size: 24px; font-weight: 800; color: #0f172a; margin: 0 0 4px; letter-spacing: -0.02em; }
    .page-subtitle { color: #64748b; font-size: 14px; margin: 0; font-weight: 500; }

    .btn-primary-premium { display: inline-flex; align-items: center; gap: 8px; padding: 10px 22px; background: #0066FF; color: white; border: none; border-radius: 14px; font-weight: 700; font-size: 14px; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 14px rgba(0,102,255,0.25); text-decoration: none; }
    .btn-primary-premium:hover { background: #0052cc; transform: translateY(-1px); box-shadow: 0 6px 20px rgba(0,102,255,0.35); }
    .btn-outline-premium { display: inline-flex; align-items: center; gap: 8px; padding: 10px 22px; background: white; color: #475569; border: 1px solid #e2e8f0; border-radius: 14px; font-weight: 700; font-size: 14px; cursor: pointer; transition: all 0.2s; }
    .btn-outline-premium:hover { background: #f8fafc; border-color: #cbd5e1; }

    /* Cards */
    .premium-card { background: white; border: 1px solid #f1f5f9; border-radius: 20px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.03); }
    .card-header-premium { padding: 18px 24px; border-bottom: 1px solid #f1f5f9; }
    .card-header-premium h3 { font-size: 15px; font-weight: 700; color: #0f172a; margin: 0; display: flex; align-items: center; }
    .card-body-premium { padding: 24px; }
    .bg-primary-subtle { background: #f0f7ff; }

    /* Form */
    .form-group-premium { margin-bottom: 0; }
    .form-group-premium label { display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #64748b; margin-bottom: 8px; }
    .form-select-premium, .form-input-premium { width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 14px; font-weight: 600; background: #fafbfc; color: #0f172a; outline: none; transition: all 0.2s; }
    .form-select-premium:focus, .form-input-premium:focus { border-color: #0066FF; background: white; box-shadow: 0 0 0 3px rgba(0,102,255,0.1); }
    .form-hint { font-size: 11px; color: #94a3b8; margin-top: 4px; display: block; }
    .g-3 > * { padding: 0.5rem; }

    /* Alerts */
    .alert-premium { display: flex; align-items: center; gap: 12px; padding: 14px 20px; border-radius: 14px; font-size: 14px; font-weight: 600; animation: slideDown 0.3s ease; }
    .alert-success { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
    .alert-error { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    @keyframes slideDown { from { opacity:0; transform: translateY(-8px); } to { opacity:1; transform: translateY(0); } }

    /* Coupon Grid */
    .coupons-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(380px, 1fr)); gap: 16px; }

    .coupon-card { display: flex; background: white; border: 1px solid #f1f5f9; border-radius: 20px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.03); transition: all 0.3s ease; }
    .coupon-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.06); }
    .coupon-card.coupon-inactive { opacity: 0.55; }
    .coupon-card.coupon-inactive:hover { opacity: 0.75; }

    .coupon-left { flex-shrink: 0; width: 110px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 20px 10px; position: relative; }
    .coupon-left::after { content: ''; position: absolute; right: -8px; top: 0; bottom: 0; width: 16px; background: radial-gradient(circle at 0 12px, transparent 8px, white 8px); background-size: 16px 24px; }
    .coupon-value-badge { display: flex; align-items: baseline; gap: 2px; color: white; }
    .big-value { font-size: 34px; font-weight: 800; line-height: 1; }
    .big-unit { font-size: 18px; font-weight: 700; opacity: 0.8; }
    .coupon-type-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: rgba(255,255,255,0.7); margin-top: 4px; }

    .coupon-right { flex-grow: 1; padding: 16px 20px; display: flex; flex-direction: column; justify-content: center; gap: 8px; }
    .coupon-code-text { font-size: 18px; font-weight: 800; color: #0f172a; font-family: 'JetBrains Mono', 'Fira Code', monospace; letter-spacing: 0.1em; }
    .coupon-meta-list { display: flex; flex-wrap: wrap; gap: 12px; }
    .coupon-meta { display: inline-flex; align-items: center; gap: 5px; font-size: 12px; color: #64748b; font-weight: 500; }
    .coupon-meta i { font-size: 11px; color: #94a3b8; }
    .text-danger { color: #dc2626 !important; }

    .coupon-actions { display: flex; align-items: center; gap: 10px; margin-top: 4px; }

    /* Toggle Switch */
    .toggle-btn { background: none; border: none; cursor: pointer; padding: 0; }
    .toggle-track { display: block; width: 40px; height: 22px; background: #cbd5e1; border-radius: 12px; position: relative; transition: background 0.3s; }
    .toggle-btn.active .toggle-track { background: #22c55e; }
    .toggle-thumb { position: absolute; top: 2px; left: 2px; width: 18px; height: 18px; background: white; border-radius: 50%; box-shadow: 0 1px 4px rgba(0,0,0,0.15); transition: transform 0.3s; }
    .toggle-btn.active .toggle-thumb { transform: translateX(18px); }

    .delete-btn { width: 32px; height: 32px; border-radius: 10px; border: 1px solid #fecaca; background: #fef2f2; color: #ef4444; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 12px; transition: all 0.2s; }
    .delete-btn:hover { background: #fee2e2; border-color: #f87171; color: #dc2626; }

    /* Empty State */
    .empty-state-premium { text-align: center; padding: 60px 20px; }
    .empty-icon { width: 80px; height: 80px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 32px; color: #94a3b8; }
    .empty-state-premium h4 { font-weight: 700; color: #0f172a; margin-bottom: 4px; }
    .empty-state-premium p { color: #94a3b8; font-size: 14px; max-width: 360px; margin: 0 auto; }

    @media (max-width: 768px) {
        .page-header-premium { flex-direction: column; align-items: flex-start; }
        .coupons-grid { grid-template-columns: 1fr; }
        .coupon-left { width: 90px; padding: 14px 8px; }
        .big-value { font-size: 26px; }
    }
</style>

<script>
    document.getElementById('showCreateForm').addEventListener('click', function() {
        const card = document.getElementById('createCouponCard');
        card.style.display = card.style.display === 'none' ? 'block' : 'none';
        if (card.style.display === 'block') {
            card.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });

    document.getElementById('cancelCreate').addEventListener('click', function() {
        document.getElementById('createCouponCard').style.display = 'none';
    });

    function toggleCoupon(id, btn) {
        const isActive = btn.classList.contains('active');
        fetch(`{{ url('/vendor/coupons') }}/${id}/toggle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ active: !isActive })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                btn.classList.toggle('active');
                btn.closest('.coupon-card').classList.toggle('coupon-inactive');
            }
        })
        .catch(err => console.error(err));
    }
</script>
@endsection
