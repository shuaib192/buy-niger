{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    View: SuperAdmin Payout Management
--}}
@extends('layouts.app')

@section('title', 'Payout Requests')
@section('page_title', 'Vendor Payout Requests')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
<div class="row mb-4">
    {{-- Summary Cards --}}
    <div class="col-md-3 mb-3">
        <div class="dashboard-card text-center" style="border-left: 4px solid #f59e0b;">
            <div class="card-body py-3">
                <div class="text-muted small mb-1"><i class="fas fa-clock me-1"></i> Pending</div>
                <div class="fw-bold fs-4 text-warning">{{ $payouts->where('status', 'pending')->count() }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="dashboard-card text-center" style="border-left: 4px solid #6366f1;">
            <div class="card-body py-3">
                <div class="text-muted small mb-1"><i class="fas fa-spinner me-1"></i> Processing</div>
                <div class="fw-bold fs-4" style="color:#6366f1;">{{ $payouts->where('status', 'processing')->count() }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="dashboard-card text-center" style="border-left: 4px solid #10b981;">
            <div class="card-body py-3">
                <div class="text-muted small mb-1"><i class="fas fa-check-circle me-1"></i> Completed</div>
                <div class="fw-bold fs-4 text-success">{{ $payouts->where('status', 'completed')->count() }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="dashboard-card text-center" style="border-left: 4px solid #ef4444;">
            <div class="card-body py-3">
                <div class="text-muted small mb-1"><i class="fas fa-times-circle me-1"></i> Failed</div>
                <div class="fw-bold fs-4 text-danger">{{ $payouts->where('status', 'failed')->count() }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Filter Bar --}}
<div class="dashboard-card mb-4">
    <div class="card-body d-flex align-items-center gap-3 flex-wrap">
        <span class="fw-semibold me-2"><i class="fas fa-filter me-1"></i> Filter:</span>
        <a href="{{ route('superadmin.payouts') }}" class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-outline-secondary' }}">All</a>
        <a href="{{ route('superadmin.payouts', ['status' => 'pending']) }}" class="btn btn-sm {{ request('status') === 'pending' ? 'btn-warning' : 'btn-outline-secondary' }}">Pending</a>
        <a href="{{ route('superadmin.payouts', ['status' => 'processing']) }}" class="btn btn-sm {{ request('status') === 'processing' ? 'btn-info' : 'btn-outline-secondary' }}">Processing</a>
        <a href="{{ route('superadmin.payouts', ['status' => 'completed']) }}" class="btn btn-sm {{ request('status') === 'completed' ? 'btn-success' : 'btn-outline-secondary' }}">Completed</a>
        <a href="{{ route('superadmin.payouts', ['status' => 'failed']) }}" class="btn btn-sm {{ request('status') === 'failed' ? 'btn-danger' : 'btn-outline-secondary' }}">Failed</a>
    </div>
</div>

{{-- Payout Table --}}
<div class="dashboard-card">
    <div class="card-body p-0">
        @if($payouts->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Payout Requests Found</h5>
                <p class="text-muted small">When vendors request payouts, they will appear here.</p>
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Reference</th>
                        <th>Vendor</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Requested</th>
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
                                <img src="{{ $payout->vendor->user->avatar_url ?? '/images/default-avatar.png' }}" 
                                     class="rounded-circle" width="32" height="32" 
                                     style="object-fit:cover;">
                                <div>
                                    <div class="fw-semibold small">{{ $payout->vendor->business_name ?? $payout->vendor->user->name ?? 'N/A' }}</div>
                                    <div class="text-muted" style="font-size:11px;">{{ $payout->vendor->user->email ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="fw-bold" style="color:#10b981;">₦{{ number_format($payout->amount, 2) }}</span>
                        </td>
                        <td>
                            <span class="text-muted small text-capitalize">{{ str_replace('_', ' ', $payout->payment_method ?? 'bank transfer') }}</span>
                        </td>
                        <td>
                            @php
                                $statusMap = [
                                    'pending' => ['bg' => '#fef3c7', 'text' => '#92400e', 'icon' => 'fa-clock'],
                                    'processing' => ['bg' => '#e0e7ff', 'text' => '#3730a3', 'icon' => 'fa-spinner fa-spin'],
                                    'completed' => ['bg' => '#d1fae5', 'text' => '#065f46', 'icon' => 'fa-check-circle'],
                                    'failed' => ['bg' => '#fee2e2', 'text' => '#991b1b', 'icon' => 'fa-times-circle'],
                                ];
                                $s = $statusMap[$payout->status] ?? $statusMap['pending'];
                            @endphp
                            <span class="badge rounded-pill px-3 py-2" style="background:{{ $s['bg'] }};color:{{ $s['text'] }};font-weight:600;">
                                <i class="fas {{ $s['icon'] }} me-1"></i> {{ ucfirst($payout->status) }}
                            </span>
                        </td>
                        <td>
                            <span class="text-muted small">{{ $payout->created_at->format('M d, Y') }}</span>
                            <div class="text-muted" style="font-size:11px;">{{ $payout->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="text-end pe-4">
                            @if($payout->status === 'pending' || $payout->status === 'processing')
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-cog me-1"></i> Update
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow">
                                    @if($payout->status === 'pending')
                                    <li>
                                        <form method="POST" action="{{ route('superadmin.payouts.status', $payout->id) }}">
                                            @csrf
                                            <input type="hidden" name="status" value="processing">
                                            <button type="submit" class="dropdown-item text-info">
                                                <i class="fas fa-spinner me-2"></i> Mark Processing
                                            </button>
                                        </form>
                                    </li>
                                    @endif
                                    <li>
                                        <form method="POST" action="{{ route('superadmin.payouts.status', $payout->id) }}">
                                            @csrf
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="dropdown-item text-success">
                                                <i class="fas fa-check-circle me-2"></i> Mark Completed
                                            </button>
                                        </form>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <button type="button" class="dropdown-item text-danger" 
                                                onclick="document.getElementById('failForm{{ $payout->id }}').classList.toggle('d-none')">
                                            <i class="fas fa-times-circle me-2"></i> Mark Failed
                                        </button>
                                    </li>
                                </ul>
                            </div>

                            {{-- Hidden Fail Form with Notes --}}
                            <form id="failForm{{ $payout->id }}" method="POST" action="{{ route('superadmin.payouts.status', $payout->id) }}" class="d-none mt-2 text-start">
                                @csrf
                                <input type="hidden" name="status" value="failed">
                                <textarea name="notes" class="form-control form-control-sm mb-2" placeholder="Reason for failure..." rows="2" required></textarea>
                                <button type="submit" class="btn btn-sm btn-danger w-100">
                                    <i class="fas fa-times me-1"></i> Confirm Fail
                                </button>
                            </form>
                            @elseif($payout->status === 'completed')
                                <span class="badge bg-success-subtle text-success"><i class="fas fa-check me-1"></i> Done</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger"><i class="fas fa-ban me-1"></i> {{ $payout->notes ?? 'Failed' }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center py-3">
            {{ $payouts->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
