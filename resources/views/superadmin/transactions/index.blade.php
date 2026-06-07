{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin
    View: Admin — Transaction History — Premium v2.0
--}}
@extends('layouts.app')

@section('title', 'Transaction History')
@section('page_title', 'Transaction History')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@push('styles')
<style>
.txn-header-banner {
    background: linear-gradient(135deg, #064e3b 0%, #065f46 40%, #047857 100%);
    border-radius: 18px;
    padding: 28px 32px;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
}
.txn-header-banner::before {
    content: '';
    position: absolute;
    top: -50px; right: -50px;
    width: 180px; height: 180px;
    background: rgba(167,243,208,.12);
    border-radius: 50%;
}
.txn-header-content { position: relative; z-index: 1; }
.txn-header-content h2 {
    color: white; font-size: 1.375rem; font-weight: 800;
    font-family: 'Outfit', sans-serif; margin-bottom: 4px;
}
.txn-header-content p { color: rgba(255,255,255,.65); font-size: .875rem; margin: 0; }
.txn-header-actions { position: relative; z-index: 1; }

.txn-stats { display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 24px; }
.txn-stat-card {
    flex: 1; min-width: 160px;
    background: var(--surface);
    border: 1.5px solid var(--border-color);
    border-radius: 16px;
    padding: 18px 20px;
    transition: all .2s;
}
.txn-stat-card:hover { border-color: #10b981; box-shadow: 0 0 0 3px rgba(16,185,129,.07); transform: translateY(-2px); }
.txn-stat-icon {
    width: 40px; height: 40px; border-radius: 11px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem; margin-bottom: 10px;
}
.txn-stat-icon.green  { background: rgba(16,185,129,.1); color: #059669; }
.txn-stat-icon.red    { background: rgba(244,63,94,.1);  color: #be123c; }
.txn-stat-icon.blue   { background: rgba(14,165,233,.1); color: #0284c7; }
.txn-stat-icon.purple { background: rgba(139,92,246,.1); color: #7c3aed; }
.txn-stat-label { font-size: .75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: .04em; }
.txn-stat-value { font-size: 1.25rem; font-weight: 800; color: var(--text-primary); margin-top: 4px; }

.txn-filters {
    display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
    padding: 14px 20px; background: var(--surface); border-bottom: 1px solid var(--border-color);
}
.txn-filter-tab {
    padding: 5px 14px; border-radius: 8px; font-size: .8rem; font-weight: 600;
    color: var(--text-secondary); background: white; border: 1.5px solid var(--border-color);
    text-decoration: none; transition: all .15s; cursor: pointer;
}
.txn-filter-tab.active { background: #10b981; border-color: #10b981; color: white; }
.txn-filter-tab:hover:not(.active) { border-color: #10b981; color: #059669; }
.txn-search-wrap {
    display: flex; gap: 0; border: 1.5px solid var(--border-color);
    border-radius: 10px; overflow: hidden; margin-left: auto;
}
.txn-search-wrap input { border: none; font-size: .8125rem; padding: 8px 12px; min-width: 200px; }
.txn-search-wrap input:focus { outline: none; }
.txn-search-wrap button { border-radius: 0; padding: 8px 12px; border: none; background: #10b981; color: white; cursor: pointer; }

.txn-ref-code {
    font-family: 'Courier New', monospace;
    font-size: .8rem; font-weight: 700;
    color: #4f46e5;
    background: rgba(79,70,229,.07);
    padding: 3px 8px; border-radius: 6px;
}
.txn-type-pill {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px; border-radius: 7px;
    font-size: .75rem; font-weight: 700;
}
.txn-type-pill.income  { background: rgba(16,185,129,.1); color: #059669; }
.txn-type-pill.expense { background: rgba(244,63,94,.1);  color: #be123c; }
.txn-amount-positive { font-size: .9rem; font-weight: 800; color: #059669; }
.txn-amount-negative { font-size: .9rem; font-weight: 800; color: #be123c; }
.txn-status-pill {
    padding: 3px 10px; border-radius: 7px;
    font-size: .75rem; font-weight: 700; text-transform: capitalize;
}
.tsp-success { background: rgba(16,185,129,.1); color: #059669; }
.tsp-pending { background: rgba(245,158,11,.1);  color: #d97706; }
.tsp-failed  { background: rgba(244,63,94,.1);  color: #be123c; }
.tsp-other   { background: rgba(100,116,139,.1); color: #475569; }
.txn-user-cell { display: flex; align-items: center; gap: 8px; }
.txn-user-avatar {
    width: 28px; height: 28px; border-radius: 8px;
    background: linear-gradient(135deg, #10b981, #059669);
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: .7rem; font-weight: 700; flex-shrink: 0;
}
</style>
@endpush

@section('content')

{{-- ═══ HEADER BANNER ═══ --}}
<div class="txn-header-banner">
    <div class="txn-header-content">
        <h2><i class="fas fa-arrow-right-arrow-left" style="margin-right:10px;opacity:.8;"></i>Transaction History</h2>
        <p>Complete ledger of platform income, vendor payouts, and financial flows.</p>
    </div>
    <div class="txn-header-actions">
        <button class="btn btn-sm" style="background:rgba(255,255,255,.15);color:white;border:1.5px solid rgba(255,255,255,.25);border-radius:10px;">
            <i class="fas fa-file-csv"></i> Export Ledger
        </button>
    </div>
</div>

{{-- ═══ STATS ═══ --}}
@php
    $allTxns = $paginatedTransactions->items();
    $totalIncome  = collect($allTxns)->where('type','income')->sum('amount');
    $totalExpense = collect($allTxns)->where('type','expense')->sum('amount');
@endphp
<div class="txn-stats">
    <div class="txn-stat-card">
        <div class="txn-stat-icon green"><i class="fas fa-arrow-trend-up"></i></div>
        <div class="txn-stat-label">Total Income</div>
        <div class="txn-stat-value">₦{{ number_format($totalIncome, 0) }}</div>
    </div>
    <div class="txn-stat-card">
        <div class="txn-stat-icon red"><i class="fas fa-arrow-trend-down"></i></div>
        <div class="txn-stat-label">Total Payouts</div>
        <div class="txn-stat-value">₦{{ number_format($totalExpense, 0) }}</div>
    </div>
    <div class="txn-stat-card">
        <div class="txn-stat-icon blue"><i class="fas fa-receipt"></i></div>
        <div class="txn-stat-label">Total Records</div>
        <div class="txn-stat-value">{{ $paginatedTransactions->total() }}</div>
    </div>
    <div class="txn-stat-card">
        <div class="txn-stat-icon purple"><i class="fas fa-scale-balanced"></i></div>
        <div class="txn-stat-label">Net Balance</div>
        <div class="txn-stat-value" style="color: {{ $totalIncome >= $totalExpense ? '#059669' : '#be123c' }};">
            ₦{{ number_format($totalIncome - $totalExpense, 0) }}
        </div>
    </div>
</div>

{{-- ═══ TABLE CARD ═══ --}}
<div class="dashboard-card">
    <div class="dashboard-card-header">
        <div>
            <h3><i class="fas fa-table-list" style="color:#10b981;margin-right:8px;"></i>All Transactions</h3>
            <div style="font-size:.8rem;color:var(--text-muted);margin-top:2px;">
                Showing {{ $paginatedTransactions->firstItem() }}–{{ $paginatedTransactions->lastItem() }}
                of {{ $paginatedTransactions->total() }} records
            </div>
        </div>
    </div>

    <div class="txn-filters">
        <a href="?" class="txn-filter-tab active">All</a>
        <a href="?type=income" class="txn-filter-tab">Income</a>
        <a href="?type=expense" class="txn-filter-tab">Payouts</a>
        <div class="txn-search-wrap">
            <input type="text" placeholder="Search reference, user..." id="txnSearch">
            <button><i class="fas fa-search"></i></button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="data-table" id="txnTable">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>User / Vendor</th>
                    <th>Description</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($paginatedTransactions as $transaction)
                    @php
                        $isIncome = $transaction->type == 'income';
                        $status = strtolower($transaction->status ?? 'completed');
                        $statusClass = match($status) {
                            'success','completed','paid' => 'tsp-success',
                            'pending'                    => 'tsp-pending',
                            'failed','cancelled'         => 'tsp-failed',
                            default                      => 'tsp-other',
                        };
                    @endphp
                    <tr>
                        <td>
                            <span class="txn-ref-code">{{ $transaction->reference }}</span>
                        </td>
                        <td>
                            <div class="txn-user-cell">
                                <div class="txn-user-avatar">
                                    {{ strtoupper(substr($transaction->user ?? 'U', 0, 1)) }}
                                </div>
                                <span style="font-weight:600;font-size:.8125rem;">{{ $transaction->user }}</span>
                            </div>
                        </td>
                        <td style="font-size:.8125rem;color:var(--text-secondary);max-width:200px;">
                            {{ Str::limit($transaction->description, 45) }}
                        </td>
                        <td>
                            <span class="txn-type-pill {{ $isIncome ? 'income' : 'expense' }}">
                                <i class="fas {{ $isIncome ? 'fa-arrow-down' : 'fa-arrow-up' }}"></i>
                                {{ $isIncome ? 'Income' : 'Payout' }}
                            </span>
                        </td>
                        <td>
                            <span class="{{ $isIncome ? 'txn-amount-positive' : 'txn-amount-negative' }}">
                                {{ $isIncome ? '+' : '-' }}₦{{ number_format($transaction->amount, 2) }}
                            </span>
                        </td>
                        <td>
                            <div style="font-size:.8125rem;font-weight:600;color:var(--text-primary);">
                                {{ $transaction->date->format('d M Y') }}
                            </div>
                            <div style="font-size:.7rem;color:var(--text-muted);">
                                {{ $transaction->date->format('h:i A') }}
                            </div>
                        </td>
                        <td>
                            <span class="txn-status-pill {{ $statusClass }}">{{ ucfirst($status) }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="fas fa-arrow-right-arrow-left"></i>
                                <p>No transactions recorded yet.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($paginatedTransactions->hasPages())
        <div style="padding:14px 20px;">
            {{ $paginatedTransactions->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
const txnSearch = document.getElementById('txnSearch');
if (txnSearch) {
    txnSearch.addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#txnTable tbody tr').forEach(function(row) {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
}
</script>
@endpush
