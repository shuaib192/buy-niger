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
<div class="finances-page">
    {{-- Page Header --}}
    <div class="page-header-premium">
        <div>
            <h1 class="page-title">Financial Overview</h1>
            <p class="page-subtitle">Monitor your earnings, payouts, and linked bank accounts.</p>
        </div>
        <button class="btn-primary-premium" data-bs-toggle="modal" data-bs-target="#payoutModal" {{ $stats['available_balance'] < 1000 ? 'disabled' : '' }}>
            <i class="fas fa-money-bill-transfer"></i> Withdraw to Bank
        </button>
    </div>

    {{-- Auto-Payout Info Banner --}}
    <div class="info-banner mb-4">
        <div class="info-banner-icon"><i class="fas fa-bolt"></i></div>
        <div class="info-banner-text">
            <strong>Automatic Earnings</strong> — Your balance is automatically credited when you mark an order as "Delivered". Withdraw anytime to your bank account.
        </div>
    </div>

    @if(session('success'))
        <div class="alert-premium alert-success mb-4">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert-premium alert-error mb-4">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="stats-grid">
        <div class="stat-card stat-total">
            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            <div class="stat-data">
                <span class="stat-label">Total Earnings</span>
                <h2 class="stat-value">₦{{ number_format($stats['total_earned'], 2) }}</h2>
                <span class="stat-hint">Gross revenue from delivered orders</span>
            </div>
        </div>
        <div class="stat-card stat-pending">
            <div class="stat-icon"><i class="fas fa-clock"></i></div>
            <div class="stat-data">
                <span class="stat-label">In Transit</span>
                <h2 class="stat-value">₦{{ number_format($stats['pending_payout'], 2) }}</h2>
                <span class="stat-hint">Orders not yet delivered</span>
            </div>
        </div>
        <div class="stat-card stat-available">
            <div class="stat-icon"><i class="fas fa-wallet"></i></div>
            <div class="stat-data">
                <span class="stat-label">Available Balance</span>
                <h2 class="stat-value">₦{{ number_format($stats['available_balance'], 2) }}</h2>
                <span class="stat-hint">Ready for withdrawal</span>
            </div>
        </div>
    </div>

    <div class="content-grid">
        {{-- Payout History --}}
        <div class="premium-card main-col">
            <div class="card-header-premium">
                <h3>Payout History</h3>
            </div>
            <div class="card-body-premium p-0">
                <div class="table-responsive">
                    <table class="premium-table">
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
                            @forelse($payouts as $payout)
                                <tr>
                                    <td><span class="ref-code">{{ $payout->reference }}</span></td>
                                    <td>
                                        @if(($payout->payment_method ?? '') === 'auto_credit')
                                            <span class="type-badge type-auto"><i class="fas fa-bolt"></i> Auto</span>
                                        @else
                                            <span class="type-badge type-manual"><i class="fas fa-university"></i> Withdrawal</span>
                                        @endif
                                    </td>
                                    <td><span class="amount-text">₦{{ number_format($payout->amount) }}</span></td>
                                    <td>
                                        @php
                                            $statusClass = match($payout->status) {
                                                'completed' => 'dot-success',
                                                'pending' => 'dot-warning',
                                                'failed' => 'dot-danger',
                                                default => 'dot-secondary'
                                            };
                                        @endphp
                                        <span class="status-dot {{ $statusClass }}"></span>
                                        <span class="status-text">{{ ucfirst($payout->status) }}</span>
                                    </td>
                                    <td><span class="date-text">{{ $payout->created_at->format('M d, Y') }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="empty-row">No payout history yet. Deliver orders to start earning!</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($payouts->hasPages())
                <div class="pagination-bar">{{ $payouts->links() }}</div>
                @endif
            </div>
        </div>

        {{-- Sidebar: Bank Accounts --}}
        <div class="side-col">
            <div class="premium-card">
                <div class="card-header-premium d-flex justify-content-between align-items-center">
                    <h3>Bank Accounts</h3>
                    <a href="{{ route('vendor.settings') }}" class="manage-link">Manage</a>
                </div>
                <div class="card-body-premium">
                    @forelse($bankDetails as $bank)
                        <div class="bank-card {{ $bank->is_primary ? 'bank-primary' : '' }}">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="bank-label">Linked Account</span>
                                @if($bank->is_primary)
                                    <span class="primary-badge">Primary</span>
                                @endif
                            </div>
                            <h4 class="bank-name">{{ $bank->bank_name }}</h4>
                            <div class="bank-number">{{ $bank->account_number }}</div>
                            <div class="bank-holder">{{ $bank->account_name }}</div>
                        </div>
                    @empty
                        <div class="empty-state-sm">
                            <i class="fas fa-university"></i>
                            <p>No bank accounts linked. <a href="{{ route('vendor.settings') }}">Add one now</a></p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- How it Works --}}
            <div class="premium-card mt-3">
                <div class="card-header-premium"><h3>How Payouts Work</h3></div>
                <div class="card-body-premium">
                    <div class="steps-list">
                        <div class="step-item">
                            <div class="step-num">1</div>
                            <div>Customer places an order</div>
                        </div>
                        <div class="step-item">
                            <div class="step-num">2</div>
                            <div>You process & deliver the order</div>
                        </div>
                        <div class="step-item">
                            <div class="step-num">3</div>
                            <div>Mark as "Delivered" → balance auto-credited</div>
                        </div>
                        <div class="step-item">
                            <div class="step-num">4</div>
                            <div>Withdraw to your bank anytime</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Withdrawal Modal --}}
