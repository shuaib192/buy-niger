{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin
    View: Vendor — Edit Product — Premium v2.0
--}}
@extends('layouts.app')

@section('title', 'Edit Product: ' . $product->name)
@section('page_title', 'Edit Product')

@section('sidebar')
    @include('vendor.partials.sidebar')
@endsection

@section('content')
<form action="{{ route('vendor.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row g-4">
        <!-- Main Column (Left) -->
        <div class="col-lg-8">
            <!-- Basic Info -->
            <div class="dashboard-card mb-4">
                <div class="dashboard-card-header">
                    <div>
                        <h3 class="mb-0">Product Details</h3>
                        <p class="text-muted small mb-0">Modify title information and descriptions of your item.</p>
                    </div>
                </div>
                <div class="dashboard-card-body">
                    <div class="form-group mb-4">
                        <label class="form-label text-dark fw-semibold small">Product Title / Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-lg fw-bold" value="{{ old('name', $product->name) }}" required>
                        @error('name') <div class="text-danger text-xs mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-label text-dark fw-semibold small">Detailed Product Description <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="6" required>{{ old('description', $product->description) }}</textarea>
                         @error('description') <div class="text-danger text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <!-- Media -->
            <div class="dashboard-card mb-4">
                <div class="dashboard-card-header">
                    <div>
                        <h3 class="mb-0">Product Media & Gallery</h3>
                        <p class="text-muted small mb-0">Drag to reorder images. First image is the primary display thumb.</p>
                    </div>
                </div>
                <div class="dashboard-card-body">
                    <div class="image-upload-area p-5 border border-dashed rounded-3 text-center bg-light mb-4" id="drop-zone" style="border-color: var(--border-color) !important; transition: all 0.2s;">
                        <input type="file" name="images[]" id="images" multiple accept="image/*" style="display: none;">
                        <label for="images" class="upload-label mb-0" style="cursor: pointer;">
                            <i class="fas fa-cloud-arrow-up text-indigo fa-3x mb-3"></i>
                            <div class="fw-bold text-dark">Click to upload new files</div>
                            <small class="text-muted">or drag & drop images here</small>
                        </label>
                    </div>
                    
                    <div id="image-preview" class="image-preview-grid">
                        @foreach($product->images as $image)
                            <div class="preview-item rounded-3 border bg-light overflow-hidden" data-id="{{ $image->id }}" style="border-color: var(--border-color) !important;">
                                <img src="{{ asset('storage/' . $image->image_path) }}" alt="" style="width:100%; height:100%; object-fit:cover;">
                                <div class="remove-img-wrap">
                                    <input type="checkbox" name="delete_images[]" value="{{ $image->id }}" id="del-img-{{ $image->id }}" style="display:none;">
                                    <button type="button" class="remove-btn" onclick="markForDelete(this, {{ $image->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <input type="hidden" name="image_order[]" value="{{ $image->id }}">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

             <!-- Variants -->
             <div class="dashboard-card mb-4">
                <div class="dashboard-card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">Product Custom Variants</h3>
                        <p class="text-muted small mb-0">Specify sizes, color values, prices, and stock units.</p>
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
                            <tbody id="variants-body">
                                @foreach($product->variants as $index => $variant)
                                    <tr>
                                        <td><input type="text" name="variants[{{ $index }}][size]" class="form-control form-control-sm" value="{{ $variant->size }}"></td>
                                        <td><input type="text" name="variants[{{ $index }}][color]" class="form-control form-control-sm" value="{{ $variant->color }}"></td>
                                        <td><input type="number" name="variants[{{ $index }}][price]" class="form-control form-control-sm" value="{{ $variant->price }}"></td>
                                        <td><input type="number" name="variants[{{ $index }}][stock]" class="form-control form-control-sm" value="{{ $variant->stock_quantity }}"></td>
                                        <td><input type="text" name="variants[{{ $index }}][sku]" class="form-control form-control-sm" value="{{ $variant->sku }}"></td>
                                        <td class="text-end"><button type="button" class="btn btn-link text-danger btn-sm p-0" onclick="this.closest('tr').remove()"><i class="fas fa-trash"></i></button></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                   <p class="text-muted small mt-3"><i class="fas fa-circle-info me-1"></i> Empty price values fall back to base listing prices.</p>
                </div>
            </div>

            <!-- SEO -->
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <div>
                        <h3 class="mb-0">Search Engine Listing</h3>
                        <p class="text-muted small mb-0">Customize page header elements for SEO listing previews.</p>
                    </div>
                </div>
                <div class="dashboard-card-body">
                     <div class="p-3 bg-light rounded-3 border mb-4" style="border-color: var(--border-color) !important;">
                        <div class="text-success small mb-1" style="font-size:11px;">buyniger.com.ng > product > {{ $product->slug }}</div>
                        <h5 class="text-primary fw-bold mb-1" id="preview-title" style="font-size: 1.05rem; font-family:'Outfit', sans-serif;">{{ $product->meta_title ?: $product->name }} | BuyNiger</h5>
                        <p class="text-muted small mb-0" id="preview-desc" style="line-height:1.4;">{{ $product->meta_description ?: 'No description set.' }}</p>
                    </div>
                     <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label text-dark fw-semibold small">SEO Page Title Tag</label>
                            <input type="text" name="meta_title" id="meta_title" class="form-control" value="{{ $product->meta_title }}" placeholder="SEO Title">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label text-dark fw-semibold small">SEO Meta Description</label>
                            <textarea name="meta_description" id="meta_description" class="form-control" rows="3" placeholder="SEO Description">{{ $product->meta_description }}</textarea>
                        </div>
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
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                         @error('category_id') <div class="text-danger text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label text-dark fw-semibold small">Search Keywords / Tags</label>
                        <input type="text" name="tags" class="form-control" value="{{ old('tags', implode(', ', $product->tags->pluck('name')->toArray())) }}" placeholder="Vintage, Summer, Sale">
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
                        <input type="number" name="price" class="form-control form-control-lg fw-bold text-dark" value="{{ old('price', $product->price) }}" min="0" step="0.01" required>
                         @error('price') <div class="text-danger text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label text-dark fw-semibold small">Compare Strikethrough Price (₦)</label>
                        <input type="number" name="compare_price" class="form-control" value="{{ old('compare_price', $product->sale_price) }}" min="0" step="0.01">
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
                        <input type="text" name="sku" class="form-control" value="{{ old('sku', $product->sku) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-dark fw-semibold small">Base Stock Quantity</label>
                        <input type="number" name="quantity" class="form-control" value="{{ old('quantity', $product->quantity) }}" min="0" required>
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
                        <i class="fas fa-save me-1"></i> Save Changes
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
    .preview-item.to-delete {
        opacity: 0.25;
        filter: grayscale(1);
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
        z-index: 10;
    }
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    // Sortable images
    const previewGrid = document.getElementById('image-preview');
    if(previewGrid){
        new Sortable(previewGrid, { animation: 150, ghostClass: 'opacity-50' });

        // Handle new image uploads preview
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

    function markForDelete(btn, id) {
        const item = btn.closest('.preview-item');
        const checkbox = document.getElementById('del-img-' + id);
        if (checkbox.checked) {
            checkbox.checked = false;
            item.classList.remove('to-delete');
            btn.innerHTML = '<i class="fas fa-trash"></i>';
        } else {
            checkbox.checked = true;
            item.classList.add('to-delete');
            btn.innerHTML = '<i class="fas fa-rotate-left"></i>';
        }
    }

    // Variants adding logic
    let vIndex = {{ $product->variants->count() }};
    document.getElementById('add-variant').addEventListener('click', function() {
        const tbody = document.getElementById('variants-body');
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><input type="text" name="variants[${vIndex}][size]" class="form-control form-control-sm"></td>
            <td><input type="text" name="variants[${vIndex}][color]" class="form-control form-control-sm"></td>
            <td><input type="number" name="variants[${vIndex}][price]" class="form-control form-control-sm"></td>
            <td><input type="number" name="variants[${vIndex}][stock]" class="form-control form-control-sm" value="0"></td>
            <td><input type="text" name="variants[${vIndex}][sku]" class="form-control form-control-sm" placeholder="Variant SKU"></td>
            <td class="text-end"><button type="button" class="btn btn-link text-danger btn-sm p-0" onclick="this.closest('tr').remove()"><i class="fas fa-trash"></i></button></td>
        `;
        tbody.appendChild(row);
        vIndex++;
    });

    // SEO Live Preview
    const metaTitleInput = document.getElementById('meta_title');
    const metaDescInput = document.getElementById('meta_description');
    const nameInput = document.querySelector('input[name="name"]');
    const previewTitle = document.getElementById('preview-title');
    const previewDesc = document.getElementById('preview-desc');

    function updateSEO() {
        const title = metaTitleInput.value || nameInput.value || 'Product Name';
        previewTitle.innerText = title + ' | BuyNiger';
        previewDesc.innerText = metaDescInput.value || 'No description set.';
    }

    if(metaTitleInput) metaTitleInput.addEventListener('input', updateSEO);
    if(metaDescInput) metaDescInput.addEventListener('input', updateSEO);
    if(nameInput) nameInput.addEventListener('input', updateSEO);
</script>
@endpush
