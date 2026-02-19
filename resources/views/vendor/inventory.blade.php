{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    View: Inventory Management (Premium)
--}}
@extends('layouts.app')

@section('title', 'Inventory Management')
@section('page_title', 'Inventory & Stock')

@section('sidebar')
    @include('vendor.partials.sidebar')
@endsection

@section('content')
<div class="inventory-page">
    {{-- Page Header --}}
    <div class="page-header-premium">
        <div>
            <h1 class="page-title">Inventory & Stock</h1>
            <p class="page-subtitle">Track stock levels, adjust quantities, and manage product variants.</p>
        </div>
        <div class="header-search">
            <i class="fas fa-search"></i>
            <input type="text" id="inventorySearch" placeholder="Search products..." value="{{ request('search') }}">
        </div>
    </div>

    <div class="row g-4">
        {{-- Stock Levels Table --}}
        <div class="col-lg-8">
            <div class="premium-card">
                <div class="card-header-premium d-flex justify-content-between align-items-center">
                    <h3><i class="fas fa-boxes mr-2 text-primary"></i>Stock Levels</h3>
                    <span class="item-count">{{ $products->total() }} products</span>
                </div>
                <div class="card-body-premium p-0">
                    <div class="table-responsive">
                        <table class="inventory-table">
                            <thead>
                                <tr>
                                    <th>Product / Variant</th>
                                    <th>SKU</th>
                                    <th>Stock</th>
                                    <th width="180">Quick Adjust</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    {{-- Product Header Row --}}
                                    <tr class="product-header-row">
                                        <td colspan="4">
                                            <div class="d-flex align-items-center gap-2">
                                                @if($product->primary_image_url)
                                                    <img src="{{ $product->primary_image_url }}" alt="" class="product-mini-thumb">
                                                @else
                                                    <div class="product-mini-thumb-placeholder"><i class="fas fa-box"></i></div>
                                                @endif
                                                <div>
                                                    <span class="product-header-name">{{ $product->name }}</span>
                                                    <span class="product-category-badge">{{ $product->category->name ?? 'Uncategorized' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    @if($product->variants->count() > 0)
                                        @foreach($product->variants as $variant)
                                            <tr class="variant-row">
                                                <td>
                                                    <div class="variant-label">
                                                        <span class="variant-connector">└</span>
                                                        {{ $variant->size ? 'Size: '.$variant->size : '' }}
                                                        {{ $variant->color ? ($variant->size ? ', ' : '').'Color: '.$variant->color : '' }}
                                                    </div>
                                                </td>
                                                <td><code class="sku-code">{{ $variant->sku }}</code></td>
                                                <td>
                                                    <span class="stock-badge {{ $variant->stock_quantity <= 5 ? 'stock-critical' : ($variant->stock_quantity <= 15 ? 'stock-low' : 'stock-ok') }}">
                                                        {{ $variant->stock_quantity }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="adjust-group">
                                                        <input type="number" class="adjust-input stock-adjust-input" placeholder="+/−" data-pid="{{ $product->id }}" data-vid="{{ $variant->id }}">
                                                        <button class="adjust-btn btn-adjust" type="button"><i class="fas fa-check"></i></button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr class="variant-row">
                                            <td><div class="variant-label"><span class="variant-connector">└</span> Base Product</div></td>
                                            <td><code class="sku-code">{{ $product->sku }}</code></td>
                                            <td>
                                                <span class="stock-badge {{ $product->quantity <= 5 ? 'stock-critical' : ($product->quantity <= 15 ? 'stock-low' : 'stock-ok') }}">
                                                    {{ $product->quantity }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="adjust-group">
                                                    <input type="number" class="adjust-input stock-adjust-input" placeholder="+/−" data-pid="{{ $product->id }}" data-vid="">
                                                    <button class="adjust-btn btn-adjust" type="button"><i class="fas fa-check"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="4">
                                            <div class="empty-state-premium">
                                                <div class="empty-icon"><i class="fas fa-cubes"></i></div>
                                                <h4>No products found</h4>
                                                <p>Add products to start managing your inventory.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($products->hasPages())
                    <div class="pagination-bar">{{ $products->links() }}</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar: Alerts + History --}}
        <div class="col-lg-4">
            {{-- Critical Stock Alerts --}}
            @php
                $lowStock = $products->filter(fn($p) => $p->quantity <= 5 && $p->variants->count() == 0);
                $lowStockVariants = collect();
                foreach($products as $p) {
                    if($p->variants->count() > 0) {
                        $lowStockVariants = $lowStockVariants->merge($p->variants->filter(fn($v) => $v->stock_quantity <= 5));
                    }
                }
            @endphp
            @if($lowStock->count() > 0 || $lowStockVariants->count() > 0)
                <div class="premium-card alert-card mb-4">
                    <div class="card-header-premium alert-header">
                        <h3><i class="fas fa-exclamation-triangle mr-2"></i>Critical Stock</h3>
                        <span class="alert-count">{{ $lowStock->count() + $lowStockVariants->count() }}</span>
                    </div>
                    <div class="card-body-premium p-0">
                        @foreach($lowStock as $p)
                            <div class="alert-item">
                                <span class="alert-name">{{ Str::limit($p->name, 25) }}</span>
                                <span class="alert-qty">{{ $p->quantity }} left</span>
                            </div>
                        @endforeach
                        @foreach($lowStockVariants as $v)
                            <div class="alert-item">
                                <span class="alert-name">{{ Str::limit($v->product->name ?? 'Product', 15) }} <small class="text-muted">({{ $v->size }}/{{ $v->color }})</small></span>
                                <span class="alert-qty">{{ $v->stock_quantity }} left</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Stock History --}}
            <div class="premium-card">
                <div class="card-header-premium"><h3><i class="fas fa-history mr-2 text-primary"></i>Recent Movements</h3></div>
                <div class="card-body-premium p-0">
                    @forelse($history as $item)
                        <div class="history-item">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <span class="history-time">{{ $item->created_at->diffForHumans() }}</span>
                                <span class="stock-change {{ $item->change_amount > 0 ? 'change-positive' : 'change-negative' }}">
                                    {{ $item->change_amount > 0 ? '+' : '' }}{{ $item->change_amount }}
                                </span>
                            </div>
                            <div class="history-product">{{ Str::limit($item->product->name ?? 'Deleted Product', 30) }}</div>
                            @if($item->variant)
                                <div class="history-variant">{{ $item->variant->size }} / {{ $item->variant->color }}</div>
                            @endif
                            <div class="history-type">{{ ucfirst($item->type) }}</div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-clock fa-2x mb-2 d-block" style="color: #e2e8f0;"></i>
                            <p class="mb-0" style="font-size: 13px;">No recent movements</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Toast Notification --}}
