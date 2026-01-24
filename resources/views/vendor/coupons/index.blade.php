@extends('layouts.app')

@section('title', 'Promotions & Coupons')

@section('sidebar')
    @include('vendor.partials.sidebar')
@endsection

@section('content')
<div class="coupons-container py-4">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="h3 font-bold text-secondary-900 mb-1">Coupons & Promotions</h1>
            <p class="text-secondary-500 mb-0">Create and manage discount codes to boost your store sales.</p>
        </div>
        <button class="btn btn-primary px-4 shadow-primary-200" data-bs-toggle="modal" data-bs-target="#createCouponModal">
            <i class="fas fa-plus mr-2"></i> Create New Coupon
        </button>
    </div>

    <div class="row">
        @forelse($coupons as $coupon)
            <div class="col-md-4 mb-4">
                <div class="premium-coupon-card {{ $coupon->isValid() ? 'active' : 'inactive' }}">
                    <div class="coupon-header d-flex justify-content-between align-items-center p-4">
                        <div class="coupon-type-badge uppercase">{{ $coupon->type == 'percent' ? 'Percentage' : 'Fixed Amount' }}</div>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input toggle-status" id="status{{ $coupon->id }}" {{ $coupon->is_active ? 'checked' : '' }} data-id="{{ $coupon->id }}">
                            <label class="custom-control-label" for="status{{ $coupon->id }}"></label>
                        </div>
                    </div>
                    
                    <div class="coupon-body p-4 pt-0 text-center">
                        <div class="discount-value mb-1">
                            {{ $coupon->type == 'percent' ? $coupon->value . '%' : '₦' . number_format($coupon->value) }}
                            <span class="off-text">OFF</span>
                        </div>
                        <div class="coupon-code-box mb-3">
                            <span class="code">{{ $coupon->code }}</span>
                            <button class="copy-btn" onclick="copyCode('{{ $coupon->code }}')" title="Copy Code">
                                <i class="far fa-copy"></i>
                            </button>
                        </div>
                        
                        <div class="coupon-details">
                            <div class="detail-item d-flex justify-content-between mb-2">
                                <span class="label">Min. Spend</span>
                                <span class="val font-bold">₦{{ number_format($coupon->min_spend ?? 0) }}</span>
                            </div>
                            <div class="detail-item d-flex justify-content-between mb-2">
                                <span class="label">Usage</span>
                                <span class="val font-bold">{{ $coupon->used_count }} / {{ $coupon->usage_limit ?? '∞' }}</span>
                            </div>
                            <div class="detail-item d-flex justify-content-between">
                                <span class="label">Expires</span>
                                <span class="val font-bold {{ $coupon->expires_at && $coupon->expires_at->isPast() ? 'text-danger' : '' }}">
                                    {{ $coupon->expires_at ? $coupon->expires_at->format('M d, Y') : 'Never' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="coupon-footer p-3 border-top d-flex justify-content-center">
                        <form action="{{ route('vendor.coupons.destroy', $coupon->id) }}" method="POST" onsubmit="return confirm('Delete this coupon?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-link text-danger text-xs font-bold uppercase letter-spacing-1">
                                <i class="fas fa-trash-alt mr-1"></i> Delete Coupon
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5 bg-white rounded-24 shadow-sm border">
                    <div class="empty-icon-box mb-4">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <h3 class="font-bold text-secondary-900 mb-2">No active coupons</h3>
                    <p class="text-secondary-500 max-w-sm mx-auto mb-4">Reward your customers with discounts! Create your first coupon to drive more conversions.</p>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCouponModal">
                        Create Coupon Now
                    </button>
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Create Coupon Modal -->
<div class="modal fade" id="createCouponModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-24">
            <form action="{{ route('vendor.coupons.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 p-4 pb-0">
                    <h5 class="font-bold text-secondary-900">Create New Coupon</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <label class="text-xs font-bold text-secondary-500 uppercase mb-2 d-block">Coupon Code</label>
                            <div class="premium-input-wrapper">
                                <i class="fas fa-tag text-secondary-400 mr-2"></i>
                                <input type="text" name="code" class="form-control-premium" required placeholder="SUMMER2026" maxlength="20">
                            </div>
                            <small class="text-secondary-400 text-xs">Customers enter this code at checkout.</small>
                        </div>
                        
                        <div class="col-6 mb-4">
                            <label class="text-xs font-bold text-secondary-500 uppercase mb-2 d-block">Discount Type</label>
                            <select name="type" class="form-control-premium custom-select" required>
                                <option value="percent">Percentage (%)</option>
                                <option value="fixed">Fixed Amount (₦)</option>
                            </select>
                        </div>
                        
                        <div class="col-6 mb-4">
                            <label class="text-xs font-bold text-secondary-500 uppercase mb-2 d-block">Discount Value</label>
                            <input type="number" name="value" class="form-control-premium bordered" required placeholder="10" min="0">
                        </div>

                        <div class="col-6 mb-4">
                            <label class="text-xs font-bold text-secondary-500 uppercase mb-2 d-block">Min. Spend (₦)</label>
                            <input type="number" name="min_spend" class="form-control-premium bordered" placeholder="0" min="0">
                        </div>
                        
                        <div class="col-6 mb-4">
                            <label class="text-xs font-bold text-secondary-500 uppercase mb-2 d-block">Usage Limit</label>
                            <input type="number" name="usage_limit" class="form-control-premium bordered" placeholder="Unlimited" min="1">
                        </div>

                        <div class="col-12">
                            <label class="text-xs font-bold text-secondary-500 uppercase mb-2 d-block">Expiry Date</label>
                            <input type="date" name="expires_at" class="form-control-premium bordered">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light bg-secondary-50 px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-5 shadow-primary-200">Launch Coupon</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .rounded-24 { border-radius: 24px; }
    .font-bold { font-weight: 700; }
    .uppercase { text-transform: uppercase; }
    .letter-spacing-1 { letter-spacing: 0.1em; }
    
    .premium-coupon-card {
        background: white;
        border-radius: 24px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.03);
        transition: all 0.3s ease;
        overflow: hidden;
        position: relative;
    }
    .premium-coupon-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px -8px rgba(0, 0, 0, 0.08); }
    
    .premium-coupon-card.inactive { opacity: 0.7; grayscale: 1; }
    
    .coupon-type-badge {
        font-size: 10px;
        font-weight: 800;
        color: #64748b;
        background: #f1f5f9;
        padding: 4px 10px;
        border-radius: 20px;
    }
    
    .discount-value { font-size: 36px; font-weight: 900; color: #0066FF; line-height: 1; }
    .discount-value .off-text { font-size: 14px; font-weight: 700; color: #64748b; margin-left: -5px; }
    
    .coupon-code-box {
        display: inline-flex;
        align-items: center;
        background: #eff6ff;
        border: 2px dashed #0066FF;
        padding: 8px 16px;
        border-radius: 12px;
    }
    .coupon-code-box .code { font-family: 'JetBrains Mono', monospace; font-weight: 800; color: #1e3a8a; letter-spacing: 0.05em; font-size: 18px; margin-right: 10px; }
    .copy-btn { border: none; background: none; color: #0066FF; font-size: 14px; cursor: pointer; padding: 0; outline: none; }
    
    .detail-item .label { font-size: 12px; color: #94a3b8; font-weight: 500; }
    .detail-item .val { font-size: 13px; color: #334155; }
    
    .empty-icon-box {
        width: 100px;
        height: 100px;
        background: #eff6ff;
        color: #0066FF;
        font-size: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }
    
    .premium-input-wrapper { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; display: flex; align-items: center; padding: 5px 15px; }
    .form-control-premium { border: none; background: transparent; padding: 10px 0; font-weight: 700; font-size: 15px; width: 100%; outline: none; }
    .form-control-premium.bordered { border: 1px solid #e2e8f0; border-radius: 12px; padding-left: 15px; background: #f8fafc; }
    .form-control-premium:focus { border-color: #0066FF; background: white; }
    
    .shadow-primary-200 { box-shadow: 0 4px 14px 0 rgba(0, 102, 255, 0.3); }
</style>

<script>
    function copyCode(code) {
        navigator.clipboard.writeText(code).then(() => {
            alert('Coupon code copied to clipboard!');
        });
    }

    document.querySelectorAll('.toggle-status').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const id = this.dataset.id;
            const active = this.checked ? 1 : 0;
            
            fetch(`{{ route('vendor.coupons.toggle', '') }}/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ active })
            }).then(response => {
                if(!response.ok) this.checked = !this.checked;
            });
        });
    });
</script>
@endsection
