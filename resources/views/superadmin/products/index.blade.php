@extends('layouts.app')

@section('title', 'Product Moderation')
@section('page_title', 'Product Moderation')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
@php
    $prefix = request()->is('admin*') ? 'admin.' : 'superadmin.';
@endphp
<div class="row g-4">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h3>Products</h3>
                    <form action="{{ route($prefix.'products') }}" method="GET" class="d-flex gap-2">
                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search products..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary btn-sm">Search</button>
                    </form>
                </div>
            </div>
            <div class="dashboard-card-body">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Vendor</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Featured</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="width: 40px; height: 40px; background: #eee; border-radius: 4px; overflow: hidden;">
                                                <img src="{{ $product->primary_image_url }}" style="width: 100%; height: 100%; object-fit: cover;">
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ Str::limit($product->name, 30) }}</div>
                                                <small class="text-muted">{{ $product->category->name ?? 'Uncategorized' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route($prefix.'vendors', ['search' => $product->vendor->store_name]) }}">
                                            {{ $product->vendor->store_name }}
                                        </a>
                                    </td>
                                    <td>{{ $product->formatted_price }}</td>
                                    <td>{{ $product->quantity }}</td>
                                    <td>
                                        @if($product->status == 'active') <span class="badge badge-success">Active</span>
                                        @elseif($product->status == 'draft') <span class="badge badge-secondary">Draft</span>
                                        @elseif($product->status == 'rejected') <span class="badge badge-danger">Rejected</span>
                                        @else <span class="badge badge-warning">{{ ucfirst($product->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->is_featured)
                                            <span class="badge badge-purple">Yes</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <form action="{{ route($prefix.'products.feature', $product) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-icon {{ $product->is_featured ? 'btn-warning' : 'btn-outline-secondary' }}" title="Toggle Feature">
                                                    <i class="fas fa-star"></i>
                                                </button>
                                            </form>
                                            
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-icon btn-outline-primary" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <form action="{{ route($prefix.'products.status', $product) }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="status" value="active">
                                                            <button class="dropdown-item text-success"><i class="fas fa-check me-2"></i> Approve</button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route($prefix.'products.status', $product) }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="status" value="rejected">
                                                            <button class="dropdown-item text-danger"><i class="fas fa-times me-2"></i> Reject</button>
                                                        </form>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item" href="{{ route('product.detail', $product->slug) }}" target="_blank"><i class="fas fa-external-link-alt me-2"></i> View</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">No products found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
