{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin
    View: SuperAdmin Payout Management — Premium v2.0
--}}
@extends('layouts.app')

@section('title', 'Payout Requests')
@section('page_title', 'Vendor Payout Requests')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
<div class="stats-grid mb-4">
    {{-- Summary Cards --}}
    <div class="stat-card orange">
        <div class="stat-card-inner">
            <div class="stat-icon"><i class="fas fa-clock"></i></div>
            <div class="stat-info">
                <h3>{{ $payouts->where('status', 'pending')->count() }}</h3>
                <p>Pending Requests</p>
            </div>
        </div>
    </div>
    
    <div class="stat-card blue">
        <div class="stat-card-inner">
            <div class="stat-icon"><i class="fas fa-spinner fa-spin"></i></div>
            <div class="stat-info">
                <h3>{{ $payouts->where('status', 'processing')->count() }}</h3>
                <p>Processing</p>
            </div>
        </div>
    </div>

    <div class="stat-card green">
        <div class="stat-card-inner">
            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            <div class="stat-info">
                <h3>{{ $payouts->where('status', 'completed')->count() }}</h3>
                <p>Completed Payouts</p>
            </div>
        </div>
    </div>

    <div class="stat-card rose">
        <div class="stat-card-inner">
            <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
            <div class="stat-info">
                <h3>{{ $payouts->where('status', 'failed')->count() }}</h3>
                <p>Failed / Rejected</p>
            </div>
        </div>
    </div>
</div>

{{-- Filter Bar --}}
<div class="dashboard-card mb-4">
    <div class="dashboard-card-body d-flex align-items-center gap-2 flex-wrap">
        <span class="fw-semibold me-2 text-dark"><i class="fas fa-filter me-1"></i> Filter:</span>
        <a href="{{ route('superadmin.payouts') }}" class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-outline-secondary' }} px-3 rounded-pill">All</a>
        <a href="{{ route('superadmin.payouts', ['status' => 'pending']) }}" class="btn btn-sm {{ request('status') === 'pending' ? 'btn-warning' : 'btn-outline-secondary' }} px-3 rounded-pill">Pending</a>
        <a href="{{ route('superadmin.payouts', ['status' => 'processing']) }}" class="btn btn-sm {{ request('status') === 'processing' ? 'btn-info' : 'btn-outline-secondary' }} px-3 rounded-pill">Processing</a>
        <a href="{{ route('superadmin.payouts', ['status' => 'completed']) }}" class="btn btn-sm {{ request('status') === 'completed' ? 'btn-success' : 'btn-outline-secondary' }} px-3 rounded-pill">Completed</a>
        <a href="{{ route('superadmin.payouts', ['status' => 'failed']) }}" class="btn btn-sm {{ request('status') === 'failed' ? 'btn-danger' : 'btn-outline-secondary' }} px-3 rounded-pill">Failed</a>
    </div>
</div>

{{-- Payout Table --}}
<div class="dashboard-card">
    <div class="dashboard-card-body p-0">
        @if($payouts->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="fas fa-money-bill-wave fa-3x mb-3 text-muted"></i>
                <h5 class="text-muted">No Payout Requests Found</h5>
                <p class="text-muted small">When vendors request payouts, they will appear here.</p>
            </div>
        @else
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="ps-4">Reference</th>
                        <th>Vendor</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Requested Date</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payouts as $payout)
                    <tr>
                        <td class="ps-4">
                            <code class="text-primary fw-bold">{{ $payout->reference }}</code>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="topbar-user-avatar" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                    {{ strtoupper(substr($payout->vendor->business_name ?? $payout->vendor->user->name ?? 'V', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark">{{ $payout->vendor->business_name ?? $payout->vendor->user->name ?? 'N/A' }}</div>
                                    <div class="text-muted small" style="font-size:11px;">{{ $payout->vendor->user->email ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="fw-bold text-success" style="font-size: 1rem;">₦{{ number_format($payout->amount, 2) }}</span>
                        </td>
                        <td>
                            <span class="badge badge-secondary text-capitalize">{{ str_replace('_', ' ', $payout->payment_method ?? 'bank transfer') }}</span>
                        </td>
                        <td>
                            @if($payout->status === 'pending')
                                <span class="badge badge-warning"><i class="fas fa-clock me-1"></i> Pending</span>
                            @elseif($payout->status === 'processing')
                                <span class="badge badge-info"><i class="fas fa-spinner fa-spin me-1"></i> Processing</span>
                            @elseif($payout->status === 'completed')
                                <span class="badge badge-success"><i class="fas fa-check-circle me-1"></i> Completed</span>
                            @else
                                <span class="badge badge-danger"><i class="fas fa-times-circle me-1"></i> Failed</span>
                            @endif
                        </td>
                        <td>
                            <div class="text-dark small">{{ $payout->created_at->format('M d, Y') }}</div>
                            <div class="text-muted" style="font-size:11px;">{{ $payout->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="text-end pe-4">
                            @if($payout->status === 'pending' || $payout->status === 'processing')
                            <div class="d-flex justify-content-end gap-2 align-items-center">
                                <form method="POST" action="{{ route('superadmin.payouts.status', $payout->id) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-3">
                                        <i class="fas fa-check-circle me-1"></i> Approve
                                    </button>
                                </form>

                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                        onclick="document.getElementById('failForm{{ $payout->id }}').classList.toggle('d-none')">
                                    <i class="fas fa-times-circle me-1"></i> Reject
                                </button>
                            </div>

                            {{-- Hidden Reject Form with Notes --}}
                            <form id="failForm{{ $payout->id }}" method="POST" action="{{ route('superadmin.payouts.status', $payout->id) }}" class="d-none mt-2 text-start p-3 border rounded-3 bg-light" style="max-width: 250px; margin-left: auto;">
                                @csrf
                                <input type="hidden" name="status" value="rejected">
                                <div class="mb-2">
                                    <label class="form-label text-muted small fw-bold" style="font-size:10px;">REJECTION REASON</label>
                                    <textarea name="notes" class="form-control form-control-sm" placeholder="Explain why..." rows="2" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-sm btn-danger w-100 rounded-pill">
                                    Confirm Rejection
                                </button>
                            </form>
                            @elseif($payout->status === 'completed')
                                <span class="badge badge-success"><i class="fas fa-check me-1"></i> Disbursed</span>
                            @else
                                <span class="badge badge-secondary" title="{{ $payout->notes }}"><i class="fas fa-ban me-1"></i> {{ Str::limit($payout->notes ?? 'Rejected', 12) }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($payouts->hasPages())
        <div class="d-flex justify-content-center py-3 border-top">
            {{ $payouts->appends(request()->query())->links() }}
        </div>
        @endif
    @endif
    </div>
</div>
@endsection
