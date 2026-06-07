{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin
    View: Admin — Vendor Management — Premium v2.0
--}}
@extends('layouts.app')
@section('title', 'Vendor Management')
@section('page_title', 'Vendor Management')
@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@push('styles')
<style>
.filters-bar {
    display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
    padding: 14px 20px;
    background: var(--surface);
    border-bottom: 1px solid var(--border-color);
}
.filter-tabs { display: flex; gap: 4px; flex-wrap: wrap; }
.filter-tab {
    padding: 6px 14px; border-radius: 8px; font-size: .8125rem; font-weight: 600;
    color: var(--text-secondary); background: white; border: 1.5px solid var(--border-color);
    text-decoration: none; transition: all .15s;
}
.filter-tab:hover { border-color: #4f46e5; color: #4f46e5; }
.filter-tab.active { background: #4f46e5; border-color: #4f46e5; color: white; }
.filter-tab.warning.active { background: #f59e0b; border-color: #f59e0b; }
.filter-tab.success.active { background: #10b981; border-color: #10b981; }
.filter-tab.danger.active  { background: #f43f5e; border-color: #f43f5e; }
.search-group {
    display: flex; gap: 0; border: 1.5px solid var(--border-color);
    border-radius: 10px; overflow: hidden; margin-left: auto;
}
.search-group .form-control { border: none; border-radius: 0; font-size: .8125rem; min-width: 200px; }
.search-group .form-control:focus { box-shadow: none; }
.search-group .btn { border-radius: 0; padding: 9px 14px; font-size: .8125rem; }
.vendor-cell { display: flex; align-items: center; gap: 10px; }
.store-icon {
    width: 38px; height: 38px; border-radius: 10px;
    background: linear-gradient(135deg, #4f46e5, #8b5cf6);
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: .875rem; font-weight: 700;
    overflow: hidden; flex-shrink: 0;
}
.store-icon img { width:100%;height:100%;object-fit:cover; }
.store-name { font-weight: 700; font-size: .875rem; color: var(--text-primary); }
.store-slug { font-size: .7rem; color: var(--text-muted); margin-top:1px; }
.action-group { display: flex; gap: 6px; align-items: center; }
</style>
@endpush

@section('content')
@php $prefix = request()->is('admin*') ? 'admin.' : 'superadmin.'; @endphp

<div class="dashboard-card">
    <div class="dashboard-card-header">
        <div>
            <h3><i class="fas fa-store" style="color:#8b5cf6;margin-right:8px;"></i>All Vendors</h3>
            <div style="font-size:.8rem;color:var(--text-muted);margin-top:2px;">
                Approve, suspend and monitor vendor stores.
            </div>
        </div>
        <a href="{{ route($prefix.'vendors.export', request()->all()) }}"
           class="btn btn-sm btn-success">
            <i class="fas fa-file-csv"></i> Export
        </a>
    </div>

    {{-- Filters --}}
    <div class="filters-bar">
        <div class="filter-tabs">
            <a href="{{ route($prefix.'vendors') }}"
               class="filter-tab {{ !request('status') ? 'active' : '' }}">All</a>
            <a href="{{ route($prefix.'vendors', ['status'=>'pending']) }}"
               class="filter-tab warning {{ request('status')=='pending' ? 'active' : '' }}">
                <i class="fas fa-clock" style="font-size:.65rem;"></i> Pending
            </a>
            <a href="{{ route($prefix.'vendors', ['status'=>'approved']) }}"
               class="filter-tab success {{ request('status')=='approved' ? 'active' : '' }}">
                <i class="fas fa-check" style="font-size:.65rem;"></i> Approved
            </a>
            <a href="{{ route($prefix.'vendors', ['status'=>'suspended']) }}"
               class="filter-tab danger {{ request('status')=='suspended' ? 'active' : '' }}">
                <i class="fas fa-ban" style="font-size:.65rem;"></i> Suspended
            </a>
        </div>
        <form action="{{ route($prefix.'vendors') }}" method="GET">
            @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
            <div class="search-group">
                <input type="text" name="search" class="form-control"
                       placeholder="Search store, owner..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Store</th>
                    <th>Owner</th>
                    <th>Contact</th>
                    <th>KYC</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vendors as $vendor)
                    <tr>
                        <td>
                            <div class="vendor-cell">
                                <div class="store-icon">
                                    @php $logo = $vendor->store_logo_url ?? null; @endphp
                                    @if($logo)
                                        <img src="{{ $logo }}" alt="">
                                    @else
                                        {{ strtoupper(substr($vendor->store_name ?? 'S', 0, 1)) }}
                                    @endif
                                </div>
                                <div>
                                    <div class="store-name">{{ $vendor->store_name ?? '—' }}</div>
                                    <div class="store-slug">/store/{{ $vendor->store_slug ?? '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-weight:600;font-size:.875rem;">{{ $vendor->user->name }}</div>
                        </td>
                        <td>
                            <div style="font-size:.8rem;color:var(--text-secondary);">{{ $vendor->user->email }}</div>
                            <div style="font-size:.75rem;color:var(--text-muted);">{{ $vendor->user->phone ?? '—' }}</div>
                        </td>
                        <td>
                            @php $kyc = $vendor->kyc_status ?? 'not_submitted'; @endphp
                            @if($kyc === 'verified')
                                <span class="badge badge-success"><i class="fas fa-shield-check" style="font-size:.65rem;"></i> Verified</span>
                            @elseif($kyc === 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @else
                                <span class="badge badge-secondary">Not Submitted</span>
                            @endif
                        </td>
                        <td>
                            @if($vendor->status == 'approved')
                                <span class="badge badge-success">Approved</span>
                            @elseif($vendor->status == 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @else
                                <span class="badge badge-danger">{{ ucfirst($vendor->status) }}</span>
                            @endif
                        </td>
                        <td style="font-size:.8125rem;color:var(--text-secondary);">
                            {{ $vendor->created_at->format('d M Y') }}
                        </td>
                        <td>
                            <div class="action-group">
                                <a href="{{ route($prefix.'vendors.show', $vendor) }}"
                                   class="btn btn-sm btn-secondary" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($vendor->status == 'pending')
                                    <form action="{{ route($prefix.'vendors.status', $vendor) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="btn btn-sm btn-success" title="Approve Vendor">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif
                                @if($vendor->status != 'suspended')
                                    <form action="{{ route($prefix.'vendors.status', $vendor) }}" method="POST"
                                          onsubmit="return confirm('Suspend {{ $vendor->store_name }}?')">
                                        @csrf
                                        <input type="hidden" name="status" value="suspended">
                                        <button type="submit" class="btn btn-sm btn-danger" title="Suspend">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route($prefix.'vendors.status', $vendor) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="btn btn-sm btn-success" title="Reactivate">
                                            <i class="fas fa-rotate-right"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="fas fa-store-slash"></i>
                                <p>No vendors found.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:14px 20px;">
        {{ $vendors->appends(request()->query())->links() }}
    </div>
</div>
@endsection
