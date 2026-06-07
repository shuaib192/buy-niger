{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin
    View: Vendor — Products Index — Premium v2.0
--}}
@extends('layouts.app')

@section('title', 'Manage Products')
@section('page_title', 'Products Catalog')

@section('sidebar')
    @include('vendor.partials.sidebar')
@endsection

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <div>
                    <h3 class="mb-1">My Products Inventory</h3>
                    <p class="text-muted small mb-0">Publish, modify, categorize, and track listings in your store.</p>
                </div>
                <a href="{{ route('vendor.products.create') }}" class="btn btn-primary rounded-pill px-4">
                    <i class="fas fa-plus me-1"></i> Add New Product
                </a>
            </div>
            <div class="dashboard-card-body">
                <form action="{{ route('vendor.products.bulk') }}" method="POST" id="bulkForm">
                    @csrf
                    
                    {{-- Filters Row --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" name="search" class="form-control border-start-0" placeholder="Search products..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="category" class="form-select">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="sort" class="form-select">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                                <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name: A-Z</option>
                                <option value="stock_low" {{ request('sort') == 'stock_low' ? 'selected' : '' }}>Stock: Low to High</option>
                            </select>
                        </div>
                    </div>

                    {{-- Bulk Actions Panel --}}
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <select name="action" class="form-select form-select-sm" style="width: auto;">
                                <option value="">Bulk Actions</option>
                                <option value="activate">Publish (Active)</option>
                                <option value="deactivate">Unpublish (Draft)</option>
                                <option value="delete">Delete Selected</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="return confirm('Apply selected action tochecked items?')">Apply Action</button>
                        </div>
                        <button type="button" onclick="window.location.href='{{ route('vendor.products') }}'" class="btn btn-sm btn-link text-indigo text-decoration-none">Clear All Filters</button>
                    </div>

                    {{-- Products Table --}}
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="ps-4" width="40">
                                        <input type="checkbox" id="selectAll" class="form-check-input" style="cursor: pointer;">
                                    </th>
                                    <th>Product Details</th>
                                    <th>Price Structure</th>
                                    <th>Inventory</th>
                                    <th>Status</th>
                                    <th class="text-end pe-4" width="120">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr>
                                        <td class="ps-4">
                                            <input type="checkbox" name="ids[]" value="{{ $product->id }}" class="row-checkbox form-check-input" style="cursor: pointer;">
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="rounded-3 border overflow-hidden bg-light" style="width: 44px; height: 44px; flex-shrink: 0;">
                                                    @if($product->primary_image_url)
                                                        <img src="{{ $product->primary_image_url }}" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                                                    @else
                                                        <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted" style="background:#eee;"><i class="fas fa-box"></i></div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="fw-semibold text-dark">{{ $product->name }}</div>
                                                    <small class="text-muted">{{ $product->category->name ?? 'Uncategorized' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($product->sale_price)
                                                <span class="fw-bold text-success" style="font-size: 0.95rem;">₦{{ number_format($product->sale_price) }}</span>
                                                <div class="text-muted small" style="font-size:10px;"><del>₦{{ number_format($product->price) }}</del></div>
                                            @else
                                                <span class="fw-bold text-dark" style="font-size: 0.95rem;">₦{{ number_format($product->price) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($product->quantity <= 5)
                                                <span class="badge badge-danger"><i class="fas fa-triangle-exclamation me-1"></i> {{ $product->quantity }} Stock</span>
                                            @else
                                                <span class="badge badge-success">{{ $product->quantity }} Available</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($product->status == 'active')
                                                <span class="badge badge-success"><i class="fas fa-circle-check me-1"></i> Published</span>
                                            @elseif($product->status == 'draft')
                                                <span class="badge badge-secondary"><i class="fas fa-file-signature me-1"></i> Draft</span>
                                            @else
                                                <span class="badge badge-warning">{{ ucfirst($product->status) }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="d-flex justify-content-end align-items-center gap-1">
                                                <a href="{{ route('vendor.products.edit', $product->id) }}" class="btn btn-sm btn-icon btn-outline-primary" title="Edit Product" style="width:28px; height:28px; border-radius:6px;">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" onclick="if(confirm('Are you sure you want to delete this product?')) document.getElementById('del-{{$product->id}}').submit()" class="btn btn-sm btn-icon btn-outline-danger" title="Delete Product" style="width:28px; height:28px; border-radius:6px;">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="fas fa-box-open fa-3x mb-3 text-muted"></i>
                                            <h5 class="text-muted">No Products Found</h5>
                                            <p class="text-muted small">You haven't listed any items matching your filters.</p>
                                            <a href="{{ route('vendor.products.create') }}" class="btn btn-primary rounded-pill px-4 mt-2">Add First Product</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>
                
                @foreach($products as $product)
                    <form id="del-{{$product->id}}" action="{{ route('vendor.products.destroy', $product->id) }}" method="POST" style="display:none;">
                        @csrf @method('DELETE')
                    </form>
                @endforeach

                @if($products->hasPages())
                    <div class="d-flex justify-content-center border-top py-3">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('selectAll').addEventListener('change', function() {
        let checkboxes = document.querySelectorAll('.row-checkbox');
        for (let checkbox of checkboxes) {
            checkbox.checked = this.checked;
        }
    });

    // Auto-submit filters on change
    document.querySelectorAll('select[name="category"], select[name="status"], select[name="sort"]').forEach(select => {
        select.addEventListener('change', () => {
            const form = document.createElement('form');
            form.method = 'GET';
            form.action = '{{ route('vendor.products') }}';
            
            const search = document.querySelector('input[name="search"]').value;
            if(search) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'search';
                input.value = search;
                form.appendChild(input);
            }

            document.querySelectorAll('.form-select[name]').forEach(s => {
                if(s.name !== 'action' && s.value) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = s.name;
                    input.value = s.value;
                    form.appendChild(input);
                }
            });

            document.body.appendChild(form);
            form.submit();
        });
    });

    // Handle search on enter keypress
    document.querySelector('input[name="search"]').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const val = this.value;
            const url = new URL(window.location.href);
            if(val) url.searchParams.set('search', val);
            else url.searchParams.delete('search');
            window.location.href = url.href;
        }
    });
</script>
@endpush
@endsection
