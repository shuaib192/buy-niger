{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin
    View: Vendor — Add New Product — Premium v2.0
--}}
@extends('layouts.app')

@section('title', 'Add New Product')
@section('page_title', 'Add Product')

@section('sidebar')
    @include('vendor.partials.sidebar')
@endsection

@section('content')
<form action="{{ route('vendor.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-4">
        <!-- Main Column (Left) -->
        <div class="col-lg-8">
            <!-- Basic Info -->
            <div class="dashboard-card mb-4">
                <div class="dashboard-card-header">
                    <div>
                        <h3 class="mb-0">Product Details</h3>
                        <p class="text-muted small mb-0">Provide primary information and descriptions of your item.</p>
                    </div>
                </div>
                <div class="dashboard-card-body">
                    <div class="form-group mb-4">
                        <label class="form-label text-dark fw-semibold small">Product Title / Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-lg fw-bold" value="{{ old('name') }}" placeholder="e.g. Handcrafted Leather Sandal" required>
                        @error('name') <div class="text-danger text-xs mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-label text-dark fw-semibold small">Detailed Product Description <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="6" placeholder="Provide detailed specifications, features, sizes, and care instructions..." required>{{ old('description') }}</textarea>
                         @error('description') <div class="text-danger text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <!-- Media -->
            <div class="dashboard-card mb-4">
                <div class="dashboard-card-header">
                    <div>
                        <h3 class="mb-0">Product Media & Gallery</h3>
                        <p class="text-muted small mb-0">Upload high-resolution images to showcase your item.</p>
                    </div>
                </div>
                <div class="dashboard-card-body">
                    <div class="image-upload-area p-5 border border-dashed rounded-3 text-center bg-light" id="drop-zone" style="border-color: var(--border-color) !important; transition: all 0.2s;">
                        <input type="file" name="images[]" id="images" multiple accept="image/*" style="display: none;">
                        <label for="images" class="upload-label mb-0" style="cursor: pointer;">
                            <i class="fas fa-cloud-arrow-up text-indigo fa-3x mb-3"></i>
                            <div class="fw-bold text-dark">Click to browse gallery files</div>
                            <small class="text-muted">or drag & drop images here</small>
                        </label>
                    </div>
                    <div id="image-preview" class="image-preview-grid mt-3"></div>
                </div>
            </div>

             <!-- Variants -->
             <div class="dashboard-card mb-4">
                <div class="dashboard-card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">Product Custom Variants</h3>
                        <p class="text-muted small mb-0">Specify size variations, colors, prices, and stock units.</p>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="add-variant">
                        <i class="fas fa-plus me-1"></i> Add Custom Option
                    </button>
                </div>
                <div class="dashboard-card-body">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Size Code</th>
                                    <th>Color Value</th>
                                    <th>Price Adjust (₦)</th>
                                    <th>Stock Qty</th>
                                    <th>Variant SKU</th>
                                    <th width="40"></th>
                                </tr>
                            </thead>
                            <tbody id="variants-body"></tbody>
                        </table>
                    </div>
                    <p class="text-muted small mt-3"><i class="fas fa-circle-info me-1"></i> Leaving variant prices blank defaults them to the base product price.</p>
                </div>
            </div>
            
            <!-- SEO -->
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <div>
                        <h3 class="mb-0">Search Engine Listing</h3>
                        <p class="text-muted small mb-0">Control how search engine bots index and display this product listing.</p>
                    </div>
                </div>
                <div class="dashboard-card-body">
                    <div class="p-3 bg-light rounded-3 border mb-4" style="border-color: var(--border-color) !important;">
                        <div class="text-success small mb-1" style="font-size:11px;">buyniger.com.ng > product > ...</div>
                        <h5 class="text-primary fw-bold mb-1" id="preview-title" style="font-size: 1.05rem; font-family:'Outfit', sans-serif;">Product Title | BuyNiger</h5>
                        <p class="text-muted small mb-0" id="preview-desc" style="line-height:1.4;">Provide a meta description below to preview search engine snippets...</p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-dark fw-semibold small">SEO Page Title Tag</label>
                        <input type="text" name="meta_title" id="meta_title" class="form-control" placeholder="Meta Title">
                    </div>
                    <div>
                        <label class="form-label text-dark fw-semibold small">SEO Meta Description</label>
                        <textarea name="meta_description" id="meta_description" class="form-control" rows="3" placeholder="Brief description visible on search results"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Column (Right) -->
        <div class="col-lg-4">
            <!-- Organization -->
            <div class="dashboard-card mb-4">
                <div class="dashboard-card-header">
                    <h3>Categories & Tags</h3>
                </div>
                <div class="dashboard-card-body">
                    <div class="form-group mb-4">
                         <label class="form-label text-dark fw-semibold small">Product Category <span class="text-danger">*</span></label>
                         <select name="category_id" class="form-select" required>
                            <option value="">Choose category...</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                         @error('category_id') <div class="text-danger text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label text-dark fw-semibold small">Search Keywords / Tags</label>
                        <input type="text" name="tags" class="form-control" value="{{ old('tags') }}" placeholder="e.g. Leather, Traditional, Gift">
                        <small class="text-muted small mt-1 d-block">Comma-separated tags for storefront filters.</small>
                    </div>
                </div>
            </div>

            <!-- Pricing -->
             <div class="dashboard-card mb-4">
                <div class="dashboard-card-header">
                    <h3>Base Pricing</h3>
                </div>
                <div class="dashboard-card-body">
                     <div class="form-group mb-4">
                        <label class="form-label text-dark fw-semibold small">Base Listing Price (₦) <span class="text-danger">*</span></label>
                        <input type="number" name="price" class="form-control form-control-lg fw-bold text-dark" value="{{ old('price') }}" min="0" step="0.01" placeholder="0.00" required>
                         @error('price') <div class="text-danger text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label text-dark fw-semibold small">Compare Strikethrough Price (₦)</label>
                        <input type="number" name="compare_price" class="form-control" value="{{ old('compare_price') }}" min="0" step="0.01" placeholder="0.00">
                        <small class="text-muted small mt-1 d-block">Original retail price shown as strikethrough.</small>
                    </div>
                </div>
            </div>

             <!-- Inventory -->
             <div class="dashboard-card mb-4">
                <div class="dashboard-card-header">
                    <h3>Listing Inventory</h3>
                </div>
                <div class="dashboard-card-body">
                     <div class="form-group mb-4">
                        <label class="form-label text-dark fw-semibold small">Product Base SKU</label>
                        <input type="text" name="sku" class="form-control" value="{{ old('sku') }}" placeholder="Auto-generated SKU code">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-dark fw-semibold small">Base Stock Quantity</label>
                        <input type="number" name="quantity" class="form-control" value="{{ old('quantity', 0) }}" min="0">
                         @error('quantity') <div class="text-danger text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
            
             <!-- Publishing Actions -->
             <div class="dashboard-card">
                <div class="dashboard-card-header bg-dark text-white">
                    <h3 class="text-white">Publish Options</h3>
                </div>
                <div class="dashboard-card-body d-flex flex-column gap-2">
                   <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold">
                        <i class="fas fa-paper-plane me-1"></i> Publish Listing
                   </button>
                    <a href="{{ route('vendor.products') }}" class="btn btn-outline-secondary w-100 rounded-pill py-2">
                        Cancel & Exit
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<style>
    .image-preview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 12px;
    }
    .preview-item {
        position: relative;
        aspect-ratio: 1;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid var(--border-color);
        cursor: grab;
    }
    .preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .preview-item .remove-btn {
        position: absolute;
        top: 4px;
        right: 4px;
        width: 20px;
        height: 20px;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ef4444;
        font-size: 10px;
        cursor: pointer;
        border: none;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    // Image Preview & Sortable
    const previewGrid = document.getElementById('image-preview');
    if(previewGrid) {
        new Sortable(previewGrid, { animation: 150, ghostClass: 'opacity-50' });
        
        document.getElementById('images').addEventListener('change', function(e) {
            Array.from(e.target.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'preview-item';
                    div.innerHTML = `<img src="${e.target.result}"><button type="button" class="remove-btn" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>`;
                    previewGrid.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        });
    }

    // SEO Preview updates
    const inputs = ['meta_title', 'meta_description', 'name'];
    inputs.forEach(id => {
        const el = document.querySelector(`[name="${id}"]`);
        if(el) el.addEventListener('input', updateSEO);
    });

    function updateSEO() {
        const title = document.querySelector('[name="meta_title"]').value || document.querySelector('[name="name"]').value || 'Product Title';
        const desc = document.querySelector('[name="meta_description"]').value || 'Provide a meta description below to preview search engine snippets...';
        document.getElementById('preview-title').innerText = title + ' | BuyNiger';
        document.getElementById('preview-desc').innerText = desc.substring(0, 150) + (desc.length > 150 ? '...' : '');
    }

    // Add Variants option row dynamically
    let vIndex = 0;
    document.getElementById('add-variant').addEventListener('click', () => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="text" name="variants[${vIndex}][size]" class="form-control form-control-sm" placeholder="e.g. L"></td>
            <td><input type="text" name="variants[${vIndex}][color]" class="form-control form-control-sm" placeholder="e.g. Blue"></td>
            <td><input type="number" name="variants[${vIndex}][price]" class="form-control form-control-sm" placeholder="0.00"></td>
            <td><input type="number" name="variants[${vIndex}][stock]" class="form-control form-control-sm" value="0"></td>
            <td><input type="text" name="variants[${vIndex}][sku]" class="form-control form-control-sm" placeholder="Variant SKU"></td>
            <td class="text-end"><button type="button" class="btn btn-link text-danger btn-sm p-0" onclick="this.closest('tr').remove()"><i class="fas fa-trash"></i></button></td>
        `;
        document.getElementById('variants-body').appendChild(tr);
        vIndex++;
    });
</script>
@endpush
@endsection