<div id="toastContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

<style>
    .inventory-page { animation: fadeInUp 0.4s ease; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }

    .page-header-premium { display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 16px; }
    .page-title { font-size: 24px; font-weight: 800; color: #0f172a; margin: 0 0 4px; letter-spacing: -0.02em; }
    .page-subtitle { color: #64748b; font-size: 14px; margin: 0; font-weight: 500; }

    .header-search { display: flex; align-items: center; gap: 10px; background: white; border: 1px solid #e2e8f0; border-radius: 14px; padding: 8px 16px; min-width: 260px; transition: all 0.2s; }
    .header-search:focus-within { border-color: #0066FF; box-shadow: 0 0 0 3px rgba(0,102,255,0.1); }
    .header-search i { color: #94a3b8; font-size: 14px; }
    .header-search input { border: none; outline: none; font-size: 14px; font-weight: 500; width: 100%; background: transparent; color: #0f172a; }

    /* Cards */
    .premium-card { background: white; border: 1px solid #f1f5f9; border-radius: 20px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.03); }
    .card-header-premium { padding: 18px 24px; border-bottom: 1px solid #f1f5f9; }
    .card-header-premium h3 { font-size: 15px; font-weight: 700; color: #0f172a; margin: 0; display: flex; align-items: center; }
    .card-body-premium { padding: 20px 24px; }
    .item-count { font-size: 12px; font-weight: 600; color: #94a3b8; background: #f8fafc; padding: 4px 12px; border-radius: 20px; }
    .text-primary { color: #0066FF; }

    /* Inventory Table */
    .inventory-table { width: 100%; border-collapse: collapse; }
    .inventory-table thead th { background: #fafbfc; color: #64748b; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; padding: 12px 16px; border-bottom: 1px solid #f1f5f9; }
    .inventory-table tbody td { padding: 12px 16px; border-bottom: 1px solid #f8fafc; vertical-align: middle; }

    .product-header-row { background: #fafbfc; }
    .product-header-row td { border-bottom: none; padding: 14px 16px; }
    .product-mini-thumb { width: 32px; height: 32px; border-radius: 8px; object-fit: cover; border: 1px solid #f1f5f9; }
    .product-mini-thumb-placeholder { width: 32px; height: 32px; border-radius: 8px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #cbd5e1; font-size: 12px; }
    .product-header-name { font-weight: 700; color: #0f172a; font-size: 14px; margin-right: 8px; }
    .product-category-badge { font-size: 10px; font-weight: 700; color: #64748b; background: #e2e8f0; padding: 2px 8px; border-radius: 6px; text-transform: uppercase; }
    .gap-2 { gap: 0.5rem; }

    .variant-row td { background: white; }
    .variant-label { display: flex; align-items: center; gap: 6px; font-size: 13px; color: #475569; padding-left: 24px; }
    .variant-connector { color: #cbd5e1; font-weight: 300; font-size: 16px; }
    .sku-code { font-size: 11px; background: #f1f5f9; color: #475569; padding: 2px 8px; border-radius: 4px; font-family: 'JetBrains Mono', monospace; }

    .stock-badge { display: inline-flex; align-items: center; justify-content: center; min-width: 36px; padding: 4px 10px; border-radius: 8px; font-size: 13px; font-weight: 700; }
    .stock-ok { background: #ecfdf5; color: #059669; }
    .stock-low { background: #fffbeb; color: #d97706; }
    .stock-critical { background: #fef2f2; color: #dc2626; animation: blink 2s infinite; }
    @keyframes blink { 0%,100% { opacity: 1; } 50% { opacity: 0.6; } }

    .adjust-group { display: flex; gap: 4px; }
    .adjust-input { width: 80px; padding: 6px 10px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 13px; font-weight: 600; text-align: center; outline: none; background: #fafbfc; transition: all 0.2s; }
    .adjust-input:focus { border-color: #0066FF; background: white; box-shadow: 0 0 0 3px rgba(0,102,255,0.1); }
    .adjust-btn { width: 36px; height: 36px; border-radius: 10px; border: none; background: #0066FF; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 12px; transition: all 0.2s; }
    .adjust-btn:hover { background: #0052cc; transform: scale(1.05); }

    /* Alert Card */
    .alert-card { border-color: #fecaca; }
    .alert-header { background: #fef2f2; }
    .alert-header h3 { color: #dc2626; }
    .alert-count { background: #dc2626; color: white; font-size: 11px; font-weight: 800; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
    .alert-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 24px; border-bottom: 1px solid #fef2f2; }
    .alert-name { font-size: 13px; color: #0f172a; font-weight: 600; }
    .alert-qty { font-size: 12px; font-weight: 700; color: #dc2626; background: #fef2f2; padding: 2px 10px; border-radius: 6px; }

    /* History */
    .history-item { padding: 14px 24px; border-bottom: 1px solid #f8fafc; }
    .history-time { font-size: 11px; color: #94a3b8; font-weight: 500; }
    .stock-change { font-size: 12px; font-weight: 700; padding: 2px 10px; border-radius: 6px; }
    .change-positive { background: #ecfdf5; color: #059669; }
    .change-negative { background: #fef2f2; color: #dc2626; }
    .history-product { font-size: 14px; font-weight: 700; color: #0f172a; }
    .history-variant { font-size: 12px; color: #64748b; }
    .history-type { font-size: 11px; color: #94a3b8; text-transform: uppercase; font-weight: 600; letter-spacing: 0.03em; margin-top: 4px; }

    .pagination-bar { padding: 16px 24px; border-top: 1px solid #f1f5f9; }

    .empty-state-premium { text-align: center; padding: 60px 20px; }
    .empty-icon { width: 80px; height: 80px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 32px; color: #94a3b8; }
    .empty-state-premium h4 { font-weight: 700; color: #0f172a; }
    .empty-state-premium p { color: #94a3b8; font-size: 14px; }

    .g-4 > * { padding: 0.75rem; }
    @media (max-width: 768px) { .page-header-premium { flex-direction: column; align-items: flex-start; } .header-search { width: 100%; } }
</style>

@push('scripts')
<script>
    document.getElementById('inventorySearch').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            window.location.href = '{{ route("vendor.inventory") }}?search=' + this.value;
        }
    });

    function showToast(message, type) {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.style.cssText = `padding:12px 20px;border-radius:12px;color:white;font-weight:600;font-size:14px;margin-bottom:8px;box-shadow:0 8px 20px rgba(0,0,0,0.15);animation:slideIn 0.3s ease;background:${type==='success'?'#059669':'#dc2626'}`;
        toast.textContent = message;
        container.appendChild(toast);
        setTimeout(() => { toast.style.opacity = '0'; toast.style.transition = 'opacity 0.3s'; setTimeout(() => toast.remove(), 300); }, 3000);
    }

    document.querySelectorAll('.btn-adjust').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.closest('.adjust-group').querySelector('.stock-adjust-input');
            const pId = input.dataset.pid;
            const vId = input.dataset.vid;
            const change = input.value;

            if (!change || change == 0) return;

            const originalBtnContent = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            this.disabled = true;

            fetch('{{ route("vendor.inventory.update") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
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
                    showToast('Stock updated successfully!', 'success');
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
@endsection
