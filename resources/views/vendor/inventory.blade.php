{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    View: Inventory Management
--}}
@extends('layouts.app')

@section('title', 'Inventory Management')
@section('page_title', 'Inventory & Stock')

@section('sidebar')
    @include('vendor.partials.sidebar')
@endsection

@section('content')
    <div class="row">
        <!-- Inventory List -->
        <div class="col-lg-8">
            <div class="dashboard-card border-0 shadow-sm mb-4">
                <div class="dashboard-card-header bg-white border-0 py-4 d-flex justify-content-between align-items-center">
                    <h3 class="h5 font-bold mb-0">Stock Levels</h3>
                <div class="search-input-group" style="width: 250px;">
                    <i class="fas fa-search"></i>
                    <input type="text" id="inventorySearch" class="form-control form-control-sm" placeholder="Search inventory..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="dashboard-card-body p-0">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Stock</th>
                                <th width="200">Quick Adjust</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr class="bg-light">
                                    <td colspan="4">
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-secondary mr-2">{{ $product->category->name ?? 'Uncategorized' }}</span>
                                            <strong class="text-secondary-900">{{ $product->name }}</strong>
                                        </div>
                                    </td>
                                </tr>
                                
                                @if($product->variants->count() > 0)
                                    @foreach($product->variants as $variant)
                                        <tr class="variant-row">
                                            <td class="pl-5">
                                                <div class="d-flex align-items-center">
                                                    <div class="text-xs text-secondary-500 mr-2">—</div>
                                                    <span class="text-sm">
                                                        {{ $variant->size ? 'Size: '.$variant->size : '' }}
                                                        {{ $variant->color ? ($variant->size ? ', ' : '').'Color: '.$variant->color : '' }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td><code class="text-xs">{{ $variant->sku }}</code></td>
                                            <td>
                                                <span class="badge badge-{{ $variant->stock_quantity <= 5 ? 'danger' : 'primary' }}-soft text-{{ $variant->stock_quantity <= 5 ? 'danger' : 'primary' }}">
                                                    {{ $variant->stock_quantity }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <input type="number" class="form-control stock-adjust-input" placeholder="+/-" data-pid="{{ $product->id }}" data-vid="{{ $variant->id }}">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-primary btn-adjust" type="button"><i class="fas fa-check"></i></button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td class="pl-5">Base Product</td>
                                        <td><code class="text-xs">{{ $product->sku }}</code></td>
                                        <td>
                                            <span class="badge badge-{{ $product->quantity <= 5 ? 'danger' : 'primary' }}-soft text-{{ $product->quantity <= 5 ? 'danger' : 'primary' }}">
                                                {{ $product->quantity }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <input type="number" class="form-control stock-adjust-input" placeholder="+/-" data-pid="{{ $product->id }}" data-vid="">
                                                <div class="input-group-append">
                                                    <button class="btn btn-primary btn-adjust" type="button"><i class="fas fa-check"></i></button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr><td colspan="4" class="text-center py-5">No products found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
        </div>

        <!-- Right Side: History & Alerts -->
        <div class="col-lg-4">
            <!-- Low Stock Alerts -->
            @php
                $lowStock = $products->filter(fn($p) => $p->quantity <= 5 && $p->variants->count() == 0);
                // Correctly handle Paginator by strictly working with the collection
                $lowStockVariants = collect();
                foreach($products as $p) {
                    if($p->variants->count() > 0) {
                        $lowStockVariants = $lowStockVariants->merge($p->variants->filter(fn($v) => $v->stock_quantity <= 5));
                    }
                }
            @endphp
            @if($lowStock->count() > 0 || $lowStockVariants->count() > 0)
                <div class="dashboard-card mb-4 border-danger-100">
                    <div class="dashboard-card-header bg-danger-50">
                        <h3 class="text-danger"><i class="fas fa-bell mr-2"></i> Critical Stock</h3>
                    </div>
                    <div class="dashboard-card-body p-0">
                        <ul class="list-group list-group-flush">
                            @foreach($lowStock as $p)
                                <li class="list-group-item text-sm d-flex justify-content-between p-3">
                                    <span>{{ Str::limit($p->name, 25) }}</span>
                                    <strong class="text-danger">{{ $p->quantity }} left</strong>
                                </li>
                            @endforeach
                            @foreach($lowStockVariants as $v)
                                <li class="list-group-item text-sm d-flex justify-content-between p-3">
                                    <span>{{ Str::limit($v->product->name, 15) }} ({{ $v->size }}-{{ $v->color }})</span>
                                    <strong class="text-danger">{{ $v->stock_quantity }} left</strong>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Stock History -->
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h3>Recent Movements</h3>
                </div>
                <div class="dashboard-card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($history as $item)
                            <div class="list-group-item p-3">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <small class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
                                    <span class="badge badge-{{ $item->change_amount > 0 ? 'success' : 'danger' }}-soft text-{{ $item->change_amount > 0 ? 'success' : 'danger' }}">
                                        {{ $item->change_amount > 0 ? '+' : '' }}{{ $item->change_amount }}
                                    </span>
                                </div>
                                <div class="text-sm font-weight-bold">{{ Str::limit($item->product->name, 30) }}</div>
                                @if($item->variant)
                                    <div class="text-xs text-secondary-500">{{ $item->variant->size }} / {{ $item->variant->color }}</div>
                                @endif
                                <div class="text-xs mt-1 text-secondary-400">Type: {{ ucfirst($item->type) }}</div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-muted">No recent movements.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Search handling
        document.getElementById('inventorySearch').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                window.location.href = '{{ route('vendor.inventory') }}?search=' + this.value;
            }
        });

        // Quick adjust logic
        document.querySelectorAll('.btn-adjust').forEach(btn => {
            btn.addEventListener('click', function() {
                const input = this.closest('.input-group').querySelector('.stock-adjust-input');
                const pId = input.dataset.pid;
                const vId = input.dataset.vid;
                const change = input.value;

                if (!change || change == 0) return;

                const originalBtnContent = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                this.disabled = true;

                fetch('{{ route('vendor.inventory.update') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: pId,
                        variant_id: vId,
                        change: change,
                        type: change > 0 ? 'restock' : 'adjustment'
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast('Stock updated successfully', 'success');
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        showToast('Error updating stock', 'error');
                        this.innerHTML = originalBtnContent;
                        this.disabled = false;
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('Network error occurred', 'error');
                    this.innerHTML = originalBtnContent;
                    this.disabled = false;
                });
            });
        });
    </script>
    @endpush

    <style>
        .variant-row td {
            background-color: #fafafa;
        }
        .text-xs { font-size: 0.75rem; }
        .font-weight-bold { font-weight: 600; }
        .badge-success-soft { background: #ecfdf5; color: #059669; }
        .badge-danger-soft { background: #fef2f2; color: #dc2626; }
        .badge-primary-soft { background: #eff6ff; color: #2563eb; }
        .text-secondary-900 { color: #0f172a; }
        .text-secondary-500 { color: #64748b; }
        .text-secondary-400 { color: #94a3b8; }
    </style>
@endsection
