{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin
    View: Admin — Product Moderation — Premium v2.0
--}}
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
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 w-100">
                    <div>
                        <h3 class="mb-1">Product Catalog & Moderation</h3>
                        <p class="text-muted small mb-0">Approve, feature, reject, or archive products across the marketplace.</p>
                    </div>
                    <form action="{{ route($prefix.'products') }}" method="GET" class="d-flex align-items-center gap-2 flex-wrap">
                        <div class="input-group input-group-sm" style="width: auto;">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-filter text-muted"></i></span>
                            <select name="status" class="form-select form-select-sm border-start-0" onchange="this.form.submit()">
                                <option value="">All Statuses</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                        </div>
                        <div class="input-group input-group-sm" style="max-width: 250px;">
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search products..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary btn-sm">Search</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="dashboard-card-body p-0">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="ps-4">Product</th>
                                <th>Vendor</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Featured</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-3 border overflow-hidden bg-light" style="width: 44px; height: 44px; flex-shrink:0;">
                                                <img src="{{ $product->primary_image_url }}" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-dark">{{ Str::limit($product->name, 30) }}</div>
                                                <small class="text-muted">{{ $product->category->name ?? 'Uncategorized' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route($prefix.'vendors', ['search' => $product->vendor->store_name]) }}" class="text-decoration-none fw-medium text-indigo">
                                            <i class="fas fa-store me-1"></i> {{ $product->vendor->store_name }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark">{{ $product->formatted_price }}</span>
                                    </td>
                                    <td>
                                        @if($product->quantity <= 5)
                                            <span class="text-danger fw-bold"><i class="fas fa-triangle-exclamation me-1"></i> {{ $product->quantity }} Left</span>
                                        @else
                                            <span class="text-muted">{{ $product->quantity }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->status == 'active') 
                                            <span class="badge badge-success"><i class="fas fa-circle-check me-1"></i> Active</span>
                                        @elseif($product->status == 'draft') 
                                            <span class="badge badge-secondary"><i class="fas fa-file-signature me-1"></i> Draft</span>
                                        @elseif($product->status == 'rejected') 
                                            <span class="badge badge-danger"><i class="fas fa-circle-xmark me-1"></i> Rejected</span>
                                        @else 
                                            <span class="badge badge-warning"><i class="fas fa-circle-info me-1"></i> {{ ucfirst($product->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->is_featured)
                                            <span class="badge badge-primary"><i class="fas fa-star me-1"></i> Featured</span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end align-items-center gap-2">
                                            <form action="{{ route($prefix.'products.feature', $product) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-icon {{ $product->is_featured ? 'btn-warning' : 'btn-outline-secondary' }}" title="{{ $product->is_featured ? 'Remove Featured Status' : 'Feature Product' }}" style="width: 28px; height: 28px; border-radius: 6px;">
                                                    <i class="fas fa-star"></i>
                                                </button>
                                            </form>
                                            
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-icon btn-outline-primary" type="button" data-bs-toggle="dropdown" style="width: 28px; height: 28px; border-radius: 6px;">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="border-radius:10px;">
                                                    @if($product->status !== 'active')
                                                        <li>
                                                            <form action="{{ route($prefix.'products.status', $product) }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="status" value="active">
                                                                <button class="dropdown-item text-success"><i class="fas fa-check-circle me-2"></i> Approve Product</button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                    @if($product->status !== 'rejected')
                                                        <li>
                                                            <form action="{{ route($prefix.'products.status', $product) }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="status" value="rejected">
                                                                <button class="dropdown-item text-danger"><i class="fas fa-ban me-2"></i> Reject Product</button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('product.detail', $product->slug) }}" target="_blank">
                                                            <i class="fas fa-external-link-alt me-2"></i> View on Webshop
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fas fa-box-open fa-3x mb-3 text-muted"></i>
                                        <h5 class="text-muted">No Products Found</h5>
                                        <p class="text-muted small">No products match your current filters.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($products->hasPages())
                    <div class="d-flex justify-content-center border-top py-3">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
