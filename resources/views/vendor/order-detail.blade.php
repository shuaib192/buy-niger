{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    View: Vendor Order Detail
--}}
@extends('layouts.app')

@section('title', 'Order #' . $orderItem->order->order_number)
@section('page_title', 'Order Detail')

@section('sidebar')
    @include('vendor.partials.sidebar')
@endsection

@section('content')
<div class="row">
    <div class="col-md-9">
        <div class="dashboard-card mb-4">
            <div class="dashboard-card-header d-flex justify-content-between align-items-center">
                <h3>Order #{{ $orderItem->order->order_number }}</h3>
                <div class="no-print">
                    <button onclick="window.print()" class="btn btn-sm btn-outline-secondary mr-2">
                        <i class="fas fa-print mr-1"></i> Print Invoice
                    </button>
                    <span class="badge badge-{{ $orderItem->status == 'delivered' ? 'success' : ($orderItem->status == 'cancelled' ? 'danger' : 'warning') }}-soft text-{{ $orderItem->status == 'delivered' ? 'success' : ($orderItem->status == 'cancelled' ? 'danger' : 'warning') }} px-3 py-2">
                        {{ strtoupper($orderItem->status) }}
                    </span>
                </div>
            </div>
            <div class="dashboard-card-body">
                <div class="order-workflow mb-5 no-print">
                    @php
                        $steps = ['pending', 'processing', 'shipped', 'delivered'];
                        $currentStep = array_search($orderItem->status, $steps);
                    @endphp
                    <div class="workflow-steps">
                        @foreach($steps as $index => $step)
                            <div class="workflow-step {{ $index <= $currentStep ? 'active' : '' }}">
                                <div class="step-icon">
                                    @if($index < $currentStep || ($orderItem->status == 'delivered'))
                                        <i class="fas fa-check"></i>
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </div>
                                <div class="step-label">{{ ucfirst($step) }}</div>
                                @if(!$loop->last)
                                    <div class="step-line"></div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-borderless">
                        <thead>
                            <tr class="text-secondary-500 font-bold border-bottom">
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="py-4">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $orderItem->product->primary_image_url }}" alt="" class="rounded mr-3 shadow-sm" style="width: 60px; height: 60px; object-fit: cover;">
                                        <div>
                                            <div class="font-bold text-secondary-900">{{ $orderItem->product_name }}</div>
                                            <small class="text-muted">SKU: {{ $orderItem->product->sku ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 align-middle">{{ $orderItem->quantity }}</td>
                                <td class="py-4 align-middle">₦{{ number_format($orderItem->price) }}</td>
                                <td class="py-4 align-middle text-right font-bold">₦{{ number_format($orderItem->subtotal) }}</td>
                            </tr>
                        </tbody>
                        <tfoot class="border-top">
                            <tr>
                                <td colspan="3" class="text-right py-3 text-secondary-500">Subtotal</td>
                                <td class="text-right py-3 font-bold">₦{{ number_format($orderItem->subtotal) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-right py-3 text-secondary-500">Shipping</td>
                                <td class="text-right py-3 font-bold">₦0.00</td>
                            </tr>
                            <tr class="h4">
                                <td colspan="3" class="text-right py-3 font-bold">Total</td>
                                <td class="text-right py-3 font-bold text-primary">₦{{ number_format($orderItem->subtotal) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="row mt-5">
                    <div class="col-md-6">
                        <h4 class="mb-3"><i class="fas fa-shipping-fast mr-2 text-primary"></i> Shipping Address</h4>
                        @if($orderItem->order->address)
                            <div class="p-4 bg-light rounded-lg border">
                                <strong class="text-secondary-900 d-block mb-2">{{ $orderItem->order->address->first_name }} {{ $orderItem->order->address->last_name }}</strong>
                                <p class="text-muted mb-1">{{ $orderItem->order->address->address_line1 }}</p>
                                @if($orderItem->order->address->address_line2)
                                    <p class="text-muted mb-1">{{ $orderItem->order->address->address_line2 }}</p>
                                @endif
                                <p class="text-muted mb-1">{{ $orderItem->order->address->city }}, {{ $orderItem->order->address->state }}</p>
                                <p class="text-muted mb-1">{{ $orderItem->order->address->zip_code }}</p>
                                <p class="text-muted mt-3"><i class="fas fa-phone-alt mr-2"></i> {{ $orderItem->order->address->phone }}</p>
                            </div>
                        @else
                            <div class="p-4 bg-light rounded text-center text-muted">No address provided.</div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <h4 class="mb-3"><i class="fas fa-info-circle mr-2 text-primary"></i> Other Information</h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between px-0 py-3">
                                <span class="text-secondary-500">Payment Method</span>
                                <span class="font-bold text-secondary-900">{{ strtoupper($orderItem->order->payment_method ?? 'Not Set') }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0 py-3">
                                <span class="text-secondary-500">Payment Status</span>
                                <span class="badge badge-{{ ($orderItem->order->payment_status ?? 'pending') == 'paid' ? 'success' : 'warning' }}-soft text-{{ ($orderItem->order->payment_status ?? 'pending') == 'paid' ? 'success' : 'warning' }}">
                                    {{ strtoupper($orderItem->order->payment_status ?? 'pending') }}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0 py-3">
                                <span class="text-secondary-500">Tracking Number</span>
                                <span class="font-bold text-secondary-900">{{ $orderItem->tracking_number ?: 'Not Shipped Yet' }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 no-print">
        <div class="dashboard-card mb-4 border-primary-100">
            <div class="dashboard-card-header bg-primary-50">
                <h3>Manage Order</h3>
            </div>
            <div class="dashboard-card-body">
                <form action="{{ route('vendor.orders.status', $orderItem->id) }}" method="POST">
                    @csrf
                    <div class="form-group mb-4">
                        <label class="form-label text-xs font-bold uppercase text-secondary-500">Update Status</label>
                        <select name="status" class="form-control" onchange="toggleTracking(this.value)">
                            <option value="pending" {{ $orderItem->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ $orderItem->status == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="shipped" {{ $orderItem->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ $orderItem->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ $orderItem->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div id="tracking-field" class="form-group mb-4 {{ $orderItem->status == 'shipped' ? '' : 'd-none' }}">
                        <label class="form-label text-xs font-bold uppercase text-secondary-500">Tracking #</label>
                        <input type="text" name="tracking_number" class="form-control" value="{{ $orderItem->tracking_number }}" placeholder="e.g. DHL-X892">
                    </div>

                    <button type="submit" class="btn btn-primary btn-full py-3 shadow-sm">
                        Apply Update
                    </button>
                </form>
            </div>
        </div>

        <div class="dashboard-card border-secondary-200">
            <div class="dashboard-card-header">
                <h3>Customer</h3>
            </div>
            <div class="dashboard-card-body">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-secondary-100 rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 48px; height: 48px;">
                        <i class="fas fa-user text-secondary-500"></i>
                    </div>
                    <div>
                        <div class="font-bold text-secondary-900">{{ $orderItem->order->user->name ?? 'Guest User' }}</div>
                        <small class="text-muted">Customer ID: #{{ $orderItem->order->user_id ?? 'N/A' }}</small>
                    </div>
                </div>
                <div class="d-flex flex-column gap-2">
                    <a href="mailto:{{ $orderItem->order->user->email ?? '' }}" class="btn btn-outline-secondary btn-full text-left">
                        <i class="fas fa-envelope mr-3 text-primary"></i> Send Email
                    </a>
                    @if($orderItem->order->address && $orderItem->order->address->phone)
                        <a href="tel:{{ $orderItem->order->address->phone }}" class="btn btn-outline-secondary btn-full text-left">
                            <i class="fas fa-phone-alt mr-3 text-primary"></i> Call Customer
                        </a>
                    @endif
                    <a href="{{ route('vendor.messages.index') }}" class="btn btn-outline-primary btn-full text-left">
                        <i class="fas fa-comments mr-3"></i> Message Buyer
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .font-bold { font-weight: 700; }
    .text-secondary-900 { color: #0f172a; }
    .text-secondary-500 { color: #64748b; }
    .badge-success-soft { background: #ecfdf5; color: #059669; }
    .badge-warning-soft { background: #fffbeb; color: #d97706; }
    .badge-danger-soft { background: #fef2f2; color: #dc2626; }
    .badge-primary-soft { background: #eff6ff; color: #2563eb; }
    .badge-info-soft { background: #f0f9ff; color: #0284c7; }
    .uppercase { text-transform: uppercase; }
    .text-xs { font-size: 0.7rem; }

    .order-workflow {
        padding: 20px 0;
    }
    .workflow-steps {
        display: flex;
        justify-content: space-between;
        position: relative;
    }
    .workflow-step {
        flex: 1;
        text-align: center;
        position: relative;
        z-index: 1;
    }
    .step-icon {
        width: 40px;
        height: 40px;
        background: #e2e8f0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        font-weight: bold;
        color: #64748b;
        transition: all 0.3s;
    }
    .workflow-step.active .step-icon {
        background: #0066FF;
        color: white;
        box-shadow: 0 0 0 5px rgba(0, 102, 255, 0.1);
    }
    .step-label {
        font-size: 12px;
        font-weight: 600;
        color: #94a3b8;
    }
    .workflow-step.active .step-label {
        color: #0066FF;
    }
    .step-line {
        position: absolute;
        top: 20px;
        left: 50%;
        width: 100%;
        height: 3px;
        background: #e2e8f0;
        z-index: -1;
    }
    .workflow-step.active .step-line {
        background: #0066FF;
    }

    @media print {
        .no-print { display: none !important; }
        .dashboard-card { border: none !important; box-shadow: none !important; }
    }
</style>

<script>
    function toggleTracking(status) {
        const field = document.getElementById('tracking-field');
        if (status === 'shipped') {
            field.classList.remove('d-none');
        } else {
            field.classList.add('d-none');
        }
    }
</script>
@endsection
