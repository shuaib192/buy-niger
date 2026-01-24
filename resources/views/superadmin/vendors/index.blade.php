{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
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
<div class="row g-4">
    <div class="dashboard-card col-12">
        <div class="dashboard-card-header">
            <h3>All Vendors</h3>
            <div style="display: flex; gap: var(--spacing-md);">
                <a href="{{ route($prefix.'vendors', ['status' => 'pending']) }}" class="btn btn-sm {{ request('status') == 'pending' ? 'btn-primary' : 'btn-secondary' }}">Pending</a>
                <a href="{{ route($prefix.'vendors', ['status' => 'approved']) }}" class="btn btn-sm {{ request('status') == 'approved' ? 'btn-primary' : 'btn-secondary' }}">Approved</a>
                <a href="{{ route($prefix.'vendors') }}" class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-secondary' }}">All</a>
            </div>
        </div>
        <div class="dashboard-card-body">
            <div class="table-responsive">
                <table class="data-table">
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
                                    <div style="display: flex; align-items: center; gap: var(--spacing-md);">
                                        <img src="{{ $vendor->user->avatar_url }}" style="width: 32px; height: 32px; border-radius: var(--radius-md);">
                                        <strong>{{ $vendor->store_name }}</strong>
                                    </div>
                                </td>
                                <td>{{ $vendor->user->name }}</td>
                                <td>
                                    <div style="font-size: 0.75rem;">{{ $vendor->user->email }}</div>
                                    <div style="font-size: 0.75rem; color: var(--secondary-500);">{{ $vendor->user->phone }}</div>
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
                                <td>{{ $vendor->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem;">
                                        @if($vendor->status == 'pending')
                                            <form action="{{ route($prefix.'vendors.status', $vendor) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="btn btn-sm btn-success" title="Approve">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route($prefix.'vendors.show', $vendor) }}" class="btn btn-sm btn-secondary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($vendor->status != 'suspended')
                                            <form action="{{ route($prefix.'vendors.status', $vendor) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="suspended">
                                                <button type="submit" class="btn btn-sm btn-danger" title="Suspend">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: var(--spacing-xl); color: var(--secondary-500);">
                                    No vendors found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $vendors->links() }}
            </div>
        </div>
    </div>
@endsection
