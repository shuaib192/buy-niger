{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin
    View: Super Admin - Vendor Management
--}}
@extends('layouts.app')

@section('title', 'Vendor Management')
@section('page_title', 'Vendors')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
@php
    $prefix = request()->is('admin*') ? 'admin.' : 'superadmin.';
@endphp

<style>
    /* ── Vendors page: zero-overflow layout ── */
    .admin-page-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 1px 4px rgba(0,0,0,.06);
        overflow: hidden;
        width: 100%;
        box-sizing: border-box;
    }
    .admin-page-header {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
    }
    .admin-page-header-left {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-shrink: 0;
    }
    .admin-page-header-left h2 {
        font-size: 1rem;
        font-weight: 700;
        margin: 0;
        color: #0f172a;
    }
    .filter-pill-group {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    .filter-pill {
        display: inline-flex;
        align-items: center;
        padding: 5px 14px;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 50px;
        border: 1.5px solid #e2e8f0;
        color: #475569;
        background: #f8fafc;
        text-decoration: none;
        cursor: pointer;
        transition: all .15s;
        white-space: nowrap;
    }
    .filter-pill:hover { background: #e2e8f0; }
    .filter-pill.active {
        background: #3b82f6;
        border-color: #3b82f6;
        color: #fff;
    }

    /* ── Responsive table ── */
    .admin-table-wrap {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .admin-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 540px;
    }
    .admin-table thead th {
        padding: 10px 16px;
        font-size: 0.6875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #94a3b8;
        background: #f8fafc;
        white-space: nowrap;
        border-bottom: 1px solid #f1f5f9;
    }
    .admin-table tbody td {
        padding: 12px 16px;
        font-size: 0.8125rem;
        color: #334155;
        border-bottom: 1px solid #f8fafc;
        vertical-align: middle;
    }
    .admin-table tbody tr:last-child td { border-bottom: none; }
    .admin-table tbody tr:hover { background: #f8fafc; }

    .store-cell { display: flex; align-items: center; gap: 10px; }
    .store-avatar {
        width: 34px; height: 34px;
        border-radius: 8px; object-fit: cover; flex-shrink: 0;
        border: 1px solid #e2e8f0;
    }
    .store-name { font-weight: 600; color: #0f172a; }

    .contact-cell small { color: #94a3b8; display: block; font-size: 0.6875rem; }

    .action-group { display: flex; gap: 6px; flex-wrap: nowrap; }
    .btn-xs {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 5px 10px; font-size: 0.75rem; font-weight: 600;
        border-radius: 8px; border: 1.5px solid transparent;
        cursor: pointer; white-space: nowrap;
        transition: all .15s; text-decoration: none;
    }
    .btn-xs-success { border-color:#10b981;color:#065f46;background:#d1fae5; }
    .btn-xs-success:hover { background:#a7f3d0; }
    .btn-xs-danger  { border-color:#ef4444;color:#991b1b;background:#fee2e2; }
    .btn-xs-danger:hover  { background:#fecaca; }
    .btn-xs-sec     { border-color:#e2e8f0;color:#475569;background:#f8fafc; }
    .btn-xs-sec:hover     { background:#e2e8f0; }

    .pager-wrap {
        padding: 14px 20px;
        border-top: 1px solid #f1f5f9;
        overflow-x: auto;
        width: 100%;
    }
    .empty-state { padding: 48px 20px; text-align: center; color: #94a3b8; }
    .empty-state i { font-size: 2rem; margin-bottom: 10px; display: block; }

    @media (max-width: 640px) {
        .admin-page-header {
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
            padding: 12px 14px;
        }
        .admin-page-header-left {
            width: 100%;
        }
        .filter-pill-group {
            width: 100%;
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            gap: 6px;
        }
        .filter-pill {
            flex: 1 1 auto;
            text-align: center;
            justify-content: center;
        }
    }
</style>

<div class="admin-page-card">
    {{-- Header --}}
    <div class="admin-page-header">
        <div class="admin-page-header-left">
            <h2><i class="fas fa-store" style="color:#f59e0b;"></i> All Vendors</h2>
        </div>

        {{-- Status filter pills --}}
        <div class="filter-pill-group">
            <a href="{{ route($prefix.'vendors') }}"
               class="filter-pill {{ !request('status') ? 'active' : '' }}">All</a>
            <a href="{{ route($prefix.'vendors', ['status' => 'pending']) }}"
               class="filter-pill {{ request('status') == 'pending' ? 'active' : '' }}">
                <i class="fas fa-clock" style="margin-right:4px;"></i>Pending
            </a>
            <a href="{{ route($prefix.'vendors', ['status' => 'approved']) }}"
               class="filter-pill {{ request('status') == 'approved' ? 'active' : '' }}">
                <i class="fas fa-check" style="margin-right:4px;"></i>Approved
            </a>
            <a href="{{ route($prefix.'vendors', ['status' => 'suspended']) }}"
               class="filter-pill {{ request('status') == 'suspended' ? 'active' : '' }}">
                <i class="fas fa-ban" style="margin-right:4px;"></i>Suspended
            </a>
        </div>
    </div>

    {{-- Table --}}
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Store</th>
                    <th>Owner</th>
                    <th>Contact</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vendors as $vendor)
                    <tr>
                        <td>
                            <div class="store-cell">
                                <img src="{{ optional($vendor->user)->avatar_url ?? '/images/default-avatar.png' }}"
                                     class="store-avatar" alt="">
                                <span class="store-name">{{ $vendor->store_name }}</span>
                            </div>
                        </td>
                        <td>{{ optional($vendor->user)->name ?? 'Unknown' }}</td>
                        <td>
                            <div class="contact-cell">
                                {{ optional($vendor->user)->email ?? 'N/A' }}
                                <small>{{ optional($vendor->user)->phone ?? 'No phone' }}</small>
                            </div>
                        </td>
                        <td>
                            @if($vendor->status == 'approved')
                                <span class="badge" style="background:#dcfce7;color:#15803d;">Approved</span>
                            @elseif($vendor->status == 'pending')
                                <span class="badge" style="background:#fef3c7;color:#92400e;">Pending</span>
                            @else
                                <span class="badge" style="background:#fee2e2;color:#dc2626;">{{ ucfirst($vendor->status) }}</span>
                            @endif
                        </td>
                        <td style="white-space:nowrap;">{{ $vendor->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="action-group">
                                @if($vendor->status == 'pending')
                                    <form action="{{ route($prefix.'vendors.status', $vendor) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="btn-xs btn-xs-success" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route($prefix.'vendors.show', $vendor) }}"
                                   class="btn-xs btn-xs-sec" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($vendor->status != 'suspended')
                                    <form action="{{ route($prefix.'vendors.status', $vendor) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="suspended">
                                        <button type="submit" class="btn-xs btn-xs-danger" title="Suspend">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="fas fa-store-slash"></i>
                                No vendors found.
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="pager-wrap">
        {{ $vendors->links() }}
    </div>
</div>
@endsection