<div class="modal fade" id="payoutModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:24px;">
            <form action="{{ route('vendor.payouts.request') }}" method="POST">
                @csrf
                <div class="modal-header border-0 p-4 pb-0">
                    <h5 style="font-weight:800; color:#0f172a;">Withdraw to Bank</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="withdrawal-box mb-4">
                        <div class="withdrawal-label">Available for Withdrawal</div>
                        <div class="withdrawal-amount">₦{{ number_format($stats['available_balance'], 2) }}</div>
                    </div>

                    <div class="form-group-premium mb-4">
                        <label>Amount (₦)</label>
                        <div class="input-with-prefix">
                            <span class="prefix">₦</span>
                            <input type="number" name="amount" class="form-input-premium" min="1000" max="{{ $stats['available_balance'] }}" required placeholder="0.00">
                        </div>
                        <small class="form-hint">Minimum withdrawal: ₦1,000</small>
                    </div>

                    <div class="form-group-premium">
                        <label>Destination Account</label>
                        <select name="bank_detail_id" class="form-select-premium" required>
                            @foreach($bankDetails as $bank)
                                <option value="{{ $bank->id }}">{{ $bank->bank_name }} ({{ $bank->account_number }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn-outline-premium" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-primary-premium" {{ $bankDetails->isEmpty() ? 'disabled' : '' }}>
                        <i class="fas fa-paper-plane"></i> Withdraw
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .finances-page { animation: fadeInUp 0.4s ease; }
    @keyframes fadeInUp { from { opacity:0; transform: translateY(12px); } to { opacity:1; transform: translateY(0); } }

    .page-header-premium { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 16px; }
    .page-title { font-size: 24px; font-weight: 800; color: #0f172a; margin: 0 0 4px; letter-spacing: -0.02em; }
    .page-subtitle { color: #64748b; font-size: 14px; margin: 0; font-weight: 500; }

    .btn-primary-premium { display: inline-flex; align-items: center; gap: 8px; padding: 10px 22px; background: #0066FF; color: white; border: none; border-radius: 14px; font-weight: 700; font-size: 14px; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 14px rgba(0,102,255,0.25); text-decoration: none; }
    .btn-primary-premium:hover { background: #0052cc; transform: translateY(-1px); }
    .btn-primary-premium:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
    .btn-outline-premium { display: inline-flex; align-items: center; gap: 8px; padding: 10px 22px; background: white; color: #475569; border: 1px solid #e2e8f0; border-radius: 14px; font-weight: 700; font-size: 14px; cursor: pointer; transition: all 0.2s; }

    /* Info Banner */
    .info-banner { display: flex; align-items: center; gap: 14px; padding: 14px 20px; background: linear-gradient(135deg, #eff6ff 0%, #f0fdf4 100%); border: 1px solid #dbeafe; border-radius: 16px; }
    .info-banner-icon { width: 36px; height: 36px; background: #0066FF; color: white; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; }
    .info-banner-text { font-size: 13px; color: #334155; line-height: 1.5; }
    .info-banner-text strong { color: #0f172a; }

    /* Alerts */
    .alert-premium { display: flex; align-items: center; gap: 12px; padding: 14px 20px; border-radius: 14px; font-size: 14px; font-weight: 600; }
    .alert-success { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
    .alert-error { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }

    /* Stats */
    .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }
    .stat-card { position: relative; overflow: hidden; border-radius: 20px; padding: 24px; background: white; border: 1px solid #f1f5f9; box-shadow: 0 1px 4px rgba(0,0,0,0.03); transition: all 0.3s; display: flex; align-items: flex-start; gap: 16px; }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.06); }
    .stat-icon { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
    .stat-total { border-left: 4px solid #10b981; }
    .stat-total .stat-icon { background: #ecfdf5; color: #10b981; }
    .stat-pending { border-left: 4px solid #f59e0b; }
    .stat-pending .stat-icon { background: #fffbeb; color: #f59e0b; }
    .stat-available { background: linear-gradient(135deg, #0066FF 0%, #0052cc 100%); border: none; color: white; }
    .stat-available .stat-icon { background: rgba(255,255,255,0.2); color: white; }
    .stat-available .stat-label, .stat-available .stat-hint { color: rgba(255,255,255,0.7); }
    .stat-available .stat-value { color: white; }
    .stat-label { font-size: 12px; font-weight: 600; color: #64748b; display: block; margin-bottom: 4px; }
    .stat-value { font-size: 26px; font-weight: 800; color: #0f172a; margin: 0 0 4px; }
    .stat-hint { font-size: 11px; color: #94a3b8; }

    /* Content Grid */
    .content-grid { display: grid; grid-template-columns: 1fr 320px; gap: 20px; align-items: start; }
    .main-col { min-width: 0; }

    /* Cards */
    .premium-card { background: white; border: 1px solid #f1f5f9; border-radius: 20px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.03); }
    .card-header-premium { padding: 18px 24px; border-bottom: 1px solid #f1f5f9; }
    .card-header-premium h3 { font-size: 15px; font-weight: 700; color: #0f172a; margin: 0; }
    .card-body-premium { padding: 20px 24px; }
    .p-0 { padding: 0 !important; }
    .manage-link { font-size: 11px; font-weight: 700; color: #0066FF; text-transform: uppercase; letter-spacing: 0.08em; text-decoration: none; }

    /* Table */
    .premium-table { width: 100%; border-collapse: collapse; }
    .premium-table th { background: #f8fafc; color: #64748b; font-size: 10px; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 700; padding: 12px 20px; border: none; text-align: left; }
    .premium-table td { padding: 14px 20px; border-bottom: 1px solid #f8fafc; vertical-align: middle; font-size: 14px; }
    .premium-table tr:hover { background: #fafbfc; }
    .ref-code { font-family: 'JetBrains Mono', monospace; font-size: 12px; color: #64748b; }
    .amount-text { font-weight: 800; color: #0f172a; }
    .date-text { font-size: 13px; color: #64748b; }
    .empty-row { text-align: center; padding: 40px 20px !important; color: #94a3b8; }
    .type-badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: 700; }
    .type-auto { background: #ecfdf5; color: #059669; }
    .type-manual { background: #eff6ff; color: #0066FF; }
    .status-dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; margin-right: 6px; }
    .dot-success { background: #22c55e; }
    .dot-warning { background: #f59e0b; }
    .dot-danger { background: #ef4444; }
    .dot-secondary { background: #94a3b8; }
    .status-text { font-size: 13px; font-weight: 600; color: #475569; }
    .pagination-bar { padding: 16px 24px; border-top: 1px solid #f1f5f9; }

    /* Bank Cards */
    .bank-card { padding: 16px; border-radius: 16px; background: #f8fafc; border: 1px solid #e2e8f0; margin-bottom: 12px; }
    .bank-card:last-child { margin-bottom: 0; }
    .bank-card.bank-primary { background: #eff6ff; border-color: #93c5fd; }
    .bank-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #94a3b8; }
    .primary-badge { background: #0066FF; color: white; font-size: 9px; font-weight: 800; padding: 2px 8px; border-radius: 6px; text-transform: uppercase; }
    .bank-name { font-size: 15px; font-weight: 800; color: #0f172a; margin: 0 0 4px; }
    .bank-number { font-family: 'JetBrains Mono', monospace; font-size: 14px; color: #475569; letter-spacing: 0.05em; margin-bottom: 4px; }
    .bank-holder { font-size: 12px; color: #64748b; }
    .empty-state-sm { text-align: center; padding: 24px 16px; }
    .empty-state-sm i { font-size: 28px; color: #cbd5e1; display: block; margin-bottom: 8px; }
    .empty-state-sm p { font-size: 13px; color: #94a3b8; margin: 0; }
    .empty-state-sm a { color: #0066FF; font-weight: 600; }

    /* Steps List */
    .steps-list { display: flex; flex-direction: column; gap: 12px; }
    .step-item { display: flex; align-items: center; gap: 12px; font-size: 13px; color: #475569; line-height: 1.4; }
    .step-num { width: 24px; height: 24px; border-radius: 8px; background: #0066FF; color: white; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800; flex-shrink: 0; }

    /* Modal */
    .withdrawal-box { background: #eff6ff; padding: 18px; border-radius: 16px; border: 1px solid #dbeafe; }
    .withdrawal-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #0066FF; margin-bottom: 4px; }
    .withdrawal-amount { font-size: 28px; font-weight: 800; color: #0066FF; }
    .form-group-premium label { display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #64748b; margin-bottom: 8px; }
    .form-input-premium, .form-select-premium { width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 14px; font-weight: 600; background: #fafbfc; color: #0f172a; outline: none; transition: all 0.2s; }
    .form-input-premium:focus, .form-select-premium:focus { border-color: #0066FF; background: white; box-shadow: 0 0 0 3px rgba(0,102,255,0.1); }
    .form-hint { font-size: 11px; color: #94a3b8; margin-top: 4px; display: block; }
    .input-with-prefix { display: flex; align-items: center; background: #fafbfc; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; }
    .input-with-prefix .prefix { padding: 10px 0 10px 14px; font-weight: 800; color: #94a3b8; }
    .input-with-prefix .form-input-premium { border: none; background: transparent; }

    @media (max-width: 1024px) { .content-grid { grid-template-columns: 1fr; } }
    @media (max-width: 768px) { .stats-grid { grid-template-columns: 1fr; } .page-header-premium { flex-direction: column; align-items: flex-start; } }
</style>
@endsection
