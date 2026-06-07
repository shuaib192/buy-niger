{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    View: Admin — Dispute Details — Premium v2.0
--}}
@extends('layouts.app')

@section('title', 'Dispute #' . $dispute->id)
@section('page_title', 'Dispute Details')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@php
    $prefix = request()->is('admin*') ? 'admin.' : 'superadmin.';
@endphp

@push('styles')
<style>
.dsp-hero {
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    border-radius: 18px;
    padding: 24px 32px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
}
.dsp-hero-content h2 {
    color: white; font-size: 1.35rem; font-weight: 800;
    font-family: 'Outfit', sans-serif; margin: 0 0 4px 0;
}
.dsp-hero-content p { color: rgba(255,255,255,.6); font-size: .85rem; margin: 0; }

.status-tag {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 14px; border-radius: 20px; font-size: .75rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .02em;
}
.status-tag.open { background: rgba(59,130,246,.15); color: #60a5fa; border: 1px solid rgba(59,130,246,.25); }
.status-tag.in-progress { background: rgba(6,182,212,.15); color: #22d3ee; border: 1px solid rgba(6,182,212,.25); }
.status-tag.resolved { background: rgba(16,185,129,.15); color: #34d399; border: 1px solid rgba(16,185,129,.25); }
.status-tag.closed { background: rgba(156,163,175,.15); color: #9ca3af; border: 1px solid rgba(156,163,175,.25); }
.status-tag.escalated { background: rgba(239,68,68,.15); color: #f87171; border: 1px solid rgba(239,68,68,.25); }

.dsp-grid {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 20px;
    align-items: start;
}
@media(max-width: 991px) { .dsp-grid { grid-template-columns: 1fr; } }

/* Cards & Layout */
.dsp-card {
    background: var(--surface);
    border: 1.5px solid var(--border-color);
    border-radius: 20px; overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.01);
    margin-bottom: 20px;
}
.dsp-card-header {
    padding: 16px 24px;
    border-bottom: 1px solid var(--border-color);
    display: flex; align-items: center; gap: 10px;
}
.dsp-card-title {
    font-size: .875rem; font-weight: 800; color: var(--text-primary);
    font-family: 'Outfit', sans-serif; text-transform: uppercase; letter-spacing: .03em;
}
.dsp-card-body { padding: 24px; }

/* Complaint Block */
.complaint-box {
    background: rgba(239,68,68,.02);
    border: 1.5px dashed rgba(239,68,68,.15);
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 24px;
}

/* Chat bubble styling */
.chat-thread {
    display: flex; flex-direction: column; gap: 16px;
    max-height: 480px; overflow-y: auto; padding-right: 8px;
}
.chat-bubble-wrap {
    display: flex; gap: 12px; max-width: 85%;
}
.chat-bubble-wrap.admin-sender {
    margin-left: auto; flex-direction: row-reverse;
}
.chat-avatar {
    width: 36px; height: 36px; border-radius: 50%; object-fit: cover; flex-shrink: 0;
}
.chat-content {
    background: var(--bg-surface);
    border: 1.5px solid var(--border-color);
    border-radius: 16px; padding: 14px 16px;
    position: relative;
}
.admin-sender .chat-content {
    background: rgba(99, 102, 241, 0.05);
    border-color: rgba(99, 102, 241, 0.15);
}
.chat-meta {
    display: flex; align-items: center; gap: 8px; margin-bottom: 5px;
}
.chat-sender-name { font-size: .8rem; font-weight: 700; color: var(--text-primary); }
.chat-time { font-size: .7rem; color: var(--text-muted); }
.chat-role-badge {
    font-size: .65rem; font-weight: 700; padding: 1px 6px; border-radius: 4px;
    text-transform: uppercase;
}
.chat-role-badge.admin { background: rgba(99, 102, 241, 0.1); color: #4f46e5; }
.chat-role-badge.customer { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

.chat-msg {
    font-size: .8125rem; color: var(--text-secondary); line-height: 1.6; white-space: pre-wrap; margin: 0;
}

/* Form Styling */
.form-input {
    width: 100%; padding: 11px 14px;
    border: 1.5px solid var(--border-color);
    border-radius: 12px; font-size: .875rem;
    color: var(--text-primary); background: white;
    transition: all .15s; box-sizing: border-box;
}
.form-input:focus { outline: none; border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(99,102,241,.08); }

.form-select-custom {
    width: 100%; padding: 10px 14px;
    border: 1.5px solid var(--border-color);
    border-radius: 12px; font-size: .875rem;
    color: var(--text-primary); background: white;
    cursor: pointer; transition: all .15s;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    padding-right: 36px;
    box-sizing: border-box;
}
.form-select-custom:focus { outline: none; border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(99,102,241,.08); }

.btn-send-reply {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 10px 20px; border-radius: 10px; font-size: .82rem;
    font-weight: 700; background: linear-gradient(135deg, #4f46e5, #6366f1);
    color: white; border: none; cursor: pointer; transition: all .2s;
}
.btn-send-reply:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(99,102,241,.25); }

/* Context Info Blocks */
.context-avatar-card {
    display: flex; align-items: center; gap: 12px;
}
.context-avatar {
    width: 42px; height: 42px; border-radius: 10px; flex-shrink: 0; background: #e2e8f0;
    display: flex; align-items: center; justify-content: center; font-weight: 700; color: #475569;
}
.context-name { font-size: .85rem; font-weight: 700; color: var(--text-primary); font-family: 'Outfit', sans-serif; }
.context-desc { font-size: .72rem; color: var(--text-muted); margin-top: 1px; }

.order-item-row {
    display: flex; align-items: center; gap: 10px; padding: 10px;
    background: var(--bg-surface); border-radius: 10px; margin-bottom: 8px;
    border: 1px solid var(--border-color);
}
.order-item-img {
    width: 38px; height: 38px; border-radius: 6px; object-fit: cover;
}
.order-item-icon-placeholder {
    width: 38px; height: 38px; border-radius: 6px; background: rgba(99,102,241,0.05);
    display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-size: .8rem;
}
</style>
@endpush

@section('content')
<div class="container-fluid" style="padding: 0;">
    {{-- Breadcrumb --}}
    <div class="mb-4">
        <a href="{{ route($prefix.'disputes') }}" class="text-decoration-none text-muted small" style="font-weight: 600;">
            <i class="fas fa-arrow-left me-1"></i> Back to Disputes
        </a>
    </div>

    {{-- Hero --}}
    <div class="dsp-hero">
        <div class="dsp-hero-content">
            <h2>Dispute #{{ $dispute->id }}: {{ $dispute->subject }}</h2>
            <p>Raised on {{ $dispute->created_at->format('M d, Y \a\t h:i A') }}</p>
        </div>
        <div>
            @if($dispute->status == 'open') <span class="status-tag open"><i class="fas fa-envelope-open mr-1"></i> Open</span>
            @elseif($dispute->status == 'in_progress') <span class="status-tag in-progress"><i class="fas fa-spinner fa-spin mr-1"></i> In Progress</span>
            @elseif($dispute->status == 'resolved') <span class="status-tag resolved"><i class="fas fa-check-circle mr-1"></i> Resolved</span>
            @elseif($dispute->status == 'closed') <span class="status-tag closed"><i class="fas fa-lock mr-1"></i> Closed</span>
            @else <span class="status-tag escalated"><i class="fas fa-triangle-exclamation mr-1"></i> Escalated</span>
            @endif
        </div>
    </div>

    {{-- Layout Grid --}}
    <div class="dsp-grid">
        
        {{-- Left: Conversation & original ticket --}}
        <div>
            {{-- Ticket Complaint --}}
            <div class="dsp-card">
                <div class="dsp-card-header" style="background: rgba(239, 68, 68, 0.03);">
                    <i class="fas fa-triangle-exclamation" style="color:#ef4444;"></i>
                    <div class="dsp-card-title" style="color:#ef4444;">Customer Complaint</div>
                </div>
                <div class="dsp-card-body">
                    <div class="complaint-box">
                        <div class="d-flex align-items-start gap-3">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($dispute->user->name ?? 'U') }}&background=ef4444&color=fff" class="chat-avatar">
                            <div>
                                <div class="chat-meta">
                                    <span class="chat-sender-name">{{ $dispute->user->name ?? 'Unknown Customer' }}</span>
                                    <span class="chat-time">{{ $dispute->created_at->format('M d, Y h:i A') }}</span>
                                    @if($dispute->priority == 'critical')
                                        <span class="badge bg-danger rounded-pill" style="font-size: 10px;">CRITICAL</span>
                                    @elseif($dispute->priority == 'high')
                                        <span class="badge bg-warning text-dark rounded-pill" style="font-size: 10px;">HIGH</span>
                                    @endif
                                </div>
                                <p class="chat-msg" style="font-size: .875rem; color: var(--text-primary);">{{ $dispute->description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Thread replies --}}
            <div class="dsp-card">
                <div class="dsp-card-header">
                    <i class="fas fa-comments" style="color: #4f46e5;"></i>
                    <div class="dsp-card-title">Conversation Thread</div>
                </div>
                <div class="dsp-card-body">
                    <div class="chat-thread">
                        @forelse($dispute->messages as $msg)
                            @php $isAdmin = $msg->is_admin; @endphp
                            <div class="chat-bubble-wrap {{ $isAdmin ? 'admin-sender' : '' }}">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($msg->user->name ?? 'U') }}&background={{ $isAdmin ? '6366f1' : 'ef4444' }}&color=fff" class="chat-avatar">
                                <div class="chat-content">
                                    <div class="chat-meta">
                                        <span class="chat-sender-name">{{ $msg->user->name ?? 'Unknown' }}</span>
                                        <span class="chat-role-badge {{ $isAdmin ? 'admin' : 'customer' }}">
                                            {{ $isAdmin ? 'Admin' : 'Customer' }}
                                        </span>
                                        <span class="chat-time">{{ $msg->created_at->format('M d, h:i A') }}</span>
                                    </div>
                                    <p class="chat-msg">{{ $msg->message }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-comment-slash fa-2x mb-3 d-block" style="opacity: .3;"></i>
                                <span style="font-size:.85rem;">No replies sent yet. Respond to the customer below.</span>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Form response --}}
            @if(!in_array($dispute->status, ['closed', 'resolved']))
                <div class="dsp-card">
                    <div class="dsp-card-header" style="background: rgba(99, 102, 241, 0.03);">
                        <i class="fas fa-reply" style="color: #4f46e5;"></i>
                        <div class="dsp-card-title">Send Response</div>
                    </div>
                    <div class="dsp-card-body">
                        <form action="{{ route($prefix.'disputes.message', $dispute->id) }}" method="POST">
                            @csrf
                            <div class="custom-form-group">
                                <textarea name="message" class="form-input" rows="4" placeholder="Type your response to the customer here..." required></textarea>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn-send-reply">
                                    <i class="fas fa-paper-plane"></i> Send Reply
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="alert alert-light text-center border-0 py-4" style="border-radius:16px;background:var(--bg-surface);">
                    <i class="fas fa-lock text-muted me-2"></i> This dispute is <strong>{{ strtoupper($dispute->status) }}</strong>. Thread is locked.
                </div>
            @endif
        </div>

        {{-- Right Column: Side info context cards --}}
        <div>
            {{-- Order details --}}
            @if($dispute->order)
                <div class="dsp-card">
                    <div class="dsp-card-header">
                        <i class="fas fa-shopping-bag" style="color:#0284c7;"></i>
                        <div class="dsp-card-title">Order Context</div>
                    </div>
                    <div class="dsp-card-body">
                        <div style="font-size: .8125rem; display: flex; flex-direction: column; gap: 8px;">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Order ID:</span>
                                <a href="{{ route($prefix.'orders.show', $dispute->order->id) }}" style="font-weight:700;text-decoration:none;">#{{ $dispute->order->order_number }}</a>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Order Status:</span>
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-warning text-dark', 'paid' => 'bg-info text-dark', 'processing' => 'bg-info text-dark',
                                        'shipped' => 'bg-primary text-white', 'delivered' => 'bg-success text-white',
                                        'cancelled' => 'bg-danger text-white', 'refunded' => 'bg-secondary text-white'
                                    ];
                                    $oBadge = $statusColors[$dispute->order->status] ?? 'bg-secondary text-white';
                                @endphp
                                <span class="badge {{ $oBadge }} rounded-pill" style="font-size:10px;text-transform:uppercase;">{{ $dispute->order->status }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Order Total:</span>
                                <span style="font-weight:700;color:var(--text-primary);">₦{{ number_format($dispute->order->total, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Order Date:</span>
                                <span class="text-secondary">{{ $dispute->order->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>

                        <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 16px 0;">
                        <h6 style="font-size: .75rem; font-weight: 700; text-transform: uppercase; color: var(--text-secondary); margin-bottom: 10px;">Items</h6>
                        
                        @foreach($dispute->order->items as $item)
                            <div class="order-item-row">
                                @if($item->product && $item->product->primary_image_url)
                                    <img src="{{ $item->product->primary_image_url }}" class="order-item-img">
                                @else
                                    <div class="order-item-icon-placeholder"><i class="fas fa-box"></i></div>
                                @endif
                                <div style="flex:1;min-width:0;">
                                    <div style="font-size: .8rem; font-weight:700; color:var(--text-primary);" class="text-truncate">{{ $item->product_name }}</div>
                                    <div style="font-size: .72rem; color:var(--text-muted);">{{ $item->quantity }}x ₦{{ number_format($item->price) }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Customer Details --}}
            <div class="dsp-card">
                <div class="dsp-card-header">
                    <i class="fas fa-user" style="color:#4f46e5;"></i>
                    <div class="dsp-card-title">Customer Info</div>
                </div>
                <div class="dsp-card-body">
                    <div class="context-avatar-card">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($dispute->user->name ?? 'U') }}&background=6366f1&color=fff" style="width:42px;height:42px;border-radius:10px;">
                        <div>
                            <div class="context-name">{{ $dispute->user->name ?? 'Unknown Customer' }}</div>
                            <div class="context-desc">{{ $dispute->user->email ?? 'No email associated' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Vendors Involved --}}
            @if($vendors->count())
                <div class="dsp-card">
                    <div class="dsp-card-header">
                        <i class="fas fa-store" style="color:#059669;"></i>
                        <div class="dsp-card-title">Vendors Involved</div>
                    </div>
                    <div class="dsp-card-body">
                        @foreach($vendors as $vendor)
                            <div class="context-avatar-card" style="margin-bottom:12px;">
                                <div class="context-avatar">{{ strtoupper(substr($vendor->store_name, 0, 1)) }}</div>
                                <div>
                                    <div class="context-name">{{ $vendor->store_name }}</div>
                                    <div class="context-desc">{{ $vendor->user->email ?? '' }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Resolution controls --}}
            <div class="dsp-card" style="border-color: #334155;">
                <div class="dsp-card-header" style="background:#1e293b; color: white;">
                    <i class="fas fa-gavel"></i>
                    <div class="dsp-card-title" style="color:white;">Dispute Resolution</div>
                </div>
                <div class="dsp-card-body">
                    <form action="{{ route($prefix.'disputes.update', $dispute->id) }}" method="POST">
                        @csrf
                        <div class="custom-form-group">
                            <label class="custom-label">Dispute Status</label>
                            <select name="status" class="form-select-custom">
                                <option value="open" {{ $dispute->status == 'open' ? 'selected' : '' }}>Open</option>
                                <option value="in_progress" {{ $dispute->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="escalated" {{ $dispute->status == 'escalated' ? 'selected' : '' }}>Escalated</option>
                                <option value="resolved" {{ $dispute->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ $dispute->status == 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                        <div class="custom-form-group">
                            <label class="custom-label">Resolution Notes</label>
                            <textarea name="resolution_notes" class="form-input" rows="3" placeholder="Log private notes or final resolution explanation..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-dark w-100 rounded-pill btn-sm py-2 fw-bold" style="background:#1e293b;border:none;">
                            <i class="fas fa-circle-check me-1"></i> Update Case Status
                        </button>
                    </form>
                </div>
            </div>

            {{-- Past resolution log --}}
            @if($dispute->resolution_notes)
                <div class="dsp-card">
                    <div class="dsp-card-header">
                        <i class="fas fa-history" style="color:#64748b;"></i>
                        <div class="dsp-card-title">Resolution Log</div>
                    </div>
                    <div class="dsp-card-body" style="padding: 16px 20px;">
                        <pre style="white-space:pre-wrap;font-family:inherit;font-size:.78rem;background:var(--bg-surface);padding:12px;border-radius:10px;border:1px solid var(--border-color);margin:0;line-height:1.5;color:var(--text-secondary);">{{ $dispute->resolution_notes }}</pre>
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
