{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    View: Vendor Finance Dashboard (Premium)
--}}
@extends('layouts.app')

@section('title', 'Finances & Payouts')
@section('page_title', 'Finances & Payouts')

@section('sidebar')
    @include('vendor.partials.sidebar')
@endsection

@section('content')
<div class="fin-page">

    {{-- Hero Balance Section --}}
    <div class="fin-hero">
        <div class="fin-hero-left">
            <div class="fin-hero-label">Available Balance</div>
            <div class="fin-hero-amount">₦{{ number_format($stats['available_balance'], 2) }}</div>
            <div class="fin-hero-hint">Ready for withdrawal to your bank</div>
        </div>
        <div class="fin-hero-right">
            <button class="fin-withdraw-btn" data-bs-toggle="modal" data-bs-target="#payoutModal" {{ $stats['available_balance'] < 500 ? 'disabled' : '' }}>
                <i class="fas fa-arrow-right"></i>
                <span>Withdraw</span>
            </button>
            @if($stats['available_balance'] < 500)
                <div class="fin-hero-note">Min. ₦500 to withdraw</div>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="fin-alert fin-alert-ok"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="fin-alert fin-alert-err"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif

    {{-- Earnings Breakdown --}}
    <div class="fin-metrics">
        <div class="fin-metric">
            <div class="fin-metric-top">
                <div class="fin-metric-icon fin-metric-green"><i class="fas fa-trending-up"></i></div>
                <span class="fin-metric-label">Total Earned</span>
            </div>
            <div class="fin-metric-value">₦{{ number_format($stats['total_earned'], 2) }}</div>
            <div class="fin-metric-sub">From delivered orders</div>
        </div>
        <div class="fin-metric">
            <div class="fin-metric-top">
                <div class="fin-metric-icon fin-metric-amber"><i class="fas fa-shipping-fast"></i></div>
                <span class="fin-metric-label">In Transit</span>
            </div>
            <div class="fin-metric-value">₦{{ number_format($stats['pending_payout'], 2) }}</div>
            <div class="fin-metric-sub">Awaiting delivery confirmation</div>
        </div>
    </div>

    {{-- Info Tip --}}
    <div class="fin-tip">
        <i class="fas fa-lightbulb"></i>
        <span>Your balance grows automatically when you mark orders as <strong>"Delivered"</strong>. Withdraw to your bank anytime!</span>
    </div>

    {{-- Main Content --}}
    <div class="fin-grid">

        {{-- Payout History --}}
        <div class="fin-card fin-card-main">
            <div class="fin-card-head">
                <h3>Payout History</h3>
            </div>

            @if($payouts->count() > 0)
            <div class="fin-table-wrap">
                <table class="fin-table">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payouts as $payout)
                        <tr>
                            <td>
                                <span class="fin-ref">{{ $payout->reference }}</span>
                            </td>
                            <td>
                                @if(($payout->payment_method ?? '') === 'auto_credit')
                                    <span class="fin-type fin-type-auto"><i class="fas fa-bolt"></i> Auto</span>
                                @else
                                    <span class="fin-type fin-type-bank"><i class="fas fa-university"></i> Bank</span>
                                @endif
                            </td>
                            <td><span class="fin-amount">₦{{ number_format($payout->amount) }}</span></td>
                            <td>
                                @php
                                    $statusClass = match($payout->status) {
                                        'completed' => 'fin-st-ok',
                                        'pending' => 'fin-st-wait',
                                        'processing' => 'fin-st-proc',
                                        'failed' => 'fin-st-fail',
                                        default => 'fin-st-wait'
                                    };
                                @endphp
                                <span class="fin-status {{ $statusClass }}">{{ ucfirst($payout->status) }}</span>
                            </td>
                            <td><span class="fin-date">{{ $payout->created_at->format('M d, Y') }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($payouts->hasPages())
            <div class="fin-pagination">{{ $payouts->links() }}</div>
            @endif
            @else
            <div class="fin-empty">
                <div class="fin-empty-icon"><i class="fas fa-receipt"></i></div>
                <h4>No payouts yet</h4>
                <p>Deliver orders to earn, then withdraw here!</p>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="fin-sidebar">

            {{-- Bank Accounts --}}
            <div class="fin-card">
                <div class="fin-card-head" style="display:flex; justify-content:space-between; align-items:center;">
                    <h3>Bank Accounts</h3>
                    <a href="{{ route('vendor.settings') }}" class="fin-manage-link">Manage →</a>
                </div>
                <div class="fin-card-body">
                    @forelse($bankDetails as $bank)
                    <div class="fin-bank {{ $bank->is_primary ? 'fin-bank-primary' : '' }}">
                        <div class="fin-bank-top">
                            <span class="fin-bank-icon"><i class="fas fa-university"></i></span>
                            @if($bank->is_primary)
                                <span class="fin-primary-tag">Primary</span>
                            @endif
                        </div>
                        <div class="fin-bank-name">{{ $bank->bank_name }}</div>
                        <div class="fin-bank-num">{{ $bank->account_number }}</div>
                        <div class="fin-bank-holder">{{ $bank->account_name }}</div>
                    </div>
                    @empty
                    <div class="fin-empty-sm">
                        <i class="fas fa-credit-card"></i>
                        <p>No bank linked yet. <a href="{{ route('vendor.settings') }}">Add one</a></p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- How it Works --}}
            <div class="fin-card">
                <div class="fin-card-head"><h3>How It Works</h3></div>
                <div class="fin-card-body">
                    <div class="fin-steps">
                        <div class="fin-step">
                            <div class="fin-step-dot">1</div>
                            <div class="fin-step-text">Customer places an order</div>
                        </div>
                        <div class="fin-step">
                            <div class="fin-step-dot">2</div>
                            <div class="fin-step-text">You process & deliver the order</div>
                        </div>
                        <div class="fin-step">
                            <div class="fin-step-dot">3</div>
                            <div class="fin-step-text">Mark as "Delivered" → balance credited</div>
                        </div>
                        <div class="fin-step">
                            <div class="fin-step-dot">4</div>
                            <div class="fin-step-text">Tap <strong>Withdraw</strong> → processed within 2 working days</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Withdrawal Modal (logic untouched) --}}
