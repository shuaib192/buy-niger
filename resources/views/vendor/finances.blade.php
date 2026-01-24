{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    View: Vendor Finance Dashboard
--}}
@extends('layouts.app')

@section('title', 'Finances & Payouts')
@section('page_title', 'Finances & Payouts')

@section('sidebar')
    @include('vendor.partials.sidebar')
@endsection

@section('content')
<div class="finances-container py-4">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="h3 font-bold text-secondary-900 mb-1">Financial Overview</h1>
            <p class="text-secondary-500 mb-0">Monitor your earnings, payouts, and linked bank accounts.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary bg-white shadow-sm border-0 px-4" data-toggle="modal" data-target="#payoutModal" {{ $stats['available_balance'] < 1000 ? 'disabled' : '' }}>
                <i class="fas fa-money-bill-transfer mr-2"></i> Request Payout
            </button>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row mb-5">
        <div class="col-md-4">
            <div class="premium-finance-card stats-total">
                <div class="card-content">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="icon-box-circle">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                    <div class="stats-data">
                        <span class="label">Total Lifetime Earnings</span>
                        <h2 class="value">₦{{ number_format($stats['total_earned'], 2) }}</h2>
                        <p class="sub-label">Gross revenue before payouts</p>
                    </div>
                </div>
                <div class="glow-effect"></div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="premium-finance-card stats-pending">
                <div class="card-content">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="icon-box-circle">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <div class="stats-data">
                        <span class="label">Pending (In Transit)</span>
                        <h2 class="value">₦{{ number_format($stats['pending_payout'], 2) }}</h2>
                        <p class="sub-label">Orders delivered but not yet settled</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="premium-finance-card stats-available">
                <div class="card-content">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="icon-box-circle">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                    <div class="stats-data">
                        <span class="label">Available for Withdrawal</span>
                        <h2 class="value">₦{{ number_format($stats['available_balance'], 2) }}</h2>
                        <p class="sub-label text-white-50">Settled funds ready for payout</p>
                    </div>
                </div>
                <div class="glow-effect"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Payout History -->
        <div class="col-md-8">
            <div class="dashboard-card border-0 shadow-sm">
                <div class="dashboard-card-header bg-white border-0 py-4">
                    <h3 class="h5 font-bold mb-0">Withdrawal History</h3>
                </div>
                <div class="dashboard-card-body p-0">
                    <div class="table-responsive">
                        <table class="table-premium">
                            <thead>
                                <tr>
                                    <th>Reference</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payouts as $payout)
                                    <tr>
                                        <td><span class="text-xs font-mono text-secondary-500">{{ $payout->reference }}</span></td>
                                        <td><span class="font-bold">₦{{ number_format($payout->amount) }}</span></td>
                                        <td>
                                            @php
                                                $statusClass = [
                                                    'completed' => 'status-success',
                                                    'pending' => 'status-warning',
                                                    'failed' => 'status-danger'
                                                ][$payout->status] ?? 'status-secondary';
                                            @endphp
                                            <span class="status-badge {{ $statusClass }}">
                                                {{ ucfirst($payout->status) }}
                                            </span>
                                        </td>
                                        <td><span class="text-sm text-secondary-600">{{ $payout->created_at->format('M d, Y') }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center py-5">No withdrawal history found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($payouts->hasPages())
                <div class="p-4 bg-white border-top">
                    {{ $payouts->links() }}
                </div>
                @endif
            </div>
        </div>

        <!-- Bank Accounts -->
        <div class="col-md-4">
            <div class="dashboard-card border-0 shadow-sm">
                <div class="dashboard-card-header bg-white border-0 py-4 d-flex justify-content-between align-items-center">
                    <h3 class="h5 font-bold mb-0">Bank Accounts</h3>
                    <a href="{{ route('vendor.settings') }}" class="text-xs font-bold text-primary uppercase letter-spacing-1">Manage</a>
                </div>
                <div class="dashboard-card-body pt-0">
                    @forelse($bankDetails as $bank)
                        <div class="premium-bank-card {{ $bank->is_primary ? 'primary' : '' }} mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-xs font-bold uppercase text-secondary-400">Linked Account</span>
                                @if($bank->is_primary)
                                    <span class="badge-mini">Primary</span>
                                @endif
                            </div>
                            <h4 class="bank-name mb-1">{{ $bank->bank_name }}</h4>
                            <div class="account-num mb-2">{{ $bank->account_number }}</div>
                            <div class="account-holder text-xs text-secondary-500">{{ $bank->account_name }}</div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="fas fa-university text-secondary-200 fa-3x mb-3"></i>
                            <p class="text-secondary-500 text-sm">No bank accounts linked yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Refined -->
<div class="modal fade" id="payoutModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-24">
            <form action="{{ route('vendor.payouts.request') }}" method="POST">
                @csrf
                <div class="modal-header border-0 p-4 pb-0">
                    <h5 class="font-bold text-secondary-900">Request Withdrawal</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body p-4">
                    <div class="withdrawal-info-box mb-4">
                        <div class="text-xs font-bold text-primary uppercase mb-1">Available for Payout</div>
                        <div class="h3 font-bold text-primary mb-0">₦{{ number_format($stats['available_balance'], 2) }}</div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="text-xs font-bold text-secondary-500 uppercase mb-2 d-block">Specify Amount (₦)</label>
                        <div class="premium-input-wrapper">
                            <span class="input-prefix text-secondary-400">₦</span>
                            <input type="number" name="amount" class="form-control-premium" min="1000" max="{{ $stats['available_balance'] }}" required placeholder="0.00">
                        </div>
                        <small class="text-secondary-400 text-xs">Minimum withdrawal amount: ₦1,000.00</small>
                    </div>

                    <div class="form-group">
                        <label class="text-xs font-bold text-secondary-500 uppercase mb-2 d-block">Payout Destinction</label>
                        <select name="bank_detail_id" class="form-control-premium custom-select" required>
                            @foreach($bankDetails as $bank)
                                <option value="{{ $bank->id }}">{{ $bank->bank_name }} ({{ $bank->account_number }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light bg-secondary-50 px-4" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-5 shadow-primary-200" {{ $bankDetails->isEmpty() ? 'disabled' : '' }}>Verify & Withdraw</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .rounded-24 { border-radius: 24px; }
    .font-bold { font-weight: 700; }
    
    .premium-finance-card {
        position: relative;
        overflow: hidden;
        border-radius: 24px;
        padding: 30px;
        height: 100%;
        background: white;
        border: 1px solid #f1f5f9;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.03);
        transition: all 0.3s ease;
    }
    .premium-finance-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px -8px rgba(0, 0, 0, 0.08); }
    
    .stats-total { border-left: 6px solid #10b981; }
    .stats-pending { border-left: 6px solid #f59e0b; }
    .stats-available { background: linear-gradient(135deg, #0066FF 0%, #004ecc 100%); color: white; border: none; }
    
    .icon-box-circle {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    .stats-total .icon-box-circle { background: #ecfdf5; color: #10b981; }
    .stats-pending .icon-box-circle { background: #fffbeb; color: #f59e0b; }
    .stats-available .icon-box-circle { background: rgba(255, 255, 255, 0.2); color: white; }
    
    .stats-data .label { font-size: 13px; font-weight: 600; color: #64748b; display: block; margin-bottom: 4px; }
    .stats-available .stats-data .label { color: rgba(255, 255, 255, 0.8); }
    .stats-data .value { font-size: 28px; font-weight: 800; margin-bottom: 4px; }
    .stats-data .sub-label { font-size: 11px; color: #94a3b8; }
    
    .table-premium th { background: #f8fafc; color: #64748b; font-size: 11px; text-transform: uppercase; letter-spacing: 0.1em; padding: 15px 20px; border: none; }
    .table-premium td { padding: 18px 20px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    
    .status-badge { padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    .status-success { background: #d1fae5; color: #065f46; }
    .status-warning { background: #fef3c7; color: #92400e; }
    .status-danger { background: #fee2e2; color: #991b1b; }
    
    .premium-bank-card { padding: 20px; border-radius: 18px; background: #f8fafc; border: 1px solid #e2e8f0; }
    .premium-bank-card.primary { background: #eff6ff; border-color: #0066FF; }
    .bank-name { font-size: 16px; font-weight: 800; color: #1e293b; }
    .account-num { font-family: 'JetBrains Mono', monospace; font-size: 14px; color: #475569; letter-spacing: 0.05em; }
    .badge-mini { background: #0066FF; color: white; font-size: 9px; font-weight: 800; padding: 2px 6px; border-radius: 4px; text-transform: uppercase; }
    
    .withdrawal-info-box { background: #eff6ff; padding: 20px; border-radius: 16px; border: 1px solid #dbeafe; }
    .premium-input-wrapper { position: relative; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; display: flex; align-items: center; overflow: hidden; padding: 5px 15px; }
    .premium-input-wrapper .input-prefix { font-weight: 800; margin-right: 10px; }
    .form-control-premium { border: none; background: transparent; padding: 10px 0; font-weight: 700; font-size: 16px; width: 100%; outline: none; }
    
    .glow-effect { position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 60%); pointer-events: none; }
    .shadow-primary-200 { box-shadow: 0 4px 14px 0 rgba(0, 102, 255, 0.3); }
</style>
@endsection
