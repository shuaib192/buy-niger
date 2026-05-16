{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    View: Edit Product
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
    <div class="row">
        <!-- Main Column (Left) -->
        <div class="col-lg-8">
            <!-- 1. Basic Info -->
            <div class="dashboard-card border-0 shadow-sm mb-4">
                <div class="dashboard-card-header bg-white border-0 pt-4 pb-0">
                    <h3 class="h5 font-bold mb-1">Product Information</h3>
                    <p class="text-secondary-500 text-sm">Basic details about your product.</p>
                </div>
                <div class="dashboard-card-body">
                    <div class="form-group mb-4">
                        <label class="form-label font-bold text-xs uppercase text-secondary-500">Product Name</label>
                        <input type="text" name="name" class="form-control form-control-lg font-bold" value="{{ old('name', $product->name) }}" required>
                        @error('name') <div class="text-danger text-xs mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-label font-bold text-xs uppercase text-secondary-500">Description</label>
                        <textarea name="description" class="form-control" rows="6" required>{{ old('description', $product->description) }}</textarea>
                         @error('description') <div class="text-danger text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <!-- 2. Media -->
            <div class="dashboard-card border-0 shadow-sm mb-4">
                <div class="dashboard-card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="h5 font-bold mb-1">Media</h3>
                        <p class="text-secondary-500 text-sm">Drag to reorder. First image is primary.</p>
                    </div>
                </div>
                <div class="dashboard-card-body">
                    <div class="image-upload-area mb-4" id="drop-zone">
                        <input type="file" name="images[]" id="images" multiple accept="image/*" style="display: none;">
                        <label for="images" class="upload-label mb-0">
                            <i class="fas fa-cloud-upload-alt text-primary-300 fa-3x mb-3"></i>
                            <span class="font-bold text-secondary-700">Upload New Images</span>
                        </label>
                    </div>
                    
                    <div id="image-preview" class="image-preview-grid">
                        @foreach($product->images as $image)
                            <div class="preview-item" data-id="{{ $image->id }}">
                                <img src="{{ asset('storage/' . $image->image_path) }}" alt="">
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

             <!-- 3. Variants -->
             <div class="dashboard-card border-0 shadow-sm mb-4">
                <div class="dashboard-card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="h5 font-bold mb-1">Variants</h3>
                        <p class="text-secondary-500 text-sm">Add different sizes or colors.</p>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary shadow-sm" id="add-variant">
                        <i class="fas fa-plus mr-1"></i> Add Option
                    </button>
                </div>
                <div class="dashboard-card-body">
                    <div class="table-responsive">
                       <table class="table table-bordered mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-xs font-bold uppercase text-secondary-500 border-0">Size</th>
                                    <th class="text-xs font-bold uppercase text-secondary-500 border-0">Color</th>
                                    <th class="text-xs font-bold uppercase text-secondary-500 border-0">Price (₦)</th>
                                    <th class="text-xs font-bold uppercase text-secondary-500 border-0">Stock</th>
                                    <th class="text-xs font-bold uppercase text-secondary-500 border-0">SKU</th>
                                    <th class="border-0" width="40"></th>
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
                                        <td><button type="button" class="btn btn-link text-danger btn-sm p-0" onclick="this.closest('tr').remove()"><i class="fas fa-trash"></i></button></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                   <p class="text-secondary-400 text-xs mt-3">Only fill what is necessary.</p>
                </div>
            </div>

            <!-- 4. SEO -->
            <div class="dashboard-card border-0 shadow-sm mb-4">
                <div class="dashboard-card-header bg-white border-0 pt-4 pb-0">
                    <h3 class="h5 font-bold mb-1">Search Engine Listing</h3>
                    <p class="text-secondary-500 text-sm">Preview how your product appears in search results.</p>
                </div>
                <div class="dashboard-card-body">
                     <div class="form-group mb-4">
                        <div class="seo-preview-card p-3 bg-light rounded border border-light mb-3">
                            <div class="seo-preview-url text-success text-xs mb-1">buyniger.com.ng > product > {{ $product->slug }}</div>
                            <div class="seo-preview-title h5 text-primary mb-1 font-bold" id="preview-title">{{ $product->meta_title ?: $product->name }} | BuyNiger</div>
                            <div class="seo-preview-desc text-secondary-600 text-sm" id="preview-desc">{{ $product->meta_description ?: 'No description set.' }}</div>
                        </div>
                    </div>
                     <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label font-bold text-xs uppercase text-secondary-500">Page Title</label>
                            <input type="text" name="meta_title" id="meta_title" class="form-control" value="{{ $product->meta_title }}" placeholder="SEO Title">
                        </div>
                         <div class="col-md-12">
                            <label class="form-label font-bold text-xs uppercase text-secondary-500">Meta Description</label>
                            <textarea name="meta_description" id="meta_description" class="form-control" rows="3" placeholder="SEO Description">{{ $product->meta_description }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Column (Right) -->
        <div class="col-lg-4">
            <!-- 1. Status -->
             <div class="dashboard-card border-0 shadow-sm mb-4">
                <div class="dashboard-card-header bg-white border-0 pt-4 pb-0">
                    <h3 class="h5 font-bold mb-1">Publishing</h3>
                </div>
                <div class="dashboard-card-body">
                   <button type="submit" class="btn btn-primary btn-block shadow-sm py-2 mb-3 font-bold">
                        <i class="fas fa-save mr-2"></i> Save Changes
                   </button>
                    <a href="{{ route('vendor.products') }}" class="btn btn-outline-secondary btn-block border-0 text-secondary-500">
                        Cancel
                    </a>
                </div>
            </div>

             <!-- 2. Organization -->
            <div class="dashboard-card border-0 shadow-sm mb-4">
                <div class="dashboard-card-header bg-white border-0 pt-4 pb-0">
                    <h3 class="h5 font-bold mb-1">Organization</h3>
                </div>
                <div class="dashboard-card-body">
                    <div class="form-group mb-4">
                         <label class="form-label font-bold text-xs uppercase text-secondary-500">Category</label>
                         <select name="category_id" class="form-control custom-select" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                         @error('category_id') <div class="text-danger text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group mb-0">
                        <label class="form-label font-bold text-xs uppercase text-secondary-500">Tags</label>
                        <input type="text" name="tags" class="form-control" value="{{ old('tags', implode(', ', $product->tags->pluck('name')->toArray())) }}" placeholder="Vintage, Summer, Sale">
                        <small class="text-secondary-400 text-xs">Comma separated.</small>
                    </div>
                </div>
            </div>

            <!-- 3. Pricing -->
             <div class="dashboard-card border-0 shadow-sm mb-4">
                <div class="dashboard-card-header bg-white border-0 pt-4 pb-0">
                    <h3 class="h5 font-bold mb-1">Pricing</h3>
                </div>
                <div class="dashboard-card-body">
                     <div class="form-group mb-4">
                        <label class="form-label font-bold text-xs uppercase text-secondary-500">Price (₦)</label>
                        <input type="number" name="price" class="form-control font-bold text-dark" value="{{ old('price', $product->price) }}" min="0" step="0.01" required>
                         @error('price') <div class="text-danger text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group mb-0">
                        <label class="form-label font-bold text-xs uppercase text-secondary-500">Compare Price (₦)</label>
                        <input type="number" name="compare_price" class="form-control" value="{{ old('compare_price', $product->sale_price) }}" min="0" step="0.01">
                        <small class="text-secondary-400 text-xs">Original price (strikethrough).</small>
                    </div>
                </div>
            </div>

             <!-- 4. Inventory -->
             <div class="dashboard-card border-0 shadow-sm mb-4">
                <div class="dashboard-card-header bg-white border-0 pt-4 pb-0">
                    <h3 class="h5 font-bold mb-1">Inventory</h3>
                </div>
                <div class="dashboard-card-body">
                     <div class="form-group mb-4">
                        <label class="form-label font-bold text-xs uppercase text-secondary-500">SKU (Stock Keeping Unit)</label>
                        <input type="text" name="sku" class="form-control" value="{{ old('sku', $product->sku) }}">
                    </div>
                    <div class="form-group mb-0">
                        <label class="form-label font-bold text-xs uppercase text-secondary-500">Quantity</label>
                        <input type="number" name="quantity" class="form-control" value="{{ old('quantity', $product->quantity) }}" min="0" required>
                         @error('quantity') <div class="text-danger text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<style>
    .image-upload-area {
        border: 2px dashed #e2e8f0;
        border-radius: 12px;
        padding: 40px;
        text-align: center;
        background: #f8fafc;
        transition: all 0.2s;
    }
    .image-upload-area:hover {
        border-color: #3b82f6;
        background: #eff6ff;
    }
    .upload-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        cursor: pointer;
    }
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
        border: 1px solid #e2e8f0;
        cursor: grab;
    }
    .preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .preview-item.to-delete {
        opacity: 0.3;
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

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    // Sortable images
    const previewGrid = document.getElementById('image-preview');
    if(previewGrid){
        new Sortable(previewGrid, { animation: 150, ghostClass: 'opacity-50' });

        // Handle new image uploads
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
            btn.innerHTML = '<i class="fas fa-undo"></i>';
        }
    }

    // Variants logic
    let vIndex = {{ $product->variants->count() }};
    document.getElementById('add-variant').addEventListener('click', function() {
        const tbody = document.getElementById('variants-body');
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><input type="text" name="variants[${vIndex}][size]" class="form-control form-control-sm"></td>
            <td><input type="text" name="variants[${vIndex}][color]" class="form-control form-control-sm"></td>
            <td><input type="number" name="variants[${vIndex}][price]" class="form-control form-control-sm"></td>
            <td><input type="number" name="variants[${vIndex}][stock]" class="form-control form-control-sm" value="0"></td>
            <td><input type="text" name="variants[${vIndex}][sku]" class="form-control form-control-sm"></td>
            <td><button type="button" class="btn btn-link text-danger btn-sm p-0" onclick="this.closest('tr').remove()"><i class="fas fa-trash"></i></button></td>
        `;
        tbody.appendChild(row);
        vIndex++;
    });

    // SEO Preview
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
@endsection
