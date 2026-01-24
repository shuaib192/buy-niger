{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    View: Inventory Management
--}}
@extends('layouts.app')

@section('title', 'Inventory Management')
@section('page_title', 'Inventory & Stock Logs')

@section('sidebar')
    @include('vendor.partials.sidebar')
@endsection

@section('content')
<div class="row">
    <!-- Stock Management -->
    <div class="col-md-7">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3>Stock Levels</h3>
            </div>
            <div class="dashboard-card-body">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Current Stock</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $product->primary_image_url }}" style="width: 40px; height: 40px; border-radius: 4px; object-fit: cover; margin-right: 10px;">
                                            <div>
                                                <div class="font-bold">{{ Str::limit($product->name, 25) }}</div>
                                                <small class="text-muted">{{ $product->sku }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $product->quantity <= 5 ? 'danger' : 'success' }}">
                                            {{ $product->quantity }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="toggleEdit('row-{{ $product->id }}')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr id="row-{{ $product->id }}" style="display: none; background: #f9f9f9;">
                                    <td colspan="3" class="p-3">
                                        <form action="{{ route('vendor.inventory.update', $product->id) }}" method="POST" class="d-flex align-items-center gap-2">
                                            @csrf
                                            @method('POST')
                                            <input type="number" name="quantity" class="form-control form-control-sm" value="{{ $product->quantity }}" style="width: 100px;" required>
                                            <input type="text" name="reason" class="form-control form-control-sm" placeholder="Reason (e.g. Restock)" required>
                                            <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                            <button type="button" class="btn btn-secondary btn-sm" onclick="toggleEdit('row-{{ $product->id }}')">Cancel</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No products found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Logs -->
    <div class="col-md-5">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3>Stock History Log</h3>
            </div>
            <div class="dashboard-card-body">
                <ul class="list-group list-group-flush">
                    @forelse($logs as $log)
                        <li class="list-group-item px-0">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong class="text-primary">{{ $log->product->name ?? 'Unknown Product' }}</strong>
                                    <div class="small text-muted">{{ $log->reason }}</div>
                                    <div class="small text-muted">{{ $log->created_at->diffForHumans() }}</div>
                                </div>
                                <div class="text-right">
                                    @if($log->new_quantity > $log->old_quantity)
                                        <span class="text-success font-bold">+{{ $log->new_quantity - $log->old_quantity }}</span>
                                    @else
                                        <span class="text-danger font-bold">{{ $log->new_quantity - $log->old_quantity }}</span>
                                    @endif
                                    <div class="small">
                                        {{ $log->old_quantity }} <i class="fas fa-arrow-right mx-1"></i> {{ $log->new_quantity }}
                                    </div>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted">No logs recorded properly yet.</li>
                    @endforelse
                </ul>
                <div class="mt-3">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleEdit(rowId) {
        const row = document.getElementById(rowId);
        if (row.style.display === 'none') {
            row.style.display = 'table-row';
        } else {
            row.style.display = 'none';
        }
    }
</script>
@endsection