<div class="modal fade" id="payoutModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border:none; border-radius:24px; overflow:hidden; box-shadow:0 25px 60px rgba(0,0,0,0.15);">
            <form action="{{ route('vendor.payouts.request') }}" method="POST">
                @csrf
                <div class="fin-modal-head">
                    <div>
                        <h5>Withdraw Funds</h5>
                        <p>Processed within 2 working days</p>
                    </div>
                    <button type="button" class="fin-modal-close" data-bs-dismiss="modal">&times;</button>
                </div>
                <div class="fin-modal-body">
                    <div class="fin-modal-balance">
                        <div class="fin-modal-balance-label">Available</div>
                        <div class="fin-modal-balance-val">₦{{ number_format($stats['available_balance'], 2) }}</div>
                    </div>

                    <div class="fin-field">
                        <label>Withdrawal Amount</label>
                        <div class="fin-input-wrap">
                            <span class="fin-input-prefix">₦</span>
                            <input type="number" name="amount" min="200" max="{{ $stats['available_balance'] }}" required placeholder="Enter amount">
                        </div>
                        <div style="display:flex; justify-content:space-between; margin-top:4px;">
                            <small style="color:#94a3b8; margin:0;">Minimum: ₦200</small>
                            <small style="color:#f59e0b; margin:0; font-weight:700;"><i class="fas fa-clock"></i> Takes 2 working days</small>
                        </div>
                    </div>

                    <div class="fin-field">
                        <label>Send To</label>
                        <select name="bank_detail_id" required>
                            @foreach($bankDetails as $bank)
                                <option value="{{ $bank->id }}">{{ $bank->bank_name }} — {{ $bank->account_number }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="fin-modal-foot">
                    <button type="button" data-bs-dismiss="modal" class="fin-btn-cancel">Cancel</button>
                    <button type="submit" class="fin-btn-send" {{ $bankDetails->isEmpty() ? 'disabled' : '' }}>
                        <i class="fas fa-paper-plane"></i> Withdraw Now
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Finances page CSS is page-specific (fin-hero, fin-metrics, fin-card, fin-modal) --}}
<style>
.fin-page { max-width:1100px; animation: finFadeIn 0.5s ease; }
@keyframes finFadeIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }

