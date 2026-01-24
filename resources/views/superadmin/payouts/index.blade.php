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

@php
    $prefix = request()->is('admin*') ? 'admin.' : 'superadmin.';
@endphp

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="dashboard-card-header d-flex justify-content-between align-items-center">
                <h3>Pending & Processed Payouts</h3>
                <div class="d-flex gap-2">
                    <a href="?status=pending" class="btn btn-sm {{ request('status') == 'pending' ? 'btn-warning' : 'btn-outline-warning' }}">Pending</a>
                    <a href="?status=processing" class="btn btn-sm {{ request('status') == 'processing' ? 'btn-info' : 'btn-outline-info' }}">Processing</a>
                    <a href="?status=completed" class="btn btn-sm {{ request('status') == 'completed' ? 'btn-success' : 'btn-outline-success' }}">Completed</a>
                    <a href="{{ route($prefix.'payouts') }}" class="btn btn-sm btn-outline-secondary">All</a>
                </div>
            </div>
            <div class="dashboard-card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Reference</th>
                                <th>Vendor</th>
                                <th>Amount</th>
                                <th>Bank Details</th>
                                <th>Status</th>
                                <th>Requested</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payouts as $payout)
                                @php 
                                    $bank = $payout->payment_details['bank_detail_id'] ?? null;
                                    $bankInfo = \App\Models\VendorBankDetail::find($bank);
                                @endphp
                                <tr>
                                    <td class="ps-4"><code class="text-primary">{{ $payout->reference }}</code></td>
                                    <td>
                                        <strong>{{ $payout->vendor->store_name }}</strong><br>
                                        <small class="text-muted">{{ $payout->vendor->user->name ?? 'N/A' }}</small>
                                    </td>
                                    <td><strong class="text-success">₦{{ number_format($payout->amount, 2) }}</strong></td>
                                    <td>
                                        @if($bankInfo)
                                            <span>{{ $bankInfo->bank_name }}</span><br>
                                            <small class="text-muted">{{ $bankInfo->account_number }} ({{ $bankInfo->account_name }})</small>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($payout->status == 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($payout->status == 'pending')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @elseif($payout->status == 'processing')
                                            <span class="badge bg-info">Processing</span>
                                        @elseif($payout->status == 'failed')
                                            <span class="badge bg-danger">Failed</span>
                                        @endif
                                    </td>
                                    <td>{{ $payout->created_at->format('M d, Y H:i') }}</td>
                                    <td class="text-end pe-4">
                                        @if($payout->status != 'completed' && $payout->status != 'failed')
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#statusModal{{ $payout->id }}">
                                                <i class="fas fa-edit"></i> Update
                                            </button>

                                            <!-- Status Modal -->
                                            <div class="modal fade" id="statusModal{{ $payout->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route($prefix.'payouts.status', $payout->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Update Payout Status</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="alert alert-secondary">
                                                                    <strong>Bank Info:</strong><br>
                                                                    @if($bankInfo)
                                                                        {{ $bankInfo->bank_name }} - {{ $bankInfo->account_number }}<br>
                                                                        ({{ $bankInfo->account_name }})
                                                                    @else
                                                                        N/A
                                                                    @endif
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Status</label>
                                                                    <select name="status" class="form-select" required>
                                                                        <option value="processing" {{ $payout->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                                                        <option value="completed">Completed (Paid)</option>
                                                                        <option value="failed">Failed (Refund Balance)</option>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Notes (Optional)</label>
                                                                    <textarea name="notes" class="form-control" rows="3">{{ $payout->notes }}</textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fas fa-wallet fa-3x mb-3"></i>
                                        <p>No payout requests found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="dashboard-card-footer">
                {{ $payouts->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

