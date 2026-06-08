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

{{-- CSS classes are in dashboard.css (admin-page-card, admin-table, btn-xs, filter-pill, etc.) --}}

<div class="admin-page-card">
    {{-- Header --}}
    <div class="admin-page-header">
        <div class="admin-page-header-left">
            <h2><i class="fas fa-store text-warning"></i> All Vendors</h2>
        </div>

        {{-- Status filter pills --}}
        <div class="filter-pill-group">
            <a href="{{ route($prefix.'vendors') }}"
               class="filter-pill {{ !request('status') ? 'active' : '' }}">All</a>
            <a href="{{ route($prefix.'vendors', ['status' => 'pending']) }}"
               class="filter-pill {{ request('status') == 'pending' ? 'active' : '' }}">
                <i class="fas fa-clock mr-1"></i>Pending
            </a>
            <a href="{{ route($prefix.'vendors', ['status' => 'approved']) }}"
               class="filter-pill {{ request('status') == 'approved' ? 'active' : '' }}">
                <i class="fas fa-check mr-1"></i>Approved
            </a>
            <a href="{{ route($prefix.'vendors', ['status' => 'suspended']) }}"
               class="filter-pill {{ request('status') == 'suspended' ? 'active' : '' }}">
                <i class="fas fa-ban mr-1"></i>Suspended
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
                                <span class="badge badge-success">Approved</span>
                            @elseif($vendor->status == 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @else
                                <span class="badge badge-danger">{{ ucfirst($vendor->status) }}</span>
                            @endif
                        </td>
                        <td class="text-nowrap">{{ $vendor->created_at->format('M d, Y') }}</td>
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