/* ---- Hero Balance ---- */
.fin-hero {
    background: linear-gradient(135deg, #0a0e27 0%, #1a1f4e 50%, #0d2847 100%);
    border-radius: 24px; padding: 32px 36px; color: white;
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 24px; position: relative; overflow: hidden;
}
.fin-hero::before {
    content: ''; position: absolute; top: -50%; right: -20%;
    width: 300px; height: 300px; background: rgba(255,255,255,0.03);
    border-radius: 50%; pointer-events: none;
}
.fin-hero::after {
    content: ''; position: absolute; bottom: -60%; left: 10%;
    width: 200px; height: 200px; background: rgba(0,102,255,0.12);
    border-radius: 50%; pointer-events: none;
}
.fin-hero-label { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; color: rgba(255,255,255,0.5); margin-bottom: 6px; }
.fin-hero-amount { font-size: 2.5rem; font-weight: 900; letter-spacing: -1px; line-height: 1.1; margin-bottom: 6px; }
.fin-hero-hint { font-size: 0.8125rem; color: rgba(255,255,255,0.45); }
.fin-hero-right { display: flex; flex-direction: column; align-items: flex-end; gap: 8px; position: relative; z-index: 1; }
.fin-withdraw-btn {
    display: flex; align-items: center; gap: 10px;
    padding: 14px 28px; background: #fff; color: #0a0e27;
    border: none; border-radius: 16px; font-weight: 800; font-size: 0.9375rem;
    cursor: pointer; transition: all 0.25s;
    box-shadow: 0 4px 20px rgba(0,0,0,0.2);
}
.fin-withdraw-btn:hover { transform: translateY(-2px) scale(1.02); box-shadow: 0 8px 30px rgba(0,0,0,0.3); }
.fin-withdraw-btn:disabled { opacity: 0.35; cursor: not-allowed; transform: none; }
.fin-withdraw-btn i { font-size: 1rem; }
.fin-hero-note { font-size: 0.6875rem; color: rgba(255,255,255,0.35); }

/* ---- Alerts ---- */
.fin-alert { display: flex; align-items: center; gap: 10px; padding: 14px 20px; border-radius: 14px; font-size: 0.875rem; font-weight: 600; margin-bottom: 20px; }
.fin-alert-ok { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
.fin-alert-err { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }

/* ---- Metrics ---- */
.fin-metrics { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-bottom: 20px; }
.fin-metric {
    background: #fff; border: 1px solid #f1f5f9; border-radius: 20px;
    padding: 22px 24px; transition: all 0.25s;
}
.fin-metric:hover { box-shadow: 0 6px 20px rgba(0,0,0,0.04); transform: translateY(-2px); }
.fin-metric-top { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
.fin-metric-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 0.875rem; flex-shrink: 0; }
.fin-metric-green { background: #ecfdf5; color: #10b981; }
.fin-metric-amber { background: #fffbeb; color: #f59e0b; }
.fin-metric-label { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #94a3b8; }
.fin-metric-value { font-size: 1.5rem; font-weight: 900; color: #0f172a; margin-bottom: 2px; letter-spacing: -0.5px; }
.fin-metric-sub { font-size: 0.75rem; color: #94a3b8; }

/* ---- Tip Bar ---- */
.fin-tip {
    display: flex; align-items: center; gap: 12px;
    padding: 12px 18px; background: #fffbeb; border: 1px solid #fde68a;
    border-radius: 14px; margin-bottom: 24px; font-size: 0.8125rem; color: #92400e;
}
.fin-tip i { color: #f59e0b; font-size: 1rem; flex-shrink: 0; }

/* ---- Grid ---- */
.fin-grid { display: grid; grid-template-columns: 1fr 320px; gap: 20px; align-items: start; }

/* ---- Cards ---- */
.fin-card {
    background: #fff; border: 1px solid #f1f5f9; border-radius: 20px;
    overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}
.fin-card + .fin-card { margin-top: 16px; }
.fin-card-head { padding: 18px 24px; border-bottom: 1px solid #f1f5f9; }
.fin-card-head h3 { font-size: 0.9375rem; font-weight: 800; color: #0f172a; margin: 0; }
.fin-card-body { padding: 20px 24px; }
.fin-card-main { min-width: 0; }
.fin-manage-link { font-size: 0.6875rem; font-weight: 700; color: #0066FF; text-transform: uppercase; letter-spacing: 0.08em; text-decoration: none; }

/* ---- Payout Table ---- */
.fin-table-wrap { overflow-x: auto; }
.fin-table { width: 100%; border-collapse: collapse; min-width: 500px; }
.fin-table th {
    background: #f8fafc; color: #94a3b8; font-size: 0.625rem;
    text-transform: uppercase; letter-spacing: 0.12em; font-weight: 800;
    padding: 11px 20px; text-align: left; border: none;
}
.fin-table td { padding: 14px 20px; border-bottom: 1px solid #f8fafc; font-size: 0.875rem; vertical-align: middle; }
.fin-table tr:hover td { background: #fafbfe; }
.fin-ref { font-family: 'JetBrains Mono', 'Courier New', monospace; font-size: 0.75rem; color: #64748b; background: #f1f5f9; padding: 3px 8px; border-radius: 6px; }
.fin-amount { font-weight: 800; color: #0f172a; }
.fin-date { font-size: 0.8125rem; color: #94a3b8; }

.fin-type { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 8px; font-size: 0.6875rem; font-weight: 700; }
.fin-type-auto { background: #ecfdf5; color: #059669; }
.fin-type-bank { background: #eff6ff; color: #3b82f6; }

.fin-status { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 0.6875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; }
.fin-st-ok { background: #dcfce7; color: #16a34a; }
.fin-st-wait { background: #fef9c3; color: #a16207; }
.fin-st-proc { background: #e0e7ff; color: #4f46e5; }
.fin-st-fail { background: #fee2e2; color: #dc2626; }

.fin-pagination { padding: 14px 24px; border-top: 1px solid #f1f5f9; }

.fin-empty { text-align: center; padding: 48px 24px; }
.fin-empty-icon { width: 64px; height: 64px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }
.fin-empty-icon i { font-size: 1.5rem; color: #cbd5e1; }
.fin-empty h4 { font-size: 1rem; font-weight: 700; color: #334155; margin: 0 0 6px; }
.fin-empty p { font-size: 0.8125rem; color: #94a3b8; margin: 0; }

/* ---- Bank Cards ---- */
.fin-bank {
    padding: 16px; border-radius: 16px; background: #f8fafc;
    border: 1px solid #e2e8f0; margin-bottom: 10px; transition: all 0.2s;
}
.fin-bank:last-child { margin-bottom: 0; }
.fin-bank:hover { border-color: #cbd5e1; }
.fin-bank-primary { background: linear-gradient(135deg, #eff6ff, #f0f9ff); border-color: #93c5fd; }
.fin-bank-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
.fin-bank-icon { width: 32px; height: 32px; background: #e2e8f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #64748b; font-size: 0.8rem; }
.fin-bank-primary .fin-bank-icon { background: #dbeafe; color: #3b82f6; }
.fin-primary-tag { font-size: 0.5625rem; font-weight: 800; background: #0066FF; color: white; padding: 2px 8px; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.06em; }
.fin-bank-name { font-size: 0.875rem; font-weight: 800; color: #0f172a; margin-bottom: 2px; }
.fin-bank-num { font-family: 'JetBrains Mono', monospace; font-size: 0.8125rem; color: #475569; letter-spacing: 0.04em; margin-bottom: 2px; }
.fin-bank-holder { font-size: 0.75rem; color: #94a3b8; }

.fin-empty-sm { text-align: center; padding: 20px 16px; }
.fin-empty-sm i { font-size: 1.5rem; color: #cbd5e1; display: block; margin-bottom: 8px; }
.fin-empty-sm p { font-size: 0.8125rem; color: #94a3b8; margin: 0; }
.fin-empty-sm a { color: #0066FF; font-weight: 700; }

/* ---- Steps ---- */
.fin-steps { display: flex; flex-direction: column; gap: 14px; }
.fin-step { display: flex; align-items: center; gap: 12px; font-size: 0.8125rem; color: #475569; }
.fin-step-dot {
    width: 26px; height: 26px; border-radius: 50%;
    background: linear-gradient(135deg, #0066FF, #0052cc); color: white;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.6875rem; font-weight: 800; flex-shrink: 0;
}

/* ---- Modal ---- */
.fin-modal-head {
    display: flex; justify-content: space-between; align-items: flex-start;
    padding: 28px 28px 0; 
}
.fin-modal-head h5 { font-size: 1.125rem; font-weight: 800; color: #0f172a; margin: 0 0 2px; }
.fin-modal-head p { font-size: 0.8125rem; color: #94a3b8; margin: 0; }
.fin-modal-close { background: #f1f5f9; border: none; width: 32px; height: 32px; border-radius: 50%; font-size: 1.25rem; color: #64748b; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
.fin-modal-close:hover { background: #e2e8f0; }

.fin-modal-body { padding: 24px 28px; }
.fin-modal-balance {
    background: linear-gradient(135deg, #0a0e27, #1a1f4e); color: white;
    padding: 20px; border-radius: 16px; margin-bottom: 24px; text-align: center;
}
.fin-modal-balance-label { font-size: 0.6875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: rgba(255,255,255,0.5); margin-bottom: 4px; }
.fin-modal-balance-val { font-size: 1.75rem; font-weight: 900; }

.fin-field { margin-bottom: 20px; }
.fin-field label { display: block; font-size: 0.6875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #64748b; margin-bottom: 8px; }
.fin-field small { font-size: 0.6875rem; color: #94a3b8; margin-top: 4px; display: block; }
.fin-input-wrap {
    display: flex; align-items: center; background: #f8fafc;
    border: 2px solid #e2e8f0; border-radius: 14px; overflow: hidden; transition: all 0.2s;
}
.fin-input-wrap:focus-within { border-color: #0066FF; background: #fff; box-shadow: 0 0 0 4px rgba(0,102,255,0.08); }
.fin-input-prefix { padding: 0 0 0 16px; font-weight: 800; color: #94a3b8; font-size: 1rem; }
.fin-input-wrap input {
    flex: 1; border: none; background: transparent; padding: 12px 14px;
    font-size: 1rem; font-weight: 700; color: #0f172a; outline: none;
}
.fin-field select {
    width: 100%; padding: 12px 14px; border: 2px solid #e2e8f0;
    border-radius: 14px; font-size: 0.875rem; font-weight: 600;
    background: #f8fafc; color: #0f172a; outline: none; transition: all 0.2s;
}
.fin-field select:focus { border-color: #0066FF; background: white; }

.fin-modal-foot { display: flex; justify-content: flex-end; gap: 10px; padding: 0 28px 28px; }
.fin-btn-cancel {
    padding: 11px 22px; background: #f1f5f9; color: #475569;
    border: none; border-radius: 12px; font-weight: 700; font-size: 0.875rem;
    cursor: pointer; transition: all 0.2s;
}
.fin-btn-cancel:hover { background: #e2e8f0; }
.fin-btn-send {
    display: flex; align-items: center; gap: 8px;
    padding: 11px 24px; background: linear-gradient(135deg, #0066FF, #0052cc);
    color: white; border: none; border-radius: 12px; font-weight: 700; font-size: 0.875rem;
    cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 14px rgba(0,102,255,0.3);
}
.fin-btn-send:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(0,102,255,0.35); }
.fin-btn-send:disabled { opacity: 0.4; cursor: not-allowed; transform: none; }

/* ---- Responsive ---- */
@media (max-width: 1024px) {
    .fin-grid { grid-template-columns: 1fr; }
}
@media (max-width: 768px) {
    .fin-hero { flex-direction: column; align-items: flex-start; gap: 20px; padding: 24px; }
    .fin-hero-amount { font-size: 2rem; }
    .fin-hero-right { align-items: flex-start; }
    .fin-metrics { grid-template-columns: 1fr; }
    .fin-table { min-width: 450px; }
}
@media (max-width: 480px) {
    .fin-hero { padding: 20px; }
    .fin-hero-amount { font-size: 1.75rem; }
    .fin-metric-value { font-size: 1.25rem; }
    .fin-modal-body { padding: 20px; }
    .fin-modal-head { padding: 20px 20px 0; }
    .fin-modal-foot { padding: 0 20px 20px; }
}
</style>
@endsection
