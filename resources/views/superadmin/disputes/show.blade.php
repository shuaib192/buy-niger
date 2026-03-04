@extends('layouts.app')

@section('title', 'Dispute #' . $dispute->id)
@section('page_title', 'Dispute #' . $dispute->id)

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@php
    $prefix = request()->is('admin*') ? 'admin.' : 'superadmin.';
@endphp

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <a href="{{ route($prefix.'disputes') }}" class="text-decoration-none text-muted small">
                <i class="fas fa-arrow-left"></i> Back to Disputes
            </a>
            <h1 class="h3 mb-0 mt-1">Dispute #{{ $dispute->id }}: {{ $dispute->subject }}</h1>
        </div>
        <div class="d-flex gap-2 mt-2 mt-sm-0">
            @if($dispute->status == 'open') <span class="badge badge-primary px-3 py-2">OPEN</span>
            @elseif($dispute->status == 'in_progress') <span class="badge badge-info px-3 py-2">IN PROGRESS</span>
            @elseif($dispute->status == 'resolved') <span class="badge badge-success px-3 py-2">RESOLVED</span>
            @elseif($dispute->status == 'closed') <span class="badge badge-secondary px-3 py-2">CLOSED</span>
            @else <span class="badge badge-danger px-3 py-2">ESCALATED</span>
            @endif
        </div>
    </div>

    <div class="row">
        {{-- Left Column: Conversation Thread --}}
        <div class="col-lg-8">
            {{-- Customer's Original Complaint --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-danger text-white">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-exclamation-triangle mr-1"></i> Customer Complaint</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-start gap-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($dispute->user->name ?? 'U') }}&background=ef4444&color=fff" class="rounded-circle" width="40" height="40">
                        <div>
                            <strong>{{ $dispute->user->name ?? 'Unknown Customer' }}</strong>
                            <span class="text-muted small ml-2">{{ $dispute->created_at->format('M d, Y h:i A') }}</span>
                            @if($dispute->priority == 'critical')
                                <span class="badge badge-danger ml-2">CRITICAL</span>
                            @elseif($dispute->priority == 'high')
                                <span class="badge badge-warning ml-2">HIGH</span>
                            @endif
                            <p class="mt-2 mb-0" style="line-height: 1.7; white-space: pre-wrap;">{{ $dispute->description }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Conversation Thread --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-comments mr-1"></i> Conversation Thread</h6>
                </div>
                <div class="card-body">
                    @forelse($dispute->messages as $msg)
                        <div class="d-flex mb-4 {{ $msg->is_admin ? 'flex-row-reverse' : '' }}">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($msg->user->name ?? 'U') }}&background={{ $msg->is_admin ? '3b82f6' : 'ef4444' }}&color=fff" class="rounded-circle mx-2" width="36" height="36">
                            <div style="max-width:75%; background:{{ $msg->is_admin ? '#eff6ff' : '#fef2f2' }}; border-radius:12px; padding:12px 16px;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong class="small">{{ $msg->user->name ?? 'Unknown' }}</strong>
                                    <span class="text-muted" style="font-size:11px;">{{ $msg->created_at->format('M d, h:i A') }}</span>
                                </div>
                                @if($msg->is_admin)
                                    <span class="badge badge-primary" style="font-size:9px;">ADMIN</span>
                                @else
                                    <span class="badge badge-danger" style="font-size:9px;">CUSTOMER</span>
                                @endif
                                <p class="mb-0 mt-1" style="white-space: pre-wrap;">{{ $msg->message }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-comment-slash fa-2x mb-3 d-block"></i>
                            No messages yet. Send a response below.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Admin Response Form --}}
            @if(!in_array($dispute->status, ['closed', 'resolved']))
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-reply mr-1"></i> Send Response</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route($prefix.'disputes.message', $dispute->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <textarea name="message" class="form-control" rows="4" placeholder="Type your response to the customer..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary mt-2"><i class="fas fa-paper-plane mr-1"></i> Send Response</button>
                    </form>
                </div>
            </div>
            @else
            <div class="alert alert-secondary text-center">
                <i class="fas fa-lock mr-1"></i> This dispute is <strong>{{ strtoupper($dispute->status) }}</strong>. No further responses can be sent.
            </div>
            @endif
        </div>

        {{-- Right Column: Context & Actions --}}
        <div class="col-lg-4">
            {{-- Order Context --}}
            @if($dispute->order)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-shopping-bag mr-1"></i> Order Details</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>Order:</strong>
                        <a href="{{ route($prefix.'orders.show', $dispute->order->id) }}">#{{ $dispute->order->order_number }}</a>
                    </div>
                    <div class="mb-2">
                        <strong>Status:</strong>
                        @php
                            $statusColors = [
                                'pending' => 'warning', 'paid' => 'info', 'processing' => 'info',
                                'shipped' => 'primary', 'delivered' => 'success',
                                'cancelled' => 'danger', 'refunded' => 'secondary'
                            ];
                            $sBadge = $statusColors[$dispute->order->status] ?? 'secondary';
                        @endphp
                        <span class="badge badge-{{ $sBadge }}">{{ strtoupper($dispute->order->status) }}</span>
                    </div>
                    <div class="mb-2">
                        <strong>Total:</strong> ₦{{ number_format($dispute->order->total, 2) }}
                    </div>
                    <div class="mb-3">
                        <strong>Date:</strong> {{ $dispute->order->created_at->format('M d, Y') }}
                    </div>

                    <h6 class="small font-weight-bold text-muted mt-3 mb-2">ITEMS IN ORDER</h6>
                    @foreach($dispute->order->items as $item)
                    <div class="d-flex align-items-center gap-2 mb-2 p-2" style="background:#f8fafc;border-radius:8px;">
                        @if($item->product && $item->product->primary_image_url)
                            <img src="{{ $item->product->primary_image_url }}" style="width:36px;height:36px;object-fit:cover;border-radius:6px;">
                        @else
                            <div style="width:36px;height:36px;background:#e2e8f0;border-radius:6px;display:flex;align-items:center;justify-content:center;"><i class="fas fa-box text-muted"></i></div>
                        @endif
                        <div style="flex:1;min-width:0;">
                            <div class="small font-weight-bold text-truncate">{{ $item->product_name }}</div>
                            <div style="font-size:11px;color:#64748b;">{{ $item->quantity }}x ₦{{ number_format($item->price) }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Customer Info --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user mr-1"></i> Customer</h6>
                </div>
                <div class="card-body text-center">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($dispute->user->name ?? 'U') }}&background=random" class="rounded-circle mb-2" width="50" height="50">
                    <h6>{{ $dispute->user->name ?? 'Unknown' }}</h6>
                    <p class="text-muted small mb-0">{{ $dispute->user->email ?? '' }}</p>
                </div>
            </div>

            {{-- Vendors Involved --}}
            @if($vendors->count())
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-store mr-1"></i> Vendor(s) Involved</h6>
                </div>
                <div class="card-body">
                    @foreach($vendors as $vendor)
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($vendor->store_name) }}&background=random" class="rounded-circle" width="32" height="32">
                        <div>
                            <strong class="small">{{ $vendor->store_name }}</strong>
                            <div style="font-size:11px;color:#64748b;">{{ $vendor->user->email ?? '' }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Resolution Actions --}}
            <div class="card shadow mb-4 border-left-dark">
                <div class="card-header py-3 bg-dark text-white">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-gavel mr-1"></i> Resolution Actions</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route($prefix.'disputes.update', $dispute->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="small font-weight-bold">Change Status</label>
                            <select name="status" class="form-control form-control-sm">
                                <option value="open" {{ $dispute->status == 'open' ? 'selected' : '' }}>Open</option>
                                <option value="in_progress" {{ $dispute->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="escalated" {{ $dispute->status == 'escalated' ? 'selected' : '' }}>Escalated</option>
                                <option value="resolved" {{ $dispute->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ $dispute->status == 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="small font-weight-bold">Resolution Note (Optional)</label>
                            <textarea name="resolution_notes" class="form-control form-control-sm" rows="3" placeholder="Explain the resolution or action taken..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-dark btn-block btn-sm"><i class="fas fa-check mr-1"></i> Update Status</button>
                    </form>
                </div>
            </div>

            {{-- Dispute Timeline --}}
            @if($dispute->resolution_notes)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history mr-1"></i> Resolution Log</h6>
                </div>
                <div class="card-body">
                    <pre class="small mb-0" style="white-space:pre-wrap;font-family:inherit;background:#f8fafc;padding:12px;border-radius:8px;">{{ $dispute->resolution_notes }}</pre>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
