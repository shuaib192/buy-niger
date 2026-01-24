{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    View: Vendor Product List
--}}
@extends('layouts.app')

@section('title', 'Manage Products')
@section('page_title', 'Products')

@section('sidebar')
    @include('vendor.partials.sidebar')
@endsection

@section('content')
    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <h3>All Products</h3>
            <a href="{{ route('vendor.products.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New
            </a>
        </div>
        <div class="dashboard-card-body">
            <form action="{{ route('vendor.products.bulk') }}" method="POST" id="bulkForm">
                @csrf
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="search-input-group">
                            <i class="fas fa-search"></i>
                            <input type="text" name="search" class="form-control" placeholder="Search products..." value="{{ request('search') }}">
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
                            <option value="">All Status</option>
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

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <select name="action" class="form-select form-select-sm" style="width: auto;">
                            <option value="">Bulk Actions</option>
                            <option value="activate">Mark Active</option>
                            <option value="deactivate">Mark Inactive</option>
                            <option value="delete">Delete Selected</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-secondary" onclick="return confirm('Apply this action to selected items?')">Apply</button>
                    </div>
                    <button type="button" onclick="window.location.href='{{ route('vendor.products') }}'" class="btn btn-sm btn-link text-muted">Clear Filters</button>
                </div>

                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th width="30"><input type="checkbox" id="selectAll"></th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="ids[]" value="{{ $product->id }}" class="row-checkbox">
                                    </td>
                                    <td>
                                        @if($product->primary_image_url)
                                            <img src="{{ $product->primary_image_url }}" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                        @else
                                            <div style="width: 50px; height: 50px; background: #eee; border-radius: 8px;"></div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="font-weight-bold">{{ $product->name }}</div>
                                        <small class="text-muted">{{ $product->category->name ?? 'Uncategorized' }}</small>
                                    </td>
                                    <td>
                                        @if($product->sale_price)
                                            <span class="text-danger">₦{{ number_format($product->sale_price) }}</span>
                                            <br><small><del>₦{{ number_format($product->price) }}</del></small>
                                        @else
                                            ₦{{ number_format($product->price) }}
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $product->quantity > 5 ? 'badge-success' : 'badge-danger' }}">
                                            {{ $product->quantity }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $product->status == 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($product->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('vendor.products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="#" onclick="if(confirm('Delete?')) document.getElementById('del-{{$product->id}}').submit()" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            <form id="del-{{$product->id}}" action="{{ route('vendor.products.destroy', $product->id) }}" method="POST" style="display:none;">
                                                @csrf @method('DELETE')
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No products found.</p>
                                        <a href="{{ route('vendor.products.create') }}" class="btn btn-primary mt-2">Add Your First Product</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
            
            <style>
                .search-input-group {
                    position: relative;
                    display: flex;
                    align-items: center;
                }
                .search-input-group i {
                    position: absolute;
                    left: 12px;
                    color: var(--secondary-400);
                }
                .search-input-group .form-control {
                    padding-left: 35px;
                }
            </style>

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
                        
                        // Add search if exists
                        const search = document.querySelector('input[name="search"]').value;
                        if(search) {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'search';
                            input.value = search;
                            form.appendChild(input);
                        }

                        // Add all filters
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

                // Handle search on enter
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
            <div class="mt-4">
                {{ $products->links() }}
            </div>
        </div>
    </div>
@endsection
